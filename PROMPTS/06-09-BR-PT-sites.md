# PROMPT 06 — Bingo do Imigrante

## Objectivo
`bingo.clinky.cc` — cartela de bingo com situações clássicas de brasileiros em Portugal.
Alta virabilidade na comunidade BR em PT.

## SEO
```php
'title'       => 'Bingo do Imigrante Brasileiro em Portugal — Quantos Já Viveste?',
'description' => 'A cartela que todo brasileiro em Portugal vai reconhecer. Marca os quadrados e descobre o teu nível de integração.',
'og_title'    => '🎯 Bingo do Imigrante — quantos já te aconteceram?',
'og_description' => 'Já disseste "vou já" e ficaste 3 horas? Já ficaste confuso com "bicha"? Joga o Bingo!',
'canonical'   => 'https://bingo.clinky.cc',
```
Hreflang PT-PT + PT-BR (ver SEO.md).

---

## Tasks

### Route
```php
Route::domain('bingo.' . config('app.base_domain'))->group(function () {
    Route::get('/', [BingoController::class, 'index'])->name('bingo.index');
});
```

### Controller `BingoController`
Devolve a lista de 25 situações (ou 24 + "FREE"). Sem Claude API, sem DB.

### Lista de situações (25 quadrados)

```php
private array $quadrados = [
    "Já disseste «tá bom» e as pessoas ficaram confusas",
    "Já tentaste pagar com PIX",
    "Já estavas 40 min em «vou já»",
    "Já ligaste o Spotify para ouvir funk e o teu chefe apareceu",
    "Já pediste «suco» e trouxeram «sumo»",
    "Já disseste «bicha» no sentido errado",
    "Já saudaste alguém com «oi» e sentiste o julgamento",
    "Já custou perceber um sotaque do Norte",
    "Já levaste choque no preço da electricidade",
    "Já comparaste o tempo de PT ao do Brasil 10 vezes",
    "Já pediste atendimento e esperaste 45 min",
    "Já ligaste o aquecedor em Julho",
    "Já mandaste áudio de 3 minutos no WhatsApp",
    "Já recebeste carta da Finanças e não percebeste nada",
    "Já disseste «não tem não» e alguém ficou com cara estranha",
    "Já saíste de casa com guarda-chuva e estava sol",
    "Já pagaste taxa de emissão do cartão de cidadão",
    "Já tentaste pedir churrasco e recebeste algo diferente",
    "FREE — És imigrante em Portugal",
    "Já comeste pastel de nata e achaste que era outro",
    "Já pediste «água com gás» e vieram com cara de «porquê?»",
    "Já explicaste que o Brasil tem mais de um estado",
    "Já enviaste dinheiro para o Brasil via Remessa Online",
    "Já saudaste alguém com 2 beijinhos e foi estranho",
    "Já faltaste a um evento por causa do SEF/AIMA",
];
```

### View `sites/bingo/index.blade.php`

**UX:**
1. Título + descrição
2. Grelha 5×5 com os 25 quadrados
3. Cada quadrado: clicável, quando marcado fica com cor + check
4. Contador: "X/25 situações vividas"
5. Ao completar linha, coluna ou diagonal: alerta "BINGO! Estás integrado 🎉"
6. Ao marcar 20+: "Já és meio português"
7. Botão share que gera texto dinâmico baseado no resultado

**Estado guardado em `localStorage`** (só para manter marcações na sessão — ver PRIVACY.md):
```javascript
// localStorage só guarda array de índices marcados — zero PII
localStorage.setItem('bingo_marks', JSON.stringify(this.marcados));
```

**Alpine.js `bingo()` function:**
```javascript
function bingo() {
    return {
        marcados: JSON.parse(localStorage.getItem('bingo_marks') || '[]'),
        get total() { return this.marcados.length; },
        get shareText() {
            const t = this.total;
            if (t >= 20) return `Fiz ${t}/25 no Bingo do Imigrante! Já sou meio português 🇵🇹`;
            if (t >= 10) return `Fiz ${t}/25 no Bingo do Imigrante! Ainda estou a integrar 😅`;
            return `Fiz ${t}/25 no Bingo do Imigrante! Acabei de chegar 🇧🇷`;
        },
        toggle(idx) {
            if (this.marcados.includes(idx)) {
                this.marcados = this.marcados.filter(i => i !== idx);
            } else {
                this.marcados.push(idx);
            }
            localStorage.setItem('bingo_marks', JSON.stringify(this.marcados));
            this.verificarBingo();
        },
        bingo: false,
        verificarBingo() {
            // Verificar linhas, colunas, diagonais
            const m = this.marcados;
            const linha = Array.from({length:5}, (_,r) => [0,1,2,3,4].map(c => r*5+c).every(i => m.includes(i)));
            // ... lógica completa de bingo
            this.bingo = linha.some(Boolean);
        }
    }
}
```

