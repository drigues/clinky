# ARCHITECTURE.md — Clinky.cc

## Routing por subdomínio

Em `routes/web.php`, inclui o ficheiro de subdomínios:

```php
require __DIR__.'/subdomains.php';

// Homepage hub
Route::get('/', [HomeController::class, 'index'])
    ->name('home');
```

Em `routes/subdomains.php`:

```php
<?php
// Cada mini-site tem o seu grupo de routes
Route::domain('desculpometro.' . config('app.base_domain'))->group(function () {
    Route::get('/', [DesculpometroController::class, 'index'])->name('desculpometro.index');
    Route::post('/gerar', [DesculpometroController::class, 'gerar'])->name('desculpometro.gerar');
});

Route::domain('botao.' . config('app.base_domain'))->group(function () {
    Route::get('/', [BotaoController::class, 'index'])->name('botao.index');
    Route::post('/pressionar', [BotaoController::class, 'pressionar'])->name('botao.pressionar');
});

// ...adicionar novo mini-site aqui
```

Em `config/app.php` adiciona:
```php
'base_domain' => env('BASE_DOMAIN', 'clinky.cc'),
```

Em `.env`:
```
BASE_DOMAIN=clinky.cc
APP_URL=https://clinky.cc
```

---

## Layout base dos mini-sites

`resources/views/layouts/minisite.blade.php` — layout partilhado por todos os mini-sites:

```blade
<!DOCTYPE html>
<html lang="pt" class="{{ request()->cookie('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO básico - cada mini-site passa estas variáveis --}}
    <title>@yield('title') — Clinky.cc</title>
    <meta name="description" content="@yield('description')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ request()->url() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', yield('title'))">
    <meta property="og:description" content="@yield('og_description', yield('description'))">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:site_name" content="Clinky.cc">
    <meta property="og:locale" content="pt_PT">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', yield('title'))">
    <meta name="twitter:description" content="@yield('og_description', yield('description'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

    {{-- WhatsApp específico --}}
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Fathom Analytics (privacy-first, sem cookies) --}}
    @if(config('services.fathom.site_id'))
    <script src="https://cdn.usefathom.com/script.js"
            data-site="{{ config('services.fathom.site_id') }}"
            data-canonical="{{ request()->url() }}"
            defer></script>
    @endif

    {{-- Structured Data --}}
    @stack('structured_data')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">

    {{-- Back to Clinky --}}
    <div class="fixed top-4 left-4 z-50">
        <a href="https://clinky.cc"
           class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 flex items-center gap-1 transition-colors">
            ← clinky.cc
        </a>
    </div>

    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Share bar fixa (componente global) --}}
    @isset($shareText)
    <div x-data="shareBar()" class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-t border-zinc-200 dark:border-zinc-800">
        @include('components.share-bar', ['text' => $shareText, 'url' => request()->url()])
    </div>
    @endisset

    @stack('scripts')
</body>
</html>
```

---

## Controller base de mini-site

Padrão mínimo para qualquer mini-site:

```php
<?php
namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;

class DesculpometroController extends Controller
{
    public function index()
    {
        AnalyticsService::pageView('desculpometro');

        return view('sites.desculpometro.index', [
            'title' => 'Desculpómetro — Gera a Desculpa Perfeita',
            'description' => 'Gerador de desculpas absurdas e irresistíveis. Grátis, anónimo e fácil de partilhar.',
        ]);
    }
}
```

---

## ClaudeService

`app/Services/ClaudeService.php`:

```php
<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey;
    private string $model = 'claude-sonnet-4-20250514';
    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');
    }

    public function generate(string $systemPrompt, string $userMessage, int $maxTokens = 300): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage]
                ],
            ]);

            if ($response->successful()) {
                return $response->json('content.0.text');
            }

            Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Claude API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
```

Em `config/services.php`:
```php
'claude' => [
    'api_key' => env('CLAUDE_API_KEY'),
],
'fathom' => [
    'site_id' => env('FATHOM_SITE_ID'),
],
```

---

## AnalyticsService (agregado, sem PII)

`app/Services/AnalyticsService.php`:

```php
<?php
namespace App\Services;

use App\Models\SiteVisit;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    public static function pageView(string $site): void
    {
        // Incrementa contador agregado por dia - sem IP, sem user agent completo
        $key = "visits:{$site}:" . now()->format('Y-m-d');
        Cache::increment($key);

        // Persiste no DB de hora em hora via job
        // Ver App\Jobs\FlushAnalytics
    }

    public static function event(string $site, string $event): void
    {
        $key = "events:{$site}:{$event}:" . now()->format('Y-m-d');
        Cache::increment($key);
    }

    public static function getCount(string $site, string $event = 'view', int $days = 30): int
    {
        // Retorna total dos últimos N dias
        return SiteVisit::where('site', $site)
            ->where('event', $event)
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->sum('count');
    }
}
```

---

## Database migrations necessárias

```php
// site_visits - analytics agregado, sem PII
Schema::create('site_visits', function (Blueprint $table) {
    $table->id();
    $table->string('site', 50);         // 'desculpometro', 'botao', etc.
    $table->string('event', 50);        // 'view', 'share', 'generate', 'press'
    $table->date('date');
    $table->unsignedBigInteger('count')->default(0);
    $table->timestamps();
    $table->unique(['site', 'event', 'date']);
});

// button_presses - para o mini-site "Aperta o Botão"
Schema::create('button_presses', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('total')->default(0);
    $table->timestamps();
});
```

---

## Filament Resources

Cada mini-site tem um `FilamentResource` mínimo:

```php
// app/Filament/Resources/SiteVisitResource.php
// Mostra stats por site no admin
// Stats widgets: total visitas, total shares, taxa de share
```

Widgets no dashboard Filament:
- `TotalVisitsWidget` — visitas total hoje/semana/mês
- `TopSiteWidget` — mini-site mais visitado
- `ShareRateWidget` — % de visitas que partilham
- `ApiCostWidget` — estimativa custo Claude API

---

## Shared Blade Components

```
resources/views/components/
├── share-bar.blade.php          ← botões de partilha
├── site-header.blade.php        ← header de mini-site
├── result-card.blade.php        ← card de resultado (IA ou gerado)
├── counter-badge.blade.php      ← badge com número animado
└── back-to-clinky.blade.php     ← link de volta ao hub
```

Ver `REFERENCES/COMPONENTS.md` para implementação de cada um.
