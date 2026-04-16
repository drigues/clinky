# PROMPT 02 — Aperta o Botão

## Objectivo
`botao.clinky.cc` — um botão vermelho gigante com contador global. Nada mais. A psicologia faz o resto.

## Lê primeiro: SKILL.md, REFERENCES/ARCHITECTURE.md, REFERENCES/SEO.md, REFERENCES/PRIVACY.md

---

## SEO
```php
'title'       => 'Aperta o Botão — Já Apertaram ' . $total . ' Vezes',
'description' => 'Um botão. Sem explicação. Sem propósito. Já apertaram milhões de vezes. Qual é a tua resistência?',
'og_title'    => '🔴 Aperta o Botão — ' . number_format($total) . ' pessoas não resistiram',
'og_image'    => asset('images/og/botao.png'),
'canonical'   => 'https://botao.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('botao.' . config('app.base_domain'))->group(function () {
    Route::get('/', [BotaoController::class, 'index'])->name('botao.index');
    Route::post('/pressionar', [BotaoController::class, 'pressionar'])->name('botao.pressionar');
    Route::get('/total', [BotaoController::class, 'total'])->name('botao.total');
});
```

### Controller `BotaoController`

- `index()` — devolve view com total actual de cliques
- `pressionar()` — incrementa contador na DB, devolve JSON com novo total
- `total()` — devolve JSON com total (para polling em tempo real)

**DB:** tabela `button_presses` com coluna `total` (uma única linha). Usar `firstOrCreate` + `increment`.

**PRIVACIDADE:** Só guarda o contador global. Zero dados do utilizador.

### View `sites/botao/index.blade.php`

**UX obrigatória:**
1. Botão vermelho gigante (`w-48 h-48` no mobile) centrado no ecrã
2. Texto acima: "Já apertaram" + contador animado
3. Após apertar: número incrementa com animação (escala + bounce)
4. Texto de reforço que muda aleatoriamente:
   ```javascript
   const mensagens = [
       "Porquê? Só porque sim.",
       "Já não podes parar.",
       "O botão agradece.",
       "Mais uma vez?",
       "Definitivamente não era necessário.",
       "E agora? Vai lá apertar outra vez.",
   ];
   ```
5. Após 1ª vez: share bar aparece com texto "Já apertei o botão. Quantas vezes vais aguentar sem apertar?"

**Alpine.js `botao()` function:**
```javascript
function botao() {
    return {
        total: initialTotal,  // passado pelo blade
        loading: false,
        pressionado: false,
        mensagem: '',
        mensagens: [...],
        async pressionar() {
            this.loading = true;
            const res = await fetch('/pressionar', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
            const data = await res.json();
            this.total = data.total;
            this.pressionado = true;
            this.mensagem = this.mensagens[Math.floor(Math.random() * this.mensagens.length)];
            this.loading = false;
            // Animação no botão
            this.$refs.botao.classList.add('scale-90');
            setTimeout(() => this.$refs.botao.classList.remove('scale-90'), 150);
        }
    }
}
```

**Polling passivo:** a cada 10s, se a página está visível, faz GET `/total` e actualiza o contador. Cria a ilusão de actividade global.

---

## Resultado esperado
- Botão funcional com contador real
- Polling passivo cria sensação viral
- Share após primeiro clique
- Sem dados de utilizador armazenados

---

---

# PROMPT 03 — Nomeador de Grupos de WhatsApp

## Objectivo
`nomeador.clinky.cc` — gera nomes épicos para grupos de WhatsApp.

## SEO
```php
'title'       => 'Nomeador de Grupos — Nomes Épicos para o Teu WhatsApp',
'description' => 'Chega de grupos chamados "Família 🏠". Gera o nome perfeito para o teu grupo de WhatsApp.',
'og_title'    => '💬 O melhor nome de grupo que algures existiu',
'canonical'   => 'https://nomeador.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('nomeador.' . config('app.base_domain'))->group(function () {
    Route::get('/', [NomeadorController::class, 'index'])->name('nomeador.index');
    Route::post('/gerar', [NomeadorController::class, 'gerar'])->name('nomeador.gerar');
});
```