**Cor de acento:** `yellow`

---

---

# PROMPT 07 — Conversor PT ↔ BR

## Objectivo
`conversor.clinky.cc` — dicionário interactivo e divertido de palavras que significam coisas diferentes em Portugal e no Brasil.

## SEO
```php
'title'       => 'Conversor PT ↔ BR — As Palavras Que Nos Separam (e Unem)',
'description' => 'Bicha ou fila? Elevador ou lift? Pequeno-almoço ou café da manhã? O guia definitivo das diferenças.',
'og_title'    => '🔁 Bicha em PT = Fila em BR. Descobre mais!',
'canonical'   => 'https://conversor.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('conversor.' . config('app.base_domain'))->group(function () {
    Route::get('/', [ConversorController::class, 'index'])->name('conversor.index');
    Route::get('/pesquisar', [ConversorController::class, 'pesquisar'])->name('conversor.pesquisar');
});
```

### Controller `ConversorController`
Sem Claude API. Dicionário curado em PHP array ou JSON.

### Dicionário (mínimo 50 entradas)

```php
private array $dicionario = [
    ['pt' => 'bicha', 'br' => 'fila', 'emoji' => '🧍', 'exemplo_pt' => 'Há uma bicha enorme no supermercado', 'exemplo_br' => 'A fila do banco está enorme'],
    ['pt' => 'elevador', 'br' => 'elevador', 'emoji' => '🛗', 'nota' => 'Igual! Mas em PT também dizem "lift"'],
    ['pt' => 'pequeno-almoço', 'br' => 'café da manhã', 'emoji' => '☕', 'exemplo_pt' => 'Tomei o pequeno-almoço', 'exemplo_br' => 'Tomei café da manhã'],
    ['pt' => 'autocarro', 'br' => 'ônibus', 'emoji' => '🚌'],
    ['pt' => 'telemóvel', 'br' => 'celular', 'emoji' => '📱'],
    ['pt' => 'frigorífico', 'br' => 'geladeira', 'emoji' => '🧊'],
    ['pt' => 'casa de banho', 'br' => 'banheiro', 'emoji' => '🚿'],
    ['pt' => 'sandes', 'br' => 'sanduíche', 'emoji' => '🥪'],
    ['pt' => 'sumo', 'br' => 'suco', 'emoji' => '🍊'],
    // ... 50+ entradas
];
```

### View `sites/conversor/index.blade.php`

**UX:**
1. Header com bandeiras 🇵🇹 ↔ 🇧🇷
2. Campo de pesquisa em tempo real (Alpine.js filtro no client)
3. Cards em grelha: cada card mostra PT | BR + emoji + exemplos opcionais
4. Toggle: "Ver em modo PT→BR" vs "Ver em modo BR→PT"
5. Palavra aleatória destacada no topo: "Palavra do dia"
6. Share: "Sabia que [palavra PT] em português do Brasil é [palavra BR]? 🇵🇹🇧🇷"

**Cor de acento:** `blue`

**Pesquisa client-side (sem server calls):**
```javascript
get filtrados() {
    if (!this.pesquisa) return this.dicionario;
    const q = this.pesquisa.toLowerCase();
    return this.dicionario.filter(e =>
        e.pt.includes(q) || e.br.includes(q)
    );
}
```

---

---

# PROMPT 08 — Sou mais BR ou PT?

## Objectivo
`quiz.clinky.cc` — quiz de 5 perguntas para descobrir se és mais brasileiro ou português. Resultado partilhável.

## SEO
```php
'title'       => 'Sou Mais Brasileiro ou Português? — Quiz de 5 Perguntas',
'description' => 'Depois de anos em Portugal (ou no Brasil), quanto do outro país já absorbeste? Faz o quiz e descobre.',
'og_title'    => '🤔 Fiz o teste: sou X% português e Y% brasileiro',
'canonical'   => 'https://quiz.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('quiz.' . config('app.base_domain'))->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('quiz.index');
});
```

### Controller `QuizController`
Sem Claude API, sem DB. Quiz 100% client-side.

### Perguntas (10 perguntas, usa 5 aleatórias por sessão)

```php
// No controller, passa as perguntas ao blade
// O quiz é processado no frontend (Alpine.js)
$perguntas = [
    [
        'pergunta' => 'Quando marcas encontro às 15h, a que horas chegas?',
        'opcoes' => [
            ['texto' => '15h em ponto', 'pt' => 10, 'br' => 0],
            ['texto' => '15h10, peço desculpa', 'pt' => 7, 'br' => 3],
            ['texto' => '15h30, "já vou"', 'pt' => 2, 'br' => 8],
            ['texto' => '16h, mando áudio a explicar', 'pt' => 0, 'br' => 10],
        ]
    ],
    [
        'pergunta' => 'O que é uma «bicha» para ti?',
        'opcoes' => [
            ['texto' => 'Uma fila de pessoas', 'pt' => 10, 'br' => 0],
            ['texto' => 'Um insecto', 'pt' => 0, 'br' => 5],
            ['texto' => 'Depende do contexto...', 'pt' => 5, 'br' => 5],
            ['texto' => 'Ainda me confundo', 'pt' => 2, 'br' => 8],
        ]
    ],
    // ... 10 perguntas total
];
```

