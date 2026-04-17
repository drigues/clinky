# PROMPT 11 — Barra de Progresso da Vida

## Conceito
**Gatilho psicológico:** Closure compulsion + ansiedade existencial
Uma barra de progresso que mostra exactamente que percentagem da tua vida (assumindo esperança de vida média) já passou. Actualiza em tempo real, ao segundo. Não podes "completar" — só consegues ver quanto resta. O desconforto É o produto. Partilha-se por angústia colectiva.

**Viral porque:** toda a gente quer saber e ao mesmo tempo não quer saber. O resultado é sempre perturbador independentemente da idade.

## SEO
```php
$seo = [
    'title'          => 'Barra de Progresso da Vida — Quanto já passou?',
    'description'    => 'Introduz a tua data de nascimento. Vê exactamente que % da tua vida já passou. Actualiza em tempo real. Não é motivacional.',
    'og_title'       => '⏳ A minha vida está X% completa',
    'og_description' => 'Um número que não queres ver mas não consegues ignorar.',
    'og_image'       => asset('images/og/progresso.png'),
    'canonical'      => route('progresso.index'),
];
```

## UX — Fluxo do utilizador
1. Página limpa, fundo escuro, só uma pergunta: "Quando nasceste?"
2. Input de data de nascimento (mês/ano — não precisamos do dia exacto)
3. Botão "Ver o progresso"
4. Barra de progresso anima de 0% até ao valor real (dramático)
5. Número grande: "A tua vida está **X.XXXXX%** completa"
6. Sub-texto: "Tens aproximadamente Y anos, Z meses e W dias"
7. Actualização em tempo real — os decimais mudam ao segundo
8. Frase contextual baseada na idade
9. Share: "A minha vida está X% completa. E a tua?"

**PRIVACIDADE:** A data de nascimento é calculada localmente no browser (JavaScript puro). Não é enviada ao servidor. Zero dados guardados.

## Tasks

### Route
```php
Route::prefix('progresso')->name('progresso.')->group(function () {
    Route::get('/', [ProgressoController::class, 'index'])->name('index');
});
```

### Controller `ProgressoController`
Zero dados de utilizador. Toda a lógica no frontend.

```php
public function index()
{
    AnalyticsService::pageView('progresso');
    return view('sites.progresso.index', ['seo' => $this->seo()]);
}
```

### View `resources/views/sites/progresso/index.blade.php`

**Alpine.js `progresso()` function:**
```javascript
function progresso() {
    return {
        // Input — nunca enviado ao servidor
        mesNascimento: '',
        anoNascimento: '',
        calculado: false,
        percentagem: 0,
        percentagemDisplay: '0.000000',
        intervalo: null,

        // Esperança de vida média (ajustável)
        esperancaVida: 79,

        calcular() {
            if (!this.mesNascimento || !this.anoNascimento) return;

            // Tudo calculado localmente — zero dados ao servidor
            const nascimento = new Date(this.anoNascimento, this.mesNascimento - 1, 15);
            const agora = new Date();
            const morte = new Date(nascimento);
            morte.setFullYear(morte.getFullYear() + this.esperancaVida);

            const totalMs = morte - nascimento;
            const passadoMs = agora - nascimento;
            this.percentagem = Math.min((passadoMs / totalMs) * 100, 100);
            this.calculado = true;

            // Animar a barra
            this.animar();

            // Actualizar decimais em tempo real
            this.intervalo = setInterval(() => {
                const agora2 = new Date();
                const passado2 = agora2 - nascimento;
                const p = Math.min((passado2 / totalMs) * 100, 100);
                this.percentagemDisplay = p.toFixed(6);
            }, 100);
        },

        animar() {
            // CSS transition na barra já trata disto
            setTimeout(() => {
                this.percentagemDisplay = this.percentagem.toFixed(6);
            }, 100);
        },

        get fraseContextual() {
            const p = this.percentagem;
            if (p < 25) return "Ainda tens muito por descobrir. Ou talvez não.";
            if (p < 40) return "Os melhores anos? Dependendo de quem perguntas.";
            if (p < 50) return "Ainda não chegaste a meio. Respira.";
            if (p < 60) return "Passaste do meio. Sem alarme necessário.";
            if (p < 75) return "Estatisticamente, já viveste mais do que o que falta.";
            if (p < 90) return "O tempo é uma ilusão. A barra não.";
            return "Parabéns por chegares até aqui. A sério.";
        },

        get shareText() {
            return `A minha vida está ${parseFloat(this.percentagemDisplay).toFixed(1)}% completa ⏳\n\nE a tua?`;
        },

        destruir() {
            if (this.intervalo) clearInterval(this.intervalo);
        }
    }
}
```

**HTML da barra:**
```html
<div class="w-full bg-zinc-800 rounded-full h-3 overflow-hidden">
    <div class="h-full rounded-full transition-all duration-[2000ms] ease-out"
         :style="`width: ${percentagem}%; background: linear-gradient(90deg, #c8f135, #ff6b00)`">
    </div>
</div>
<p class="text-5xl font-black tabular-nums mt-6"
   x-text="percentagemDisplay + '%'">
</p>
```

**Frase após resultado:**
- Mostrar estimativa de "dias restantes" (sem o número de morte — apenas "aproximadamente X dias")
- Nota de rodapé: "* Baseado em esperança de vida média de 79 anos. O teu resultado pode variar."

**Cor de acento:** `lime → orange` (gradiente na barra)

**Texto de partilha WhatsApp:**
```
A minha vida está {X}% completa ⏳

E a tua? → https://clinky.cc/progresso
```

## JSON-LD
```json
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Barra de Progresso da Vida",
  "description": "Quanto da tua vida já passou? Actualiza em tempo real.",
  "url": "https://clinky.cc/progresso",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT"
}
```

## OG Image
Fundo preto, barra de progresso horizontal a meio da imagem preenchida a ~67%, cor gradiente lime→laranja, número grande "67.431821%" em branco, subtexto muted "e a tua?". Minimalista, impactante.