### Controller `NomeadorController`

**Sem Claude API** — usa lista curada em JSON.

Lista de nomes por categoria (mínimo 20 por categoria):

```php
private array $nomes = [
    'familia' => [
        'As Tias do Apocalipse',
        'Tribunal de Família',
        'Alerta Familiar',
        'A Família Que Deus Esqueceu',
        'Tios Sem Filtro',
        'Grupo do Drama Familiar',
        'Os Parentes de Quarentena',
        'DNA Questionável',
        'Herança em Risco',
        // ... 20+ nomes
    ],
    'trabalho' => [
        'Reunião que Devia Ser Email',
        'Stress Colectivo',
        'Sobreviventes da Segunda-feira',
        'Grupo do Mimimi',
        'Reunião às 17:59',
        'O Verdadeiro Trabalho',
        // ...
    ],
    'amigos' => [
        'Os Sem Planos Mas Sempre Juntos',
        'Grupo de Apoio ao Preguiçoso',
        'Saímos Mas Não Saímos',
        'Desculpas Colectivas',
        // ...
    ],
    'casal' => [
        'Amor e Discussão',
        'Eles/Elas Não Sabem',
        'Queixinhas',
        // ...
    ],
];
```

`gerar()` — recebe categoria, devolve 3 sugestões aleatórias.

### View `sites/nomeador/index.blade.php`

**UX:**
1. Escolhe categoria: Família, Trabalho, Amigos, Casal, Vizinhos, Escola
2. Clica "Gerar Nomes"
3. Aparece 3 cards com nomes, cada um com botão "Copiar"
4. Botão "Mais 3" gera novos
5. Share bar: "Encontrei o nome perfeito para o grupo!"

**Cor de acento:** `pink`

**Copiar para clipboard:**
```javascript
copiar(nome) {
    navigator.clipboard.writeText(nome);
    // Feedback visual: botão fica "✓ Copiado!" por 2s
}
```

---

## Resultado esperado
- Gera nomes sem Claude API (zero custo)
- Copiar nome com 1 toque
- Share directo para WhatsApp

---

---

# PROMPT 04 — Horóscopo Inútil

## Objectivo
`horoscopo.clinky.cc` — horóscopo diário que não diz absolutamente nada útil.

## SEO
```php
'title'       => 'Horóscopo Inútil — O Teu Futuro em Palavras Que Não Significam Nada',
'description' => 'O horóscopo mais honesto do mundo. Hoje é ' . now()->locale('pt')->dayName . '. As estrelas não sabem mais do que isso.',
'og_title'    => '🔮 Horóscopo de hoje: as estrelas encolheram os ombros',
'canonical'   => 'https://horoscopo.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('horoscopo.' . config('app.base_domain'))->group(function () {
    Route::get('/', [HoroscopoController::class, 'index'])->name('horoscopo.index');
    Route::get('/{signo}', [HoroscopoController::class, 'signo'])->name('horoscopo.signo');
});
```

### Controller `HoroscopoController`

**Sem Claude API** — algoritmo determinístico (mesmo signo + mesma data = mesmo horóscopo).

```php
private function gerarPrevisao(string $signo, string $data): string
{
    // Seed: signo + data → sempre o mesmo resultado para o mesmo dia
    $seed = crc32($signo . $data);
    srand($seed);

    $inicio = [
        "Hoje é {$data}.",
        "As estrelas repararam em ti hoje.",
        "O universo processou o teu signo.",
        "Marte está a fazer o que Marte faz.",
        ucfirst($signo) . ": o cosmos tomou nota.",
    ];

    $meio = [
        "Algo pode ou não acontecer.",
        "Cuidado com as {$this->diaDaSemana()}.",
        "Uma pessoa que conheces pode falar contigo.",
        "Considera as tuas opções antes de as ignorares.",
        "O teu telemóvel vai tocar. Ou não.",
    ];

    $fim = [
        "Número da sorte: " . (($seed % 97) + 1) . ".",
        "Cor do dia: " . $this->corAleatoria($seed) . ".",
        "Nível de estrelas: " . str_repeat('⭐', ($seed % 4) + 1) . ".",
        "Previsão com " . (($seed % 30) + 70) . "% de certeza.",
    ];

    return $inicio[array_rand($inicio)] . ' ' .
           $meio[array_rand($meio)] . ' ' .
           $fim[array_rand($fim)];
}
```

