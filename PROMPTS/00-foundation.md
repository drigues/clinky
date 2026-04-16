# PROMPT 00 — Fundação do Projecto Clinky.cc

## Objectivo
Criar o projecto Laravel base do Clinky.cc — repositório, estrutura, homepage e deploy.

## Lê primeiro
- `SKILL.md`
- `REFERENCES/ARCHITECTURE.md`
- `REFERENCES/SEO.md`
- `REFERENCES/PRIVACY.md`
- `REFERENCES/COMPONENTS.md`

---

## Tasks

### 1. Criar projecto Laravel 12

```bash
composer create-project laravel/laravel clinky.cc
cd clinky.cc
```

Instalar dependências:
```bash
composer require livewire/livewire filament/filament:"^3.0" -W
npm install -D tailwindcss @tailwindcss/vite alpinejs
```

### 2. Configurar `.env`

```
APP_NAME="Clinky.cc"
APP_URL=https://clinky.cc
BASE_DOMAIN=clinky.cc

CLAUDE_API_KEY=
FATHOM_SITE_ID=

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=true
```

### 3. Criar `config/app.php` — adicionar `base_domain`

Adicionar à array de configuração:
```php
'base_domain' => env('BASE_DOMAIN', 'clinky.cc'),
```

### 4. Migrations

Criar e executar:

```bash
php artisan make:migration create_site_visits_table
php artisan make:migration create_button_presses_table
```

Conteúdo das migrations: ver `REFERENCES/ARCHITECTURE.md` secção "Database migrations".

### 5. Models

```bash
php artisan make:model SiteVisit
php artisan make:model ButtonPress
```

### 6. Services

Criar:
- `app/Services/ClaudeService.php` — ver `REFERENCES/ARCHITECTURE.md`
- `app/Services/AnalyticsService.php` — ver `REFERENCES/ARCHITECTURE.md`

### 7. Routing

Criar `routes/subdomains.php` — inicialmente vazio (só estrutura).

Em `routes/web.php`:
```php
require __DIR__.'/subdomains.php';

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::get('/privacidade', [PrivacidadeController::class, 'index'])->name('privacidade');
Route::post('/api/track', [TrackController::class, 'store'])->name('track');
```

### 8. Controllers

```bash
php artisan make:controller Hub/HomeController
php artisan make:controller SitemapController
php artisan make:controller RobotsController
php artisan make:controller PrivacidadeController
php artisan make:controller TrackController
```

**HomeController** — devolve a homepage com lista de mini-sites activos:

```php
public function index()
{
    $sites = collect([
        [
            'slug'        => 'desculpometro',
            'title'       => 'Desculpómetro',
            'emoji'       => '😅',
            'tagline'     => 'Gera a desculpa perfeita',
            'color'       => 'orange',
            'url'         => 'https://desculpometro.' . config('app.base_domain'),
            'live'        => true,
            'tag'         => 'Top',
        ],
        [
            'slug'        => 'botao',
            'title'       => 'Aperta o Botão',
            'emoji'       => '🔴',
            'tagline'     => 'Um botão. Sem explicação.',
            'color'       => 'lime',
            'url'         => 'https://botao.' . config('app.base_domain'),
            'live'        => false,
            'tag'         => 'Em Alta',
        ],
        // ...adicionar os restantes
    ]);

    return view('hub.home', compact('sites'));
}
```

**TrackController** — recebe eventos de partilha (anónimos):
```php
public function store(Request $request)
{
    $event = $request->input('event', 'unknown');
    // Só aceita eventos conhecidos — sem dados pessoais
    $allowed = ['share_whatsapp', 'share_native', 'share_copy', 'generate', 'press'];
    if (in_array($event, $allowed)) {
        AnalyticsService::event(request()->getHost(), $event);
    }
    return response()->json(['ok' => true]);
}
```

### 9. Layouts Blade

Criar:
- `resources/views/layouts/hub.blade.php` — layout da homepage
- `resources/views/layouts/minisite.blade.php` — ver `REFERENCES/ARCHITECTURE.md`

### 10. Homepage (`resources/views/hub/home.blade.php`)

Implementar o design do wireframe `clinky-homepage.html` em Blade + Tailwind.
A homepage deve:
- Ter SEO completo (ver `REFERENCES/SEO.md`)
- Listar os mini-sites em grid 2 colunas mobile-first
- Card destaque (featured) para o mini-site em destaque
- Badge "Em Breve" para sites `live: false`
- Dark mode nativo

### 11. Componentes Blade

Criar todos os componentes de `REFERENCES/COMPONENTS.md`:
```bash
php artisan make:component ShareBar --view
php artisan make:component ResultCard --view
php artisan make:component SiteHeader --view
php artisan make:component CounterBadge --view
```

### 12. Filament Admin

```bash
php artisan filament:install --panels
php artisan make:filament-resource SiteVisit --simple
```

Criar widgets do dashboard:
- `TotalVisitsWidget`
- `TopSiteWidget`

### 13. SEO Global

- `public/robots.txt` gerado por `RobotsController`
- `public/images/og/default.png` — OG image padrão do hub (1200×630)
- `resources/views/hub/sitemap.blade.php` — template XML

### 14. Tailwind + Vite

`vite.config.js`:
```javascript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
        tailwindcss(),
    ],
})
```

### 15. GitHub + Forge

- Criar repo `clinky-cc` no GitHub
- Adicionar ao Forge como novo site em `clinky.cc`
- Configurar wildcard DNS `*.clinky.cc → IP do servidor`
- SSL Let's Encrypt wildcard
- Configurar deploy script com `npm run build` + `php artisan migrate --force`

---

## Resultado esperado

Após executar este prompt:
- `clinky.cc` está online com a homepage e lista de mini-sites
- Admin Filament acessível em `clinky.cc/admin`
- Routing por subdomínio configurado e pronto para receber mini-sites
- Deploy automático via GitHub Actions configurado
