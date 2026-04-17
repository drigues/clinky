# PROMPT 12 — Nada

## Conceito
**Gatilho psicológico:** Curiosidade pura + meta-humor
Uma página completamente em branco (ou quase) com um contador de pessoas que estão agora a ver "nada". O paradoxo: quanto mais pessoas partilharem, mais o "nada" se torna "algo". É auto-referencial, é absurdo, é exatamente o tipo de coisa que se partilha por ser indefensável.

**Viral porque:** "Enviei-te um link de nada. Sério. É literalmente nada. Abre." — esta frase já é irresistível.

## SEO
```php
$seo = [
    'title'          => 'Nada',
    'description'    => 'Nada aqui. Mesmo nada. E mesmo assim estás a ler isto.',
    'og_title'       => 'Nada.',
    'og_description' => 'Não há nada aqui. 4.832 pessoas já viram o nada hoje.',
    'og_image'       => asset('images/og/nada.png'),
    'canonical'      => route('nada.index'),
];
```

## UX — Fluxo do utilizador
1. Página quase em branco — fundo `#0a0a0a`
2. Ao centro, apenas: a palavra **"Nada."** em tipografia grande
3. Abaixo, muito pequeno: "X pessoas estão agora a ver isto"
4. Contador actualiza em tempo real via polling
5. Ao fim de 5 segundos aparece, com fade: "Continuas aqui."
6. Ao fim de 15 segundos: "Interessante."
7. Ao fim de 30 segundos: "Não há mais nada para além disto."
8. Ao fim de 60 segundos: botão de partilha aparece — "Partilhar o nada"
9. Cursor: um ponto. Nada mais.

**Easter egg:** clicar 5 vezes no "Nada." muda para "Quase nada." durante 3 segundos, depois volta.

## Tasks

### Route
```php
Route::prefix('nada')->name('nada.')->group(function () {
    Route::get('/', [NadaController::class, 'index'])->name('index');
    Route::get('/viewers', [NadaController::class, 'viewers'])->name('viewers');
});
```

### Controller `NadaController`
Usa cache para simular viewers em tempo real. Sem Claude API.

```php
public function index()
{
    AnalyticsService::pageView('nada');
    // Seed realista baseado na hora do dia
    $base = 800 + (date('H') * 47) + (date('i') * 3);
    $viewers = Cache::remember('nada_viewers', 30, fn() => $base + rand(-50, 150));

    return view('sites.nada.index', [
        'seo'     => $this->seo(),
        'viewers' => $viewers,
    ]);
}

public function viewers()
{
    // Flutua de forma orgânica — não completamente aleatório
    $base = 800 + (date('H') * 47) + (date('i') * 3);
    $viewers = $base + rand(-80, 200);
    return response()->json(['viewers' => $viewers]);
}
```

### View `resources/views/sites/nada/index.blade.php`

**Alpine.js `nada()` function:**
```javascript
function nada() {
    return {
        viewers: {{ $viewers }},
        tempo: 0,
        clicks: 0,
        titulo: 'Nada.',
        mostrarShare: false,

        init() {
            // Polling de viewers a cada 8s
            setInterval(async () => {
                const r = await fetch('/nada/viewers');
                const d = await r.json();
                this.viewers = d.viewers;
            }, 8000);

            // Mensagens temporais
            setInterval(() => {
                this.tempo++;
            }, 1000);
        },

        get mensagem() {
            if (this.tempo >= 60) { this.mostrarShare = true; return ''; }
            if (this.tempo >= 30) return 'Não há mais nada para além disto.';
            if (this.tempo >= 15) return 'Interessante.';
            if (this.tempo >= 5)  return 'Continuas aqui.';
            return '';
        },

        clicarTitulo() {
            this.clicks++;
            if (this.clicks >= 5) {
                this.titulo = 'Quase nada.';
                setTimeout(() => { this.titulo = 'Nada.'; this.clicks = 0; }, 3000);
            }
        },

        get shareText() {
            return `Enviei-te um link de nada. Sério. É literalmente nada.`;
        }
    }
}
```

**HTML minimalista:**
```html
<div class="min-h-screen bg-[#0a0a0a] flex flex-col items-center justify-center"
     x-data="nada()">

    <h1 @click="clicarTitulo"
        class="text-6xl md:text-8xl font-black text-white cursor-default select-none"
        x-text="titulo">
    </h1>

    <p class="text-zinc-600 text-sm mt-8 tabular-nums">
        <span x-text="viewers.toLocaleString('pt-PT')"></span>
        pessoas estão agora a ver isto
    </p>

    <p class="text-zinc-500 text-sm mt-12 h-6 transition-opacity duration-1000"
       :class="mensagem ? 'opacity-100' : 'opacity-0'"
       x-text="mensagem">
    </p>

</div>
```

**Cor de acento:** nenhuma. Monocromático. É "nada".

**Texto de partilha WhatsApp:**
```
Enviei-te um link de nada. Sério. É literalmente nada.
(mas {X} pessoas já viram)

→ https://clinky.cc/nada
```

## JSON-LD
```json
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Nada",
  "description": "Nada aqui. Mesmo nada.",
  "url": "https://clinky.cc/nada",
  "inLanguage": "pt-PT"
}
```

## OG Image
Fundo completamente preto. Centro: apenas a palavra "Nada." em branco, tipografia bold enorme. Abaixo, muito pequeno e cinzento: "4.832 pessoas já viram". Absolutamente mais nada.