### View `sites/horoscopo/index.blade.php`

**UX:**
1. Grelha 4×3 com todos os 12 signos (emojis + nome)
2. Utilizador clica no seu signo
3. Página `/aries`, `/touro`, etc. mostra a previsão do dia
4. A previsão muda a cada dia (mesmo signo)
5. Share: "O meu horóscopo de hoje diz: '{previsão}'"

**Cor de acento:** `purple`

**Signos e emojis:**
```php
$signos = [
    'aries' => ['♈', 'Áries', '21 Mar – 19 Abr'],
    'touro' => ['♉', 'Touro', '20 Abr – 20 Mai'],
    // ...todos os 12
];
```

---

---

# PROMPT 05 — O Que o Teu Nome Diz?

## Objectivo
`nome.clinky.cc` — o utilizador escreve o nome, recebe uma "análise" completamente inventada mas convincente.

## SEO
```php
'title'       => 'O Que o Teu Nome Diz? — Análise Científica* de Personalidade',
'description' => 'Descobre o que o teu nome revela sobre ti. *Totalmente inventado, mas surpreendentemente preciso.',
'og_title'    => '🧬 Descobri o que o meu nome diz sobre mim',
'canonical'   => 'https://nome.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('nome.' . config('app.base_domain'))->group(function () {
    Route::get('/', [AnalisadorNomeController::class, 'index'])->name('nome.index');
    Route::post('/analisar', [AnalisadorNomeController::class, 'analisar'])->name('nome.analisar');
});
```

### Controller `AnalisadorNomeController`

**Com Claude API** — análise personalizada por nome.

```php
public function analisar(Request $request)
{
    $request->validate(['nome' => 'required|string|max:50|regex:/^[\p{L}\s]+$/u']);

    $nome = trim($request->nome);
    // Não guardamos o nome — processado e descartado (PRIVACY.md)

    $systemPrompt = <<<PROMPT
    És o Analisador de Nomes — um "cientista" completamente inventado que analisa nomes com pseudociência divertida.
    Dado um nome, gera uma análise curta (3-4 frases) no formato:
    - Percentagem de uma qualidade absurda (ex: "73% caos")
    - Percentagem de outra qualidade (ex: "27% potencial")
    - Uma característica "científica" do nome
    - Uma previsão ridícula mas específica

    Tom: confiante, científico-falso, divertido.
    Escreve em português de Portugal.
    Responde APENAS com a análise, sem introdução, sem explicações.
    PROMPT;

    $analise = $this->claude->generate($systemPrompt, "Analisa o nome: {$nome}");

    if (!$analise) {
        $analise = "O nosso laboratório de análise de nomes entrou em colapso quântico. Tenta novamente.";
    }

    AnalyticsService::event('nome', 'analyze');
    // PRIVACIDADE: $nome NÃO é guardado

    return response()->json(['analise' => $analise, 'nome' => $nome]);
}
```

### View `sites/nome/index.blade.php`

**UX:**
1. Campo de texto: "Escreve o teu nome"
2. Botão "Analisar"
3. Loading com texto: "A analisar as letras do teu nome..."
4. Resultado aparece num card estilo "relatório científico"
5. Asterisco pequeno: "* Análise 100% inventada. Surpreendentemente precisa."
6. Share: "Descobri que '{nome}' significa que sou {primeira frase da análise}"

**Cor de acento:** `teal`

**Input — sem guardar:**
```html
<input type="text"
       x-model="nome"
       placeholder="O teu nome aqui"
       maxlength="50"
       autocomplete="off"
       autocorrect="off"
       class="...">
```

---

## Resultado esperado (prompts 02-05)
- 4 mini-sites funcionais
- 2 com Claude API (01, 05), 2 sem (03, 04), 1 com DB (02)
- Todos com SEO completo, share funcional, dark mode
