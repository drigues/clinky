# PROMPT 13 — O Botão Proibido

## Conceito
**Gatilho psicológico:** Variable reward + reacção de proibição (reactance theory)
Dizer "não carregues" é a forma mais eficaz de garantir que alguém carrega. O botão dá resultados aleatórios — às vezes uma mensagem engraçada, às vezes absolutamente nada, às vezes uma repreensão, às vezes um elogio inesperado. O imprevisível é mais viciante que o previsível.

**Viral porque:** "Não carregues no botão deste site" é o melhor texto de partilha possível.

## SEO
```php
$seo = [
    'title'          => 'O Botão Proibido — Não Carregues',
    'description'    => 'Há um botão. Disseram-te para não carregar. O que vais fazer?',
    'og_title'       => '🚫 Não carregues neste botão.',
    'og_description' => 'Sério. Não carregues. Por favor.',
    'og_image'       => asset('images/og/proibido.png'),
    'canonical'      => route('proibido.index'),
];
```

## UX — Fluxo do utilizador
1. Página com aviso: "NÃO CARREGUES NESTE BOTÃO"
2. Botão escuro, discreto, levemente pulsante
3. Ao carregar: resultado aleatório de um pool (ver abaixo)
4. Resultados variam muito — cria expectativa para o próximo
5. Botão "Tentar outra vez" aparece sempre
6. Ao 5.º clique: "Já dissemos para não carregar."
7. Ao 10.º clique: "Sabíamos que ias fazer isto."
8. Share: "Não carregues neste botão. Sério."

## Tasks

### Route
```php
Route::prefix('proibido')->name('proibido.')->group(function () {
    Route::get('/', [ProibidoController::class, 'index'])->name('index');
    Route::post('/carregar', [ProibidoController::class, 'carregar'])->name('carregar');
});
```

### Controller `ProibidoController`
Variable reward — resultados aleatórios com pesos diferentes.

```php
private array $resultados = [
    // Frequentes (peso 3)
    ['tipo' => 'nada',      'texto' => '...', 'peso' => 3],
    ['tipo' => 'repreensao','texto' => 'Dissemos para não carregar.', 'peso' => 3],
    ['tipo' => 'nada2',     'texto' => 'Nada aconteceu. Satisfeito?', 'peso' => 3],

    // Médios (peso 2)
    ['tipo' => 'elogio',    'texto' => 'Tens uma energia muito especial. Não sabemos porquê.', 'peso' => 2],
    ['tipo' => 'filosofia', 'texto' => 'Se uma árvore cair numa floresta e ninguém ouvir, carregaste no botão na mesma.', 'peso' => 2],
    ['tipo' => 'parabens',  'texto' => 'Parabéns. Não ganhou nada.', 'peso' => 2],

    // Raros (peso 1)
    ['tipo' => 'raro',      'texto' => '🎉 Encontraste o resultado raro! (não há prémio)', 'peso' => 1],
    ['tipo' => 'existencial','texto' => 'O botão também te escolheu a ti.', 'peso' => 1],
    ['tipo' => 'silencio',  'texto' => '', 'peso' => 1], // Completamente vazio
];

public function carregar(Request $request)
{
    // Selecção por peso
    $pool = [];
    foreach ($this->resultados as $r) {
        for ($i = 0; $i < $r['peso']; $i++) {
            $pool[] = $r;
        }
    }
    $resultado = $pool[array_rand($pool)];

    AnalyticsService::event('proibido', 'press');
    // PRIVACIDADE: zero dados guardados
    return response()->json($resultado);
}
```

### View `resources/views/sites/proibido/index.blade.php`

**Alpine.js `proibido()` function:**
```javascript
function proibido() {
    return {
        cliques: 0,
        resultado: null,
        loading: false,
        tipo: null,

        get aviso() {
            if (this.cliques >= 10) return 'Sabíamos que ias fazer isto.';
            if (this.cliques >= 5)  return 'Já dissemos para não carregar.';
            if (this.cliques >= 1)  return 'Outra vez?';
            return 'NÃO CARREGUES NESTE BOTÃO.';
        },

        async carregar() {
            this.loading = true;
            this.resultado = null;
            this.cliques++;

            const res = await fetch('/proibido/carregar', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            });
            const data = await res.json();

            await new Promise(r => setTimeout(r, 800)); // suspense
            this.resultado = data.texto;
            this.tipo = data.tipo;
            this.loading = false;
        },

        get shareText() {
            return `Não carregues neste botão. Sério. Por favor não carregues.`;
        }
    }
}
```

**Cor de acento:** vermelho escuro `#8B0000` — sinal de perigo mas discreto

**Texto de partilha WhatsApp:**
```
Não carregues neste botão. Sério.
→ https://clinky.cc/proibido
```

## JSON-LD
```json
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "O Botão Proibido",
  "description": "Há um botão. Disseram-te para não carregar.",
  "url": "https://clinky.cc/proibido",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT"
}
```

## OG Image
Fundo vermelho muito escuro (`#1a0000`). Centro: botão discreto cinzento com texto "NÃO" em cima. Sinal de proibição (círculo vermelho com traço). Tipografia bold. Simples e intrigante.
