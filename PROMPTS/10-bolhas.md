# PROMPT 10 — Rebenta as Bolhas

## Conceito
**Gatilho psicológico:** Satisfação sensorial imediata (ASMR visual)
O cérebro liberta dopamina ao completar micro-acções repetitivas. Rebentar bolhas de plástico é um dos exemplos mais estudados de satisfação sensorial sem propósito. Versão digital: infinita, sem fim, impossível parar.

**Viral porque:** ninguém consegue partilhar sem dizer "cuidado que vicia" — e isso já é o texto de partilha perfeito.

## SEO
```php
$seo = [
    'title'          => 'Rebenta as Bolhas — Satisfação Garantida ou Dinheiro de Volta',
    'description'    => 'Bolhas infinitas para rebentar. Sem propósito. Sem fim. Completamente viciante. Não digas que não avisámos.',
    'og_title'       => '🫧 Rebenta as Bolhas — impossível parar',
    'og_description' => 'Já rebentaste 0 bolhas. Daqui a 5 minutos esse número vai ser muito maior.',
    'og_image'       => asset('images/og/bolhas.png'),
    'canonical'      => route('bolhas.index'),
];
```

## UX — Fluxo do utilizador
1. Entra na página — grelha 8×12 de bolhas translúcidas a pulsar levemente
2. Toca/clica numa bolha — animação de pop (escala 0 + fade) + som visual (ripple)
3. Bolha rebentada deixa círculo vazio cinzento
4. Contador no topo: "Rebentaste X bolhas"
5. Quando rebenta todas: nova grelha aparece automaticamente (infinite)
6. Ao chegar a 50 bolhas: "Não há nada aqui. E mesmo assim continuaste."
7. Share bar aparece após 10 bolhas rebentadas

## Tasks

### Route
```php
Route::prefix('bolhas')->name('bolhas.')->group(function () {
    Route::get('/', [BolhasController::class, 'index'])->name('index');
});
```

### Controller `BolhasController`
Sem Claude API, sem DB. Página estática com Alpine.js.

```php
public function index()
{
    AnalyticsService::pageView('bolhas');
    return view('sites.bolhas.index', ['seo' => $this->seo()]);
}
```

### View `resources/views/sites/bolhas/index.blade.php`

**Alpine.js `bolhas()` function:**
```javascript
function bolhas() {
    return {
        total: 100,           // bolhas por grelha
        rebentadas: [],       // índices rebentados
        contador: 0,          // total histórico
        mostrarShare: false,

        get todasRebentadas() {
            return this.rebentadas.length === this.total;
        },

        rebentar(idx) {
            if (this.rebentadas.includes(idx)) return;
            this.rebentadas.push(idx);
            this.contador++;
            if (this.contador === 10) this.mostrarShare = true;
            if (this.todasRebentadas) {
                setTimeout(() => { this.rebentadas = []; }, 600);
            }
        },

        get shareText() {
            return `Já rebentei ${this.contador} bolhas neste site inútil e não consigo parar 🫧`;
        }
    }
}
```

**HTML da grelha:**
```html
<div class="grid gap-2"
     style="grid-template-columns: repeat(8, 1fr)"
     x-data="bolhas()">

    <template x-for="i in total" :key="i">
        <button
            @click="rebentar(i)"
            :class="rebentadas.includes(i) ? 'opacity-20 scale-0' : 'scale-100'"
            class="aspect-square rounded-full transition-all duration-200
                   bg-gradient-to-br from-blue-200/40 to-blue-400/20
                   border border-blue-300/30 hover:scale-110 active:scale-95
                   shadow-inner cursor-pointer">
        </button>
    </template>
</div>
```

**Mensagens progressivas** (mostrar baseado no contador):
- 10: "Isto não vai a lado nenhum. E mesmo assim..."
- 50: "Nenhum problema teu foi resolvido."
- 100: "Parabéns. Rebentaste 100 bolhas. A tua vida é igual."
- 200: "Estás bem?"
- 500: "Procura ajuda."

**Cor de acento:** `blue` / `cyan`

**Texto de partilha WhatsApp:**
```
Já rebentei {X} bolhas neste site completamente inútil e não consigo parar 🫧

Tenta tu: https://clinky.cc/bolhas
```

## JSON-LD
```json
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Rebenta as Bolhas",
  "description": "Bolhas infinitas para rebentar. Completamente viciante.",
  "url": "https://clinky.cc/bolhas",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT"
}
```

## OG Image
Fundo `#001a2e`, bolhas translúcidas azuis/ciano de vários tamanhos espalhadas, algumas já rebentadas (círculos vazios), luz suave. Vibe ASMR visual.
