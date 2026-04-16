# PROMPT 01 — Desculpómetro

## Objectivo
Criar o mini-site `desculpometro.clinky.cc` — gerador de desculpas absurdas com IA (Claude API).

## Lê primeiro
- `SKILL.md`
- `REFERENCES/ARCHITECTURE.md`
- `REFERENCES/SEO.md`
- `REFERENCES/PRIVACY.md`
- `REFERENCES/COMPONENTS.md`

## Assumir que existe
- Projecto Laravel base do PROMPT 00 já executado
- `ClaudeService` já criado
- `AnalyticsService` já criado
- Layouts e componentes base já existem

---

## SEO deste mini-site

```php
$seo = [
    'title'          => 'Desculpómetro — Gera a Desculpa Perfeita em 1 Segundo',
    'description'    => 'Gerador de desculpas absurdas, criativas e irresistíveis com IA. Ideal para quando precisas de uma razão épica para faltar, cancelar ou desaparecer.',
    'og_title'       => '😅 Desculpómetro — a culpa foi do gato filosófico',
    'og_description' => 'Gera a tua desculpa perfeita agora. Grátis, anónimo, partilhável no WhatsApp.',
    'og_image'       => asset('images/og/desculpometro.png'),
    'canonical'      => 'https://desculpometro.clinky.cc',
    'keywords'       => 'desculpas engraçadas, gerador de desculpas, desculpa criativa, humor, scuse',
];
```

OG Image `public/images/og/desculpometro.png`:
- Fundo `#2E1400` (dark orange)
- Emoji 😅 grande
- Texto: "Desculpómetro" + "Gera a desculpa perfeita"

---

## Tasks

### 1. Route

Em `routes/subdomains.php`:
```php
Route::domain('desculpometro.' . config('app.base_domain'))->group(function () {
    Route::get('/', [DesculpometroController::class, 'index'])->name('desculpometro.index');
    Route::post('/gerar', [DesculpometroController::class, 'gerar'])->name('desculpometro.gerar');
});
```

### 2. Controller

`app/Http/Controllers/Sites/DesculpometroController.php`

```php
<?php
namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\ClaudeService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DesculpometroController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('desculpometro');
        $totalGeradas = AnalyticsService::getCount('desculpometro', 'generate', 365);

        return view('sites.desculpometro.index', [
            'seo' => $this->seo(),
            'totalGeradas' => max($totalGeradas, 1247), // seed number para arranque
        ]);
    }

    public function gerar(Request $request)
    {
        $request->validate([
            'situacao' => 'required|in:trabalho,ginasio,familia,encontro,reuniao,aula,consulta,outro',
            'absurdo'  => 'required|integer|min:0|max:3',
        ]);

        $niveis = ['realista', 'criativo', 'épico', 'completamente absurdo'];
        $nivel  = $niveis[$request->absurdo];
        $situacao = $request->situacao;

        $systemPrompt = <<<PROMPT
        És o Desculpómetro — um gerador de desculpas {$nivel}.
        Gera UMA desculpa curta (máximo 2 frases) para justificar: {$situacao}.
        A desculpa deve ser {$nivel} e divertida, mas plausível para o nível pedido.
        Responde APENAS com a desculpa, sem introdução, sem aspas, sem explicações.
        Escreve em português de Portugal.
        PROMPT;

        $desculpa = $this->claude->generate($systemPrompt, "Gera uma desculpa {$nivel} para {$situacao}.");

        if (!$desculpa) {
            $desculpa = 'O meu gerador de desculpas teve uma crise existencial. Tenta outra vez.';
        }

        AnalyticsService::event('desculpometro', 'generate');

        // PRIVACIDADE: Não guardamos o input do utilizador.
        return response()->json(['desculpa' => $desculpa]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Desculpómetro — Gera a Desculpa Perfeita em 1 Segundo',
            'description'    => 'Gerador de desculpas absurdas com IA. Ideal para quando precisas de uma razão épica para faltar, cancelar ou desaparecer.',
            'og_title'       => '😅 Desculpómetro — a culpa foi do gato filosófico',
            'og_description' => 'Gera a tua desculpa perfeita agora. Grátis, anónimo, partilhável.',
            'og_image'       => asset('images/og/desculpometro.png'),
            'canonical'      => 'https://desculpometro.' . config('app.base_domain'),
        ];
    }
}
```

### 3. View `resources/views/sites/desculpometro/index.blade.php`

Implementar conforme exemplo completo em `REFERENCES/COMPONENTS.md` (secção "Layout mini-site completo").

**Opções de situação:**
- `trabalho` → "Faltar ao trabalho"
- `ginasio` → "Faltar ao ginásio"
- `familia` → "Evitar a família"
- `encontro` → "Cancelar um encontro"
- `reuniao` → "Sair de uma reunião"
- `aula` → "Faltar à aula"
- `consulta` → "Cancelar consulta"
- `outro` → "Outra situação"

**Graus de absurdo:**
- 0 → "Normal 😐"
- 1 → "Criativo 😏"
- 2 → "Épico 🤌"
- 3 → "Absurdo 🤯"

**Cor de acento:** `orange`

**Texto de partilha WhatsApp:**
```
"{desculpa gerada}"

😅 Gera a tua em: https://desculpometro.clinky.cc
```

### 4. JSON-LD

```json
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Desculpómetro",
  "description": "Gerador de desculpas absurdas com IA",
  "url": "https://desculpometro.clinky.cc",
  "applicationCategory": "EntertainmentApplication",
  "operatingSystem": "Web",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "EUR" },
  "inLanguage": "pt-PT",
  "isPartOf": { "@type": "WebSite", "name": "Clinky.cc", "url": "https://clinky.cc" }
}
```

### 5. OG Image

Criar `public/images/og/desculpometro.png` (1200×630px).
Design: fundo escuro tom laranja, emoji 😅 grande centrado, texto "Desculpómetro" em branco bold, tagline "A desculpa perfeita, gerada por IA" em cinzento claro.

### 6. Adicionar ao sitemap

Em `SitemapController`, adicionar URL do mini-site.

### 7. Adicionar ao hub

No `HomeController`, marcar `desculpometro` como `live: true`.

---

## Prompt Claude (referência técnica)

O prompt enviado à Claude API deve:
- Ser específico sobre o nível de absurdo
- Pedir resposta em PT-PT
- Limitar a 2 frases máximo
- Não pedir introdução ou explicações
- System prompt + user message separados (não misturar)

Testar manualmente os 4 níveis × 8 situações antes de publicar.

---

## Resultado esperado

- `desculpometro.clinky.cc` live
- Gera desculpas via Claude API
- Share para WhatsApp funcional com texto gerado
- Counter de desculpas geradas visível
- SEO completo e OG image correcta
