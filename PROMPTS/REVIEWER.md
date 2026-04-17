# REVIEWER.md — Sistema de Revisão Automática do Clinky.cc

Lê `CLAUDE.md`, `REFERENCES/ARCHITECTURE.md` e `REFERENCES/COMPONENTS.md` antes de começar.

Cria um sistema de revisão que corre com:

```bash
php artisan clinky:review
```

Opções:
- `--site=desculpometro` → revê apenas um mini-site
- `--visual` → adiciona screenshots via Playwright (mobile 375px + desktop 1280px)
- `--fix` → sugere correcções automáticas no stdout (sem aplicar)

Output:
1. Tabela colorida no terminal com ✅ / ⚠️ / ❌ por mini-site
2. Ficheiro `storage/app/review-report.md` com detalhe completo
3. Exit code `0` se tudo ok, `1` se houver falhas — para poder correr em CI

---

## Tasks

### 1. Comando Artisan

Criar:
```bash
php artisan make:command ClinkyReview
```

Ficheiro: `app/Console/Commands/ClinkyReview.php`

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ClinkyReview extends Command
{
    protected $signature = 'clinky:review
        {--site= : Rever apenas um slug específico}
        {--visual : Tirar screenshots mobile e desktop}
        {--fix : Mostrar sugestões de correcção}';

    protected $description = 'Revê todos os mini-sites do Clinky.cc';

    /** Lista canónica — manter sincronizada com HomeController */
    protected array $sites = [
        ['slug' => '',               'nome' => 'Hub Homepage',          'usa_api' => false, 'categoria' => 'hub'],
        ['slug' => 'desculpometro',  'nome' => 'Desculpómetro',         'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'botao',          'nome' => 'Aperta o Botão',        'usa_api' => false, 'categoria' => 'db'],
        ['slug' => 'nomeador',       'nome' => 'Nomeador de Grupos',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'horoscopo',      'nome' => 'Horóscopo Inútil',      'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'nome',           'nome' => 'Analisador de Nome',    'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'bingo',          'nome' => 'Bingo do Imigrante',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'conversor',      'nome' => 'Conversor PT/BR',       'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'quiz',           'nome' => 'Sou mais BR ou PT?',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'corporativo',    'nome' => 'Tradutor Corporativo',  'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'bolhas',         'nome' => 'Rebenta as Bolhas',     'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'progresso',      'nome' => 'Progresso da Vida',     'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'nada',           'nome' => 'Nada',                  'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'proibido',       'nome' => 'O Botão Proibido',      'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'decisao',        'nome' => 'A Decisão Impossível',  'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'oraculo',        'nome' => 'O Oráculo',             'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'panico',         'nome' => 'Modo Pânico',           'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'tempo',          'nome' => 'Quanto Tempo Perdeste?','usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'lista',          'nome' => 'Lista do Nunca',        'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'conquistas',     'nome' => 'Conquistas do Nada',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'ouviste',        'nome' => 'Já Ouviste Isto?',      'usa_api' => true,  'categoria' => 'ia'],
    ];

    protected array $resultados = [];
    protected string $baseUrl;

    public function handle(): int
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
        $filtro = $this->option('site');

        $sites = $filtro
            ? array_values(array_filter($this->sites, fn($s) => $s['slug'] === $filtro))
            : $this->sites;

        if (empty($sites)) {
            $this->error("Site '{$filtro}' não encontrado.");
            return 1;
        }

        $this->info('');
        $this->info('🔍 A rever ' . count($sites) . ' sites em ' . $this->baseUrl);
        $this->info('');

        foreach ($sites as $site) {
            $this->revSite($site);
        }

        $this->mostrarTabela();
        $this->gravarRelatorio();

        if ($this->option('visual')) {
            $this->correrVisual($sites);
        }

        $totalFalhas = collect($this->resultados)->sum('falhas');
        $this->info('');
        $this->info("📄 Relatório: storage/app/review-report.md");

        return $totalFalhas > 0 ? 1 : 0;
    }

    /* -------------------------------------------------------------- */
    /*  REVISÃO POR SITE                                              */
    /* -------------------------------------------------------------- */

    protected function revSite(array $site): void
    {
        $slug = $site['slug'];
        $url  = $slug === '' ? $this->baseUrl : $this->baseUrl . '/' . $slug;
        $this->line("  → {$site['nome']} ({$url})");

        $checks = [];

        // 1. HTTP response
        try {
            $r = Http::timeout(10)->get($url);
            $checks['http_200']        = $r->status() === 200;
            $checks['content_length']  = strlen($r->body()) > 1000;
            $html                      = $r->body();
        } catch (\Throwable $e) {
            $this->warn("    ❌ Falha de rede: {$e->getMessage()}");
            $this->resultados[$slug ?: 'hub'] = [
                'site'     => $site,
                'checks'   => ['http_200' => false],
                'falhas'   => 1,
                'sucessos' => 0,
                'avisos'   => [],
            ];
            return;
        }

        $crawler = new Crawler($html);
        $avisos  = [];

        // 2. SEO essencial
        $checks['tem_title']       = $crawler->filter('title')->count() > 0;
        $checks['tem_description'] = $crawler->filter('meta[name=description]')->count() > 0;
        $checks['tem_viewport']    = $crawler->filter('meta[name=viewport]')->count() > 0;
        $checks['tem_canonical']   = $crawler->filter('link[rel=canonical]')->count() > 0;

        // Title entre 30-65 chars
        if ($checks['tem_title']) {
            $titleText = trim($crawler->filter('title')->first()->text());
            $len = mb_strlen($titleText);
            $checks['title_len_ok'] = $len >= 30 && $len <= 65;
            if (!$checks['title_len_ok']) {
                $avisos[] = "Title tem {$len} chars (ideal 30-65): '{$titleText}'";
            }
        }

        // Description entre 120-160 chars
        if ($checks['tem_description']) {
            $desc = $crawler->filter('meta[name=description]')->first()->attr('content') ?? '';
            $len = mb_strlen($desc);
            $checks['desc_len_ok'] = $len >= 120 && $len <= 160;
            if (!$checks['desc_len_ok']) {
                $avisos[] = "Description tem {$len} chars (ideal 120-160)";
            }
        }

        // 3. Open Graph
        $checks['og_title']       = $crawler->filter('meta[property="og:title"]')->count() > 0;
        $checks['og_description'] = $crawler->filter('meta[property="og:description"]')->count() > 0;
        $checks['og_image']       = $crawler->filter('meta[property="og:image"]')->count() > 0;
        $checks['og_url']         = $crawler->filter('meta[property="og:url"]')->count() > 0;
        $checks['og_type']        = $crawler->filter('meta[property="og:type"]')->count() > 0;

        // OG image existe no disco?
        if ($checks['og_image']) {
            $ogImageUrl = $crawler->filter('meta[property="og:image"]')->first()->attr('content') ?? '';
            $path = public_path(parse_url($ogImageUrl, PHP_URL_PATH) ?? '');
            $checks['og_image_existe'] = file_exists($path);
            if (!$checks['og_image_existe']) {
                $avisos[] = "OG image não existe em disco: {$path}";
            }
        }

        // 4. JSON-LD (excepto hub, onde pode não ser necessário)
        if ($slug !== '') {
            $checks['tem_jsonld'] = $crawler->filter('script[type="application/ld+json"]')->count() > 0;
        }

        // 5. Tipografia — o h1 deve ter classe grande
        $h1 = $crawler->filter('h1, h2')->first();
        if ($h1->count() > 0) {
            $classes = $h1->attr('class') ?? '';
            $grande  = Str::contains($classes, ['text-4xl', 'text-5xl', 'text-6xl', 'text-7xl', 'text-8xl', 'text-9xl']);
            $checks['heading_grande'] = $grande;
            if (!$grande) {
                $avisos[] = "Heading principal não parece grande (classes: '{$classes}')";
            }
        } else {
            $checks['heading_grande'] = false;
            $avisos[] = "Nenhum h1 ou h2 encontrado";
        }

        // 6. Margens mobile-first
        $main = $crawler->filter('main, [role=main], section')->first();
        if ($main->count() > 0) {
            $classesMain = '';
            $crawler->filter('section, main, .container, [class*=max-w]')
                ->each(function ($node) use (&$classesMain) {
                    $classesMain .= ' ' . ($node->attr('class') ?? '');
                });
            $checks['tem_padding_mobile'] = Str::contains($classesMain, ['px-4', 'px-5', 'px-6', 'p-4', 'p-6', 'p-8']);
            $checks['tem_max_width']      = Str::contains($classesMain, ['max-w-']);
            if (!$checks['tem_padding_mobile']) {
                $avisos[] = "Não encontrei padding horizontal mobile (px-4/5/6)";
            }
        }

        // 7. Componentes obrigatórios (excepto hub)
        if ($slug !== '') {
            // Share bar — procurar link wa.me ou navigator.share
            $temShare = Str::contains($html, ['wa.me/', 'whatsapp.com/send', 'navigator.share', 'x-data="share']);
            $checks['tem_share'] = $temShare;
            if (!$temShare) {
                $avisos[] = "Não encontrei share bar (wa.me/navigator.share)";
            }

            // Link de volta ao hub
            $temLinkHub = $crawler->filter('a')->reduce(function ($node) {
                $href = $node->attr('href') ?? '';
                return $href === '/' || Str::contains($href, ['clinky.cc', "://clinky"]) || $href === url('/');
            })->count() > 0;
            $checks['tem_link_hub'] = $temLinkHub;
        }

        // 8. Dark mode
        $checks['suporta_dark'] = Str::contains($html, ['dark:', 'prefers-color-scheme', 'bg-zinc-9', 'bg-[#0']);

        // 9. Alpine.js
        $checks['tem_alpine'] = Str::contains($html, ['x-data', 'alpine']);

        // 10. CSRF (só em páginas com POST)
        if ($site['usa_api']) {
            $checks['tem_csrf'] = Str::contains($html, ['csrf-token', 'X-CSRF-TOKEN']);
        }

        // 11. Sem horizontal overflow suspect (procurar overflow-x-hidden ou width fixed problemático)
        $temOverflowFix = Str::contains($html, ['overflow-x-hidden', 'overflow-hidden']);
        // Só aviso, não falha
        if (!$temOverflowFix) {
            $avisos[] = "Nenhum overflow-x-hidden (podes querer adicionar no body para segurança)";
        }

        // 12. Acção POST (se aplicável) — testar endpoint
        if ($site['usa_api'] && !empty($this->acaoPost($slug))) {
            $checks['acao_responde'] = $this->testarAcao($slug);
        }

        $falhas   = count(array_filter($checks, fn($v) => $v === false));
        $sucessos = count(array_filter($checks, fn($v) => $v === true));

        $this->resultados[$slug ?: 'hub'] = [
            'site'     => $site,
            'url'      => $url,
            'checks'   => $checks,
            'falhas'   => $falhas,
            'sucessos' => $sucessos,
            'avisos'   => $avisos,
        ];

        $status = $falhas === 0 ? ($avisos ? '⚠️ ' : '✅ ') : '❌ ';
        $this->line("    {$status}{$sucessos} ok / {$falhas} falhas / " . count($avisos) . " avisos");
    }

    /* -------------------------------------------------------------- */
    /*  TESTE DE ACÇÕES (POST)                                        */
    /* -------------------------------------------------------------- */

    protected function acaoPost(string $slug): ?array
    {
        $rotas = [
            'desculpometro' => ['/desculpometro/gerar', ['situacao' => 'trabalho', 'absurdo' => 1]],
            'nome'          => ['/nome/analisar',       ['nome' => 'Teste']],
            'corporativo'   => ['/corporativo/traduzir',['texto' => 'vamos alinhar']],
            'decisao'       => ['/decisao/escolher',    ['escolha' => 'A', 'opcao' => 'a']],
            'oraculo'       => ['/oraculo/consultar',   ['pergunta' => 'vou ficar bem?']],
            'panico'        => ['/panico/activar',      ['situacao' => 'reuniao às 10h']],
            'ouviste'       => ['/ouviste/gerar',       ['categoria' => 'memória']],
            'botao'         => ['/botao/pressionar',    []],
            'proibido'      => ['/proibido/carregar',   []],
        ];
        return $rotas[$slug] ?? null;
    }

    protected function testarAcao(string $slug): bool
    {
        [$path, $data] = $this->acaoPost($slug);
        try {
            // Obter CSRF primeiro
            $home = Http::timeout(5)->get($this->baseUrl . '/' . $slug);
            preg_match('/<meta name="csrf-token" content="([^"]+)"/', $home->body(), $m);
            $token = $m[1] ?? '';

            $r = Http::timeout(15)
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

    /* -------------------------------------------------------------- */
    /*  OUTPUT                                                         */
    /* -------------------------------------------------------------- */

    protected function mostrarTabela(): void
    {
        $this->info('');
        $this->info('═══════════════════════════════════════════════════');
        $this->info('  RESUMO');
        $this->info('═══════════════════════════════════════════════════');

        $rows = [];
        foreach ($this->resultados as $key => $res) {
            $status = $res['falhas'] === 0
                ? ($res['avisos'] ? '<fg=yellow>⚠️  AVISO</>' : '<fg=green>✅ OK</>')
                : '<fg=red>❌ FALHA</>';

            $rows[] = [
                $res['site']['nome'],
                $status,
                $res['sucessos'],
                $res['falhas'],
                count($res['avisos']),
            ];
        }

        $this->table(
            ['Site', 'Status', 'OK', 'Falhas', 'Avisos'],
            $rows
        );
    }

    protected function gravarRelatorio(): void
    {
        $md = "# Clinky.cc — Relatório de Revisão\n\n";
        $md .= "_Gerado em " . now()->format('Y-m-d H:i:s') . "_\n\n";

        foreach ($this->resultados as $key => $res) {
            $icon = $res['falhas'] === 0 ? ($res['avisos'] ? '⚠️' : '✅') : '❌';
            $md .= "## {$icon} {$res['site']['nome']}\n";
            $md .= "- URL: `{$res['url']}`\n";
            $md .= "- OK: {$res['sucessos']} · Falhas: {$res['falhas']} · Avisos: " . count($res['avisos']) . "\n\n";

            $md .= "### Checks\n";
            foreach ($res['checks'] as $check => $valor) {
                $icon2 = $valor ? '✅' : '❌';
                $md .= "- {$icon2} `{$check}`\n";
            }

            if ($res['avisos']) {
                $md .= "\n### Avisos\n";
                foreach ($res['avisos'] as $a) {
                    $md .= "- ⚠️ {$a}\n";
                }
            }
            $md .= "\n---\n\n";
        }

        file_put_contents(storage_path('app/review-report.md'), $md);
    }

    /* -------------------------------------------------------------- */
    /*  VISUAL (opcional)                                              */
    /* -------------------------------------------------------------- */

    protected function correrVisual(array $sites): void
    {
        $script = base_path('review/visual.cjs');
        if (!file_exists($script)) {
            $this->warn('Visual review precisa do ficheiro review/visual.cjs (instalar com: npm i -D playwright)');
            return;
        }
        $this->info('');
        $this->info('📸 A gerar screenshots (mobile 375px + desktop 1280px)...');
        $slugs = implode(',', array_map(fn($s) => $s['slug'] ?: '_hub', $sites));
        passthru("node {$script} --base={$this->baseUrl} --sites={$slugs}");
        $this->info('   Screenshots em: storage/app/screenshots/');
    }
}
```

### 2. Script visual opcional (Playwright)

Criar `review/visual.cjs`:

```javascript
// Uso: node review/visual.cjs --base=http://localhost:8000 --sites=desculpometro,botao
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const args = Object.fromEntries(
    process.argv.slice(2).map(a => {
        const [k, v] = a.replace(/^--/, '').split('=');
        return [k, v];
    })
);

const base  = args.base  || 'http://localhost:8000';
const sites = (args.sites || '').split(',').filter(Boolean);
const outDir = path.join(__dirname, '..', 'storage', 'app', 'screenshots');
fs.mkdirSync(outDir, { recursive: true });

(async () => {
    const browser = await chromium.launch();

    for (const slug of sites) {
        const url = slug === '_hub' ? base : `${base}/${slug}`;
        console.log(`📸 ${url}`);

        for (const vp of [
            { name: 'mobile',  width: 375,  height: 812 },
            { name: 'desktop', width: 1280, height: 800 },
        ]) {
            const ctx = await browser.newContext({
                viewport: { width: vp.width, height: vp.height },
            });
            const page = await ctx.newPage();

            try {
                await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });

                // Verificar overflow horizontal
                const hasOverflow = await page.evaluate(() => {
                    return document.documentElement.scrollWidth > document.documentElement.clientWidth;
                });
                if (hasOverflow) console.log(`  ⚠️  overflow-x em ${slug} @ ${vp.name}`);

                // Verificar font-size do heading principal
                const headingSize = await page.evaluate(() => {
                    const h = document.querySelector('h1, h2');
                    return h ? parseFloat(getComputedStyle(h).fontSize) : null;
                });
                const min = vp.name === 'mobile' ? 32 : 48;
                if (headingSize !== null && headingSize < min) {
                    console.log(`  ⚠️  heading pequeno em ${slug} @ ${vp.name}: ${headingSize}px (min ${min}px)`);
                }

                await page.screenshot({
                    path: path.join(outDir, `${slug}-${vp.name}.png`),
                    fullPage: true,
                });
            } catch (e) {
                console.log(`  ❌ ${slug} @ ${vp.name}: ${e.message}`);
            }
            await ctx.close();
        }
    }

    await browser.close();
})();
```

Instalar Playwright:
```bash
npm i -D playwright
npx playwright install chromium
```

### 3. Ficheiro `.gitignore`

Adicionar:
```
/storage/app/review-report.md
/storage/app/screenshots/
```

---

## Como usar

```bash
# Revisão completa
php artisan clinky:review

# Só um mini-site
php artisan clinky:review --site=desculpometro

# Com screenshots
php artisan clinky:review --visual

# Em CI (retorna código 1 se houver falhas)
php artisan clinky:review && echo "Tudo ok" || echo "Há problemas"
```

---

## O que é verificado

**Por cada mini-site:**

- [x] **Retorno HTTP** — rota responde 200 e tem conteúdo (>1KB)
- [x] **SEO** — title (30–65 chars), description (120–160), canonical, viewport
- [x] **Open Graph** — og:title, og:description, og:image (+ ficheiro existe), og:url, og:type
- [x] **JSON-LD** — schema.org presente
- [x] **Tipografia** — h1/h2 com classe `text-4xl` ou maior
- [x] **Margens** — padding horizontal mobile (`px-4/5/6`) e `max-w-*`
- [x] **Componentes** — share bar (wa.me ou navigator.share), link de volta ao hub
- [x] **Dark mode** — classes `dark:` ou `prefers-color-scheme`
- [x] **Alpine.js** — `x-data` presente em páginas interactivas
- [x] **CSRF** — token em páginas com POST
- [x] **Ações POST** — endpoints retornam JSON válido (testa `/gerar`, `/analisar`, `/escolher`, etc.)

**Visual (com `--visual`):**

- [x] Screenshot mobile 375×812
- [x] Screenshot desktop 1280×800
- [x] Sem overflow horizontal
- [x] Heading principal ≥32px mobile / ≥48px desktop

---

## Tasks para Claude Code

1. Cria `app/Console/Commands/ClinkyReview.php` com o código acima
2. Cria `review/visual.cjs` com o script Playwright
3. Adiciona `symfony/dom-crawler` se ainda não existir: `composer require symfony/dom-crawler`
4. Actualiza `.gitignore`
5. Corre `php artisan clinky:review` pela primeira vez e mostra-me o output
6. Se houver falhas, lista-as mas não as corrijas — só as apresentas

Confirma cada task com ✅ antes de avançar.
