# REVIEWER-FIXES.md — Patch ao reviewer + Fixes reais

Lê `CLAUDE.md`, `REFERENCES/SEO.md` e `app/Console/Commands/ClinkyReview.php` antes de começar.

Este prompt faz duas coisas em ordem:
1. **PARTE A** — Patcha o reviewer para eliminar falsos positivos
2. **PARTE B** — Corrige os problemas reais nos mini-sites

Executa a PARTE A primeiro, corre `php artisan clinky:review` para confirmar que os falsos positivos desapareceram, e só depois executa a PARTE B.

---

## PARTE A — Patch ao reviewer

### A.1 — `heading_grande` mais tolerante + skip list

Em `app/Console/Commands/ClinkyReview.php`, substitui o bloco do check do heading por:

```php
// 5. Tipografia — o h1/h2 deve ser grande OU o site tem design headless legítimo
$semHeadingPorDesign = ['botao', 'proibido', 'nada']; // design intencional sem h1/h2

if (in_array($slug, $semHeadingPorDesign, true)) {
    $checks['heading_grande'] = true; // skip — é por design
} else {
    // Procura o primeiro heading em qualquer nível
    $headings = $crawler->filter('h1, h2, h3');
    $encontrouGrande = false;
    $classesExaminadas = [];

    $headings->each(function ($node) use (&$encontrouGrande, &$classesExaminadas) {
        $classes = $node->attr('class') ?? '';
        $classesExaminadas[] = $classes;
        // Aceita text-3xl em mobile (é legítimo) até text-9xl
        // Também aceita classes Tailwind arbitrárias tipo text-[...]
        if (Str::contains($classes, ['text-3xl', 'text-4xl', 'text-5xl', 'text-6xl', 'text-7xl', 'text-8xl', 'text-9xl'])
            || preg_match('/text-\[\d+(?:\.\d+)?(?:rem|px|em|vw)\]/', $classes)
            || preg_match('/text-\[clamp\(/', $classes)) {
            $encontrouGrande = true;
        }
    });

    $checks['heading_grande'] = $encontrouGrande;
    if (!$encontrouGrande && $headings->count() > 0) {
        $avisos[] = "Nenhum heading com text-3xl+ ou arbitrário (classes vistas: " . implode(' | ', $classesExaminadas) . ")";
    } elseif ($headings->count() === 0) {
        $avisos[] = "Sem h1/h2/h3. Se for por design, adicionar slug a \$semHeadingPorDesign.";
    }
}
```

### A.2 — `tem_padding_mobile` varrer todos os elementos

Substitui o bloco das margens por:

```php
// 6. Margens mobile-first — varrer TODOS os elementos com classe, não só containers
$html_classes = '';
$crawler->filter('[class]')->each(function ($node) use (&$html_classes) {
    $html_classes .= ' ' . ($node->attr('class') ?? '');
});

$checks['tem_padding_mobile'] = Str::contains($html_classes, [
    'px-3', 'px-4', 'px-5', 'px-6', 'px-8',
    'p-3', 'p-4', 'p-6', 'p-8', 'p-12',
    'pl-4', 'pr-4', 'pl-6', 'pr-6',
]);
$checks['tem_max_width'] = Str::contains($html_classes, [
    'max-w-', 'container', 'w-full',
]);

if (!$checks['tem_padding_mobile']) {
    $avisos[] = "Nenhum padding horizontal (px-3/4/5/6/8) em nenhum elemento";
}
```

### A.3 — `tem_alpine` skip no hub

Substitui o check do Alpine por:

```php
// 9. Alpine.js — não exigido na homepage (é estática)
if ($slug !== '') {
    $checks['tem_alpine'] = Str::contains($html, ['x-data', 'Alpine.']);
}
```

### A.4 — `acao_responde` detecta falta de API key

Substitui o método `testarAcao()` por:

```php
protected function testarAcao(string $slug): bool|string
{
    [$path, $data] = $this->acaoPost($slug);

    // Sites que dependem de Claude API: só testar se houver key configurada
    $sitesComApi = ['desculpometro', 'nome', 'corporativo', 'decisao', 'oraculo', 'panico', 'ouviste'];
    if (in_array($slug, $sitesComApi, true) && empty(config('services.anthropic.key') ?? env('CLAUDE_API_KEY'))) {
        return 'skip'; // devolve string especial
    }

    try {
        $home = Http::timeout(5)->get($this->baseUrl . '/' . $slug);
        preg_match('/<meta name="csrf-token" content="([^"]+)"/', $home->body(), $m);
        $token = $m[1] ?? '';

        $r = Http::timeout(30)
            ->withHeaders([
                'X-CSRF-TOKEN' => $token,
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->asForm()
            ->post($this->baseUrl . $path, $data);

        return $r->status() === 200 && is_array($r->json());
    } catch (\Throwable $e) {
        return false;
    }
}
```