### View `sites/quiz/index.blade.php`

**UX:**
1. Splash: "Sou mais BR ou PT?" + botão "Começar"
2. Uma pergunta de cada vez (stepper)
3. Barra de progresso (1/5, 2/5...)
4. Ao terminar: resultado em percentagem com emoji
   - 80%+ PT: "Já pedes café sem açúcar e achas normal"
   - 80%+ BR: "O fado ainda não te convenceu"
   - 40-60% cada: "És o imigrante integrado perfeito 🎉"
5. Resultado gerado dinamicamente para share:
   ```
   "Fiz o teste e sou 65% português e 35% brasileiro! 🇵🇹🇧🇷
   Descobre o teu resultado: https://quiz.clinky.cc"
   ```

**Cor de acento:** `orange` (bandeiras)

---

---

# PROMPT 09 — Tradutor Corporativo

## Objectivo
`corporativo.clinky.cc` — traduz jargão corporativo para português real.

## SEO
```php
'title'       => 'Tradutor Corporativo — O Que Significam Realmente as Palavras do Escritório',
'description' => '"Synergize deliverables" = fazer o costume mas com slide. Descobre o que o teu chefe está mesmo a dizer.',
'og_title'    => '💼 "Vamos alinhar" = reunião desnecessária. Descobre mais!',
'canonical'   => 'https://corporativo.clinky.cc',
```

---

## Tasks

### Route
```php
Route::domain('corporativo.' . config('app.base_domain'))->group(function () {
    Route::get('/', [CorporativoController::class, 'index'])->name('corporativo.index');
    Route::post('/traduzir', [CorporativoController::class, 'traduzir'])->name('corporativo.traduzir');
});
```

### Controller `CorporativoController`

**Com Claude API** para input livre + dicionário curado para termos comuns.

```php
public function traduzir(Request $request)
{
    $request->validate(['texto' => 'required|string|max:300']);
    $texto = $request->texto;
    // Não guardamos — PRIVACY.md

    // Primeiro: verificar no dicionário curado
    foreach ($this->dicionario as $termo => $traducao) {
        if (stripos($texto, $termo) !== false) {
            return response()->json(['traducao' => $traducao, 'fonte' => 'dicionario']);
        }
    }

    // Se não está no dicionário: usar Claude
    $systemPrompt = <<<PROMPT
    És o Tradutor Corporativo — traduz jargão de escritório para português real e directo.
    Exemplos:
    - "vamos alinhar" = "vamos ter uma reunião desnecessária"
    - "synergize" = "fazer o costume mas com slide bonito"
    - "fora da caixa" = "ninguém sabe o que isto significa"
    - "quick win" = "coisa fácil que fingimos ser estratégica"

    Dado um texto com jargão, devolve a tradução honesta em 1-2 frases.
    Tom: sarcástico mas amigável, como um colega de trabalho honesto.
    Escreve em português de Portugal.
    Responde APENAS com a tradução, sem introdução.
    PROMPT;

    $traducao = $this->claude->generate($systemPrompt, $texto, 150);
    AnalyticsService::event('corporativo', 'translate');

    return response()->json(['traducao' => $traducao ?? 'O nosso tradutor também está em "modo reunião". Tenta noutra altura.']);
}

private array $dicionario = [
    'vamos alinhar'          => '🗓️ Vamos ter uma reunião que devia ser um email',
    'synergy'                => '🤝 Fazer o costume mas com slide de PowerPoint',
    'fora da caixa'          => '📦 Ninguém sabe o que isto significa, incluindo quem disse',
    'quick win'              => '🏆 Coisa pequena que fingimos ser estratégica',
    'deep dive'              => '🤿 Reunião mais longa sobre o mesmo assunto',
    'move the needle'        => '📊 Fazer algo que apareça no dashboard do chefe',
    'baixar o nível do lixo' => '🗑️ Ir à reunião sem preparação',
    'takeaways'              => '📝 O que devias ter dito antes da reunião',
    'bandwidth'              => '📡 Tempo que não tens mas vão pedir na mesma',
    'circling back'          => '🔄 Fazer followup de algo que ninguém fez',
    // ...20+ termos
];
```

### View `sites/corporativo/index.blade.php`

**UX:**
1. Exemplos de termos populares (clicáveis para preencher o campo)
2. Campo de texto livre: "Escreve o jargão aqui"
3. Botão "Traduzir para Português Real"
4. Resultado com emoji + tradução
5. Dicionário de termos mais populares abaixo (estático, sempre visível)

**Cor de acento:** `red`