E na lógica que consome o resultado (dentro de `revSite`, onde se chama `$this->testarAcao`), trata o valor `'skip'`:

```php
if ($site['usa_api'] && !empty($this->acaoPost($slug))) {
    $resultado = $this->testarAcao($slug);
    if ($resultado === 'skip') {
        $avisos[] = 'Teste POST saltado (CLAUDE_API_KEY não configurada em dev)';
    } else {
        $checks['acao_responde'] = $resultado;
    }
}
```

### A.5 — Correr o reviewer novamente

```bash
php artisan clinky:review
```

Confirma que desapareceram:
- Falsos `heading_grande` em Botão e Proibido
- Falsos `tem_padding_mobile` em todos
- Falso `tem_alpine` no hub
- Falsos `acao_responde` nos sites com Claude API

Se algum destes ainda aparecer, mostra-me o output antes de avançares para a PARTE B.

---

## PARTE B — Fixes reais

### B.1 — Gerar as 14 OG images em falta

Cria o comando:

```bash
php artisan make:command GenerateOgImages
```

Ficheiro `app/Console/Commands/GenerateOgImages.php`:

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateOgImages extends Command
{
    protected $signature = 'clinky:og-images {--site= : Gerar apenas um}';
    protected $description = 'Gera as OG images 1200×630 para todos os mini-sites';

    protected array $sites = [
        ['slug' => 'default',        'emoji' => '✦',  'titulo' => 'Clinky.cc',                'sub' => 'Mini-sites virais, inúteis e partilháveis', 'bg' => '#0a0a0a', 'accent' => '#c8f135'],
        ['slug' => 'desculpometro',  'emoji' => '😅', 'titulo' => 'Desculpómetro',            'sub' => 'A desculpa perfeita, gerada por IA',         'bg' => '#1a0800', 'accent' => '#ff6b00'],
        ['slug' => 'botao',          'emoji' => '🔴', 'titulo' => 'Aperta o Botão',           'sub' => 'Um botão. Sem explicação.',                  'bg' => '#0a0a0a', 'accent' => '#c8f135'],
        ['slug' => 'nomeador',       'emoji' => '💬', 'titulo' => 'Nomeador de Grupos',       'sub' => 'Nomes épicos para o teu WhatsApp',           'bg' => '#1a0010', 'accent' => '#ff2d78'],
        ['slug' => 'horoscopo',      'emoji' => '🔮', 'titulo' => 'Horóscopo Inútil',         'sub' => 'As estrelas encolheram os ombros',           'bg' => '#0d0020', 'accent' => '#a855f7'],
        ['slug' => 'nome',           'emoji' => '🧬', 'titulo' => 'Analisador de Nome',       'sub' => 'O que o teu nome diz sobre ti',              'bg' => '#001a18', 'accent' => '#00d9c0'],
        ['slug' => 'bingo',          'emoji' => '🎯', 'titulo' => 'Bingo do Imigrante',       'sub' => 'Quantas já viveste em Portugal?',            'bg' => '#1a1500', 'accent' => '#ffd600'],
        ['slug' => 'conversor',      'emoji' => '🔁', 'titulo' => 'Conversor PT ↔ BR',        'sub' => 'As palavras que nos separam',                'bg' => '#001020', 'accent' => '#00aaff'],
        ['slug' => 'quiz',           'emoji' => '🤔', 'titulo' => 'Sou mais BR ou PT?',       'sub' => 'Quiz de 5 perguntas',                        'bg' => '#0a1500', 'accent' => '#7bc900'],
        ['slug' => 'corporativo',    'emoji' => '💼', 'titulo' => 'Tradutor Corporativo',     'sub' => '"Vamos alinhar" = reunião desnecessária',    'bg' => '#1a0000', 'accent' => '#ff3b3b'],
        ['slug' => 'bolhas',         'emoji' => '🫧', 'titulo' => 'Rebenta as Bolhas',        'sub' => 'Impossível parar',                           'bg' => '#001a2e', 'accent' => '#00d4ff'],
        ['slug' => 'progresso',      'emoji' => '⏳', 'titulo' => 'Progresso da Vida',        'sub' => 'Quanto já passou?',                          'bg' => '#0a0a0a', 'accent' => '#c8f135'],
        ['slug' => 'nada',           'emoji' => '·',  'titulo' => 'Nada.',                    'sub' => '4.832 pessoas já viram',                     'bg' => '#0a0a0a', 'accent' => '#ffffff'],
        ['slug' => 'proibido',       'emoji' => '🚫', 'titulo' => 'O Botão Proibido',         'sub' => 'Não carregues.',                             'bg' => '#1a0000', 'accent' => '#8b0000'],
        ['slug' => 'decisao',        'emoji' => '⚖️', 'titulo' => 'A Decisão Impossível',     'sub' => 'Duas opções. Nenhuma é boa.',                'bg' => '#0d0020', 'accent' => '#a855f7'],
        ['slug' => 'oraculo',        'emoji' => '🔮', 'titulo' => 'O Oráculo',                'sub' => 'Pergunta. A resposta já existe.',            'bg' => '#0d0020', 'accent' => '#a855f7'],
        ['slug' => 'panico',         'emoji' => '🚨', 'titulo' => 'Modo Pânico',              'sub' => 'Activa a crise',                             'bg' => '#1a0000', 'accent' => '#ff3b3b'],
        ['slug' => 'tempo',          'emoji' => '😱', 'titulo' => 'Quanto Tempo Perdeste?',   'sub' => 'A calculadora do arrependimento',            'bg' => '#1a1000', 'accent' => '#f59e0b'],
        ['slug' => 'lista',          'emoji' => '📝', 'titulo' => 'Coisas Que Nunca Vais Fazer','sub' => 'A bucket list honesta',                   'bg' => '#1a0800', 'accent' => '#ff6b00'],
        ['slug' => 'conquistas',     'emoji' => '🏆', 'titulo' => 'Conquistas do Nada',       'sub' => 'Medalhas por não fazer nada',                'bg' => '#1a1500', 'accent' => '#ffd600'],
        ['slug' => 'ouviste',        'emoji' => '🎧', 'titulo' => 'Já Ouviste Isto?',         'sub' => 'Sons que nunca ouviste mas conheces',        'bg' => '#001a18', 'accent' => '#00d9c0'],
    ];

    public function handle(): int
    {
        $dir = public_path('images/og');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filtro = $this->option('site');
        $sites = $filtro
            ? array_values(array_filter($this->sites, fn($s) => $s['slug'] === $filtro))
            : $this->sites;

        foreach ($sites as $s) {
            $this->gerar($s, $dir);
            $this->info("✓ {$s['slug']}.png");
        }
        return 0;
    }

    protected function gerar(array $s, string $dir): void
    {
        $w = 1200; $h = 630;
        $img = imagecreatetruecolor($w, $h);

        // Fundo
        [$r1, $g1, $b1] = sscanf($s['bg'], '#%02x%02x%02x');
        $bg = imagecolorallocate($img, $r1, $g1, $b1);
        imagefill($img, 0, 0, $bg);

        // Rectângulo de acento à esquerda (40px)
        [$r2, $g2, $b2] = sscanf($s['accent'], '#%02x%02x%02x');
        $accent = imagecolorallocate($img, $r2, $g2, $b2);
        imagefilledrectangle($img, 0, 0, 12, $h, $accent);

        $white = imagecolorallocate($img, 255, 255, 255);
        $grey  = imagecolorallocate($img, 160, 160, 160);

        // Paths das fonts — usar as do sistema
        $bold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
        $reg  = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

        if (!file_exists($bold)) {
            $this->warn("Font não encontrada em {$bold}. Instala: apt install fonts-dejavu-core");
            // fallback: usar imagestring em vez de ttf
            imagestring($img, 5, 80, 280, $s['titulo'], $white);
            imagestring($img, 3, 80, 340, $s['sub'], $grey);
        } else {
            // Emoji grande (fallback para texto)
            imagettftext($img, 72, 0, 80, 180, $white, $bold, $s['emoji']);
            // Título
            imagettftext($img, 64, 0, 80, 340, $white, $bold, $s['titulo']);
            // Subtítulo
            imagettftext($img, 28, 0, 80, 410, $grey, $reg, $s['sub']);
            // URL no fundo
            imagettftext($img, 20, 0, 80, 560, $accent, $bold, 'clinky.cc' . ($s['slug'] !== 'default' ? '/' . $s['slug'] : ''));
        }

        imagepng($img, "{$dir}/{$s['slug']}.png");
        imagedestroy($img);
    }
}
```

Correr:
```bash
sudo apt install fonts-dejavu-core -y   # se não tiver
php artisan clinky:og-images
```

### B.2 — Reescrever meta descriptions (< 120 chars)

Abre cada controller em `app/Http/Controllers/Sites/` e actualiza o array `seo()` com descriptions entre 120-160 chars. Referência:

| Slug | Description nova (mede com `strlen` ou `mb_strlen`) |
|---|---|
| `desculpometro` | "Gerador de desculpas com IA — realistas, criativas, épicas ou completamente absurdas. Ideal para faltar, cancelar ou desaparecer sem culpa." |
| `botao` | "Um botão vermelho. Sem explicação. Sem propósito. Já foi apertado milhões de vezes em todo o mundo. Qual é a tua resistência?" |
| `nomeador` | "Gera nomes épicos para os teus grupos de WhatsApp. Família, trabalho, amigos, casal — o nome perfeito está a três toques de distância." |
| `horoscopo` | "Horóscopo diário inútil mas estranhamente preciso. 12 signos, previsões 100% inventadas, 100% partilháveis. As estrelas encolheram os ombros." |
| `nome` | "Escreve o teu nome e descobre o que ele revela sobre ti. Análise científica* por IA — 73% caos, 27% potencial. *Totalmente inventada." |
| `bingo` | "Cartela de bingo com as situações clássicas de brasileiros em Portugal. Marca as que já viveste e descobre o teu nível de integração." |
| `conversor` | "Bicha ou fila? Autocarro ou ônibus? Pequeno-almoço ou café da manhã? O dicionário interactivo PT ↔ BR com 50+ palavras e exemplos." |
| `quiz` | "Depois de anos entre Portugal e o Brasil, quanto do outro país já absorbeste? 5 perguntas rápidas para descobrir a tua percentagem de cada." |
| `corporativo` | "Traduz jargão corporativo para português real. 'Vamos alinhar' = reunião desnecessária. Escreve o termo e recebe a tradução honesta." |
| `bolhas` | "Bolhas infinitas para rebentar no browser. Sem propósito. Sem fim. Completamente viciante. Não digas que não avisámos antes de começares." |
| `progresso` | "Introduz a tua data de nascimento. Vê exactamente que percentagem da tua vida já passou. Actualiza em tempo real. Não é motivacional." |
| `proibido` | "Há um botão neste site. Disseram-te para não carregares. O que vais fazer? Cada clique dá um resultado diferente e inesperado." |
| `decisao` | "Duas opções impossíveis. Nenhuma é boa. Tens de escolher uma. Recebe uma análise psicológica da tua escolha e partilha com quem te conhece." |
| `oraculo` | "Faz uma pergunta ao Oráculo. A resposta vai fazer sentido, sempre. Efeito Barnum em acção — vago o suficiente para caber em qualquer vida." |

Para cada controller, substituir a array `seo()` (ou propriedade equivalente) com a description nova. Mede antes:

```bash
php artisan tinker --execute='echo mb_strlen("Gerador de desculpas..."), PHP_EOL;'
```

### B.3 — Reescrever titles fora de gama

| Slug | Title actual | Title novo (30-65 chars) |
|---|---|---|
| `nada` | "Nada" (4) | "Nada — Literalmente Nada para Ver Aqui" (40) |
| `corporativo` | "Tradutor Corporativo — O Que Significam Realmente as Palavras do Escritório" (87) | "Tradutor Corporativo — Jargão para Português Real" (51) |

Os outros 6 que falharam — verifica um a um:
```bash
curl -s http://localhost:8000/{slug} | grep -oP '<title>\K[^<]+'
```
E ajusta no controller.

### B.4 — Horóscopo sem share bar

Em `resources/views/sites/horoscopo/index.blade.php` (ou `signo.blade.php` se a share é só na página do signo), adicionar depois do card da previsão:

```blade
<x-share-bar
    :text="'O meu horóscopo de hoje diz: ' . $previsao"
    :url="route('horoscopo.signo', $signo)"
    accent="purple" />
```

Confirmar que o componente `<x-share-bar>` aceita estes 3 props. Se não, adicionar ao `resources/views/components/share-bar.blade.php`.

### B.5 — Hub sem `max-w-*`

Em `resources/views/hub/home.blade.php`, envolver cada secção com um container máximo:

```blade
<section class="relative min-h-screen flex items-center overflow-hidden" style="...">
    <div class="relative z-10 w-full max-w-2xl mx-auto px-6 py-24 md:px-12 md:max-w-4xl">
        {{-- conteúdo da secção --}}
    </div>
</section>
```

### B.6 — Correr revisão final

```bash
php artisan clinky:review --visual
```

Objectivo: zero falhas, avisos aceitáveis.

---

## Checklist de confirmação

Antes de terminares, confirma:

- [ ] PARTE A aplicada e reviewer já não apanha falsos positivos
- [ ] 14 OG images geradas em `public/images/og/*.png`
- [ ] 12 meta descriptions reescritas entre 120-160 chars
- [ ] 2 titles (mínimo) corrigidos para 30-65 chars
- [ ] Horóscopo tem share bar funcional
- [ ] Hub tem `max-w-*` em cada secção
- [ ] `php artisan clinky:review` retorna exit code 0 (ou só avisos)

No final, mostra-me o output da última revisão completa.
