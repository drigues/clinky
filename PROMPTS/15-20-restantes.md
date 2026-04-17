# PROMPT 15 — O Oráculo

## Conceito
**Gatilho psicológico:** Efeito Barnum (validação genérica percebida como pessoal)
O Oráculo responde a qualquer pergunta com respostas vagas mas emocionalmente convincentes. O efeito Barnum é o fenómeno pelo qual as pessoas acreditam que descrições genéricas se aplicam especificamente a elas. Claude API gera respostas propositalmente ambíguas mas impactantes.

**Viral porque:** toda a gente recebe uma resposta que "acerta" — e partilha por isso.

## SEO
```php
$seo = [
    'title'       => 'O Oráculo — Pergunta. A Resposta Já Existe.',
    'description' => 'Faz uma pergunta ao Oráculo. A resposta vai fazer sentido. Sempre.',
    'og_title'    => '🔮 Perguntei ao Oráculo e acertou em cheio',
    'og_image'    => asset('images/og/oraculo.png'),
    'canonical'   => route('oraculo.index'),
];
```

## Tasks

### Route
```php
Route::prefix('oraculo')->name('oraculo.')->group(function () {
    Route::get('/', [OraculoController::class, 'index'])->name('index');
    Route::post('/consultar', [OraculoController::class, 'consultar'])->name('consultar');
});
```

### Controller `OraculoController`
Com Claude API. Input da pergunta processado e descartado.

```php
public function consultar(Request $request)
{
    $request->validate(['pergunta' => 'required|string|max:200']);
    // PRIVACIDADE: pergunta não guardada

    $systemPrompt = <<<PROMPT
    És o Oráculo — um ser misterioso que responde perguntas com sabedoria propositalmente vaga.
    Regras da resposta:
    - 2-3 frases máximo
    - Usa o efeito Barnum: respostas que parecem específicas mas aplicam-se a qualquer pessoa
    - Tom: sério, misterioso, ligeiramente poético
    - Nunca diz "não sei" — o Oráculo sabe sempre
    - A resposta deve parecer profunda à primeira leitura
    - Pode incluir uma "acção" vaga ("presta atenção ao que sentes amanhã")
    - Escreve em português de Portugal. Sem introdução. Só a resposta.
    PROMPT;

    $resposta = $this->claude->generate($systemPrompt, $request->pergunta, 150);
    AnalyticsService::event('oraculo', 'consult');

    return response()->json(['resposta' => $resposta ?? 'O silêncio também é uma resposta.']);
}
```

### UX
1. Campo de texto: "Faz a tua pergunta ao Oráculo"
2. Loading dramático: 3 segundos com animação de bola de cristal pulsante
3. Resposta aparece com fade
4. "O Oráculo falou." em pequeno
5. Share: "Perguntei ao Oráculo: {pergunta} — e respondeu: {resposta}"

**Cor de acento:** `purple`

---

---

# PROMPT 16 — Modo Pânico

## Conceito
**Gatilho psicológico:** Urgência falsa + alívio cómico
O utilizador descreve uma situação banal e o Modo Pânico transforma-a numa crise dramática de nível 10. Depois mostra o "plano de acção" completamente inútil. O contraste entre o drama e a banalidade é o humor.

**Viral porque:** "Coloquei 'tenho uma reunião amanhã' no Modo Pânico" é um tweet.

## SEO
```php
$seo = [
    'title'       => 'Modo Pânico — Activa a Crise para Qualquer Situação',
    'description' => 'Introduz qualquer situação. O Modo Pânico transforma-a numa emergência de nível 10 com plano de acção.',
    'og_title'    => '🚨 MODO PÂNICO ACTIVADO',
    'og_image'    => asset('images/og/panico.png'),
    'canonical'   => route('panico.index'),
];
```

## Tasks

### Route
```php
Route::prefix('panico')->name('panico.')->group(function () {
    Route::get('/', [PanicoController::class, 'index'])->name('index');
    Route::post('/activar', [PanicoController::class, 'activar'])->name('activar');
});
```

### Controller `PanicoController`
Com Claude API.

```php
public function activar(Request $request)
{
    $request->validate(['situacao' => 'required|string|max:200']);

    $systemPrompt = <<<PROMPT
    És o Modo Pânico — um sistema de gestão de crises completamente exagerado.
    Dado uma situação banal, gera:
    1. NÍVEL DE CRISE: um número de 1-10 (sempre entre 8-10, nunca menos)
    2. ANÁLISE DA AMEAÇA: 1 frase dramática e absurda
    3. PLANO DE ACÇÃO: 3 passos completamente inúteis mas apresentados com seriedade
    4. MENSAGEM FINAL: 1 frase de encorajamento ridículo

    Formato de resposta (exactamente assim):
    NÍVEL: X/10
    AMEAÇA: [texto]
    ACÇÃO 1: [texto]
    ACÇÃO 2: [texto]
    ACÇÃO 3: [texto]
    FORÇA: [texto]

    Tom: alarme militar + absurdo. Escreve em português de Portugal.
    PROMPT;

    $resposta = $this->claude->generate($systemPrompt, $request->situacao, 250);
    AnalyticsService::event('panico', 'activate');

    return response()->json(['resposta' => $resposta]);
}
```

### UX
1. Input: "Descreve a situação"
2. Botão vermelho: "ACTIVAR MODO PÂNICO"
3. Animação: ecrã pisca vermelho, sirene visual
4. Resultado: card estilo terminal militar com NÍVEL DE CRISE, análise e plano
5. Share: "Activei o Modo Pânico para '{situação}' e o nível de crise é 9/10"

**Cor de acento:** `red` — vermelho de emergência

---

---

# PROMPT 17 — Quanto Tempo Perdeste?

## Conceito
**Gatilho psicológico:** Culpa produtiva + auto-consciência
Calculadora que estima de forma dramática quanto tempo da tua vida já perdeste em actividades específicas. Os números são reais (baseados em estudos) mas apresentados de forma catastrófica.

**Viral porque:** "Já passei 847 dias da minha vida a fazer scroll" é assustador e partilhável.

## SEO
```php
$seo = [
    'title'       => 'Quanto Tempo Perdeste? — A Calculadora do Arrependimento',
    'description' => 'Calcula exactamente quanto tempo da tua vida já perdeste. Os números são reais. O drama também.',
    'og_title'    => '😱 Já perdi X anos da minha vida a fazer scroll',
    'og_image'    => asset('images/og/tempo.png'),
    'canonical'   => route('tempo.index'),
];
```

## Tasks

### Route
```php
Route::prefix('tempo')->name('tempo.')->group(function () {
    Route::get('/', [TempoController::class, 'index'])->name('index');
});
```

### Controller `TempoController`
Sem Claude API. Cálculo puramente no frontend. Zero dados guardados.

### View — Alpine.js puro

**Actividades com médias de estudos reais:**
```javascript
const actividades = [
    { id: 'scroll',    label: 'Scroll nas redes sociais',  horas_dia: 2.5,  emoji: '📱' },
    { id: 'tv',        label: 'Ver televisão/séries',      horas_dia: 3.1,  emoji: '📺' },
    { id: 'email',     label: 'Verificar email',           horas_dia: 0.5,  emoji: '📧' },
    { id: 'reunioes',  label: 'Reuniões desnecessárias',   horas_dia: 0.8,  emoji: '💼' },
    { id: 'espera',    label: 'Esperar por coisas',        horas_dia: 1.0,  emoji: '⏳' },
    { id: 'whatsapp',  label: 'Responder ao WhatsApp',     horas_dia: 0.7,  emoji: '💬' },
    { id: 'saudades',  label: 'Pensar no passado',         horas_dia: 0.5,  emoji: '😔' },
];
```

**Cálculo:** `anos_vividos × 365 × horas_dia / 24`
Apresentado em dias, semanas, meses e anos.

**UX:**
1. Slider: "Tenho X anos"
2. Checkboxes com sliders de "horas por dia" por actividade
3. Resultado em tempo real: "Já perdeste X anos, Y meses e Z dias"
4. Frase dramática: "Equivale a {X} temporadas completas de séries"
5. Share: "Já perdi {X} anos da minha vida a {actividade principal}"

**Cor de acento:** `amber`

---

---

# PROMPT 18 — A Lista de Coisas Que Nunca Vais Fazer

## Conceito
**Gatilho psicológico:** Identificação + humor de reconhecimento
Uma lista de "objectivos de vida" que toda a gente tem mas nunca vai cumprir. O utilizador vai marcando as que se aplicam. Quanto mais marca, mais se ri (e chora). Partilha o resultado como auto-sabotagem bem-humorada.

**Viral porque:** é um espelho cómico — partilhas porque "isto sou eu".

## SEO
```php
$seo = [
    'title'       => 'Coisas Que Nunca Vais Fazer — A Lista Honesta',
    'description' => 'A bucket list que ninguém vai completar. Marca as tuas e descobre o teu nível de procrastinação.',
    'og_title'    => '😅 Marquei X/30 coisas que nunca vou fazer',
    'og_image'    => asset('images/og/lista.png'),
    'canonical'   => route('lista.index'),
];
```

## Tasks

### Route
```php
Route::prefix('lista')->name('lista.')->group(function () {
    Route::get('/', [ListaController::class, 'index'])->name('index');
});
```

### Controller `ListaController`
Sem Claude API, sem DB. Lista curada.

```php
private array $itens = [
    "Aprender a tocar guitarra",
    "Começar a correr regularmente",
    "Ler todos os livros que comprei",
    "Organizar as fotos do telemóvel",
    "Aprender uma língua nova",
    "Fazer o curso online que comprei em promoção",
    "Responder àquele email de há 3 meses",
    "Ir ao médico para aquela coisa",
    "Limpar o closet de uma vez por todas",
    "Escrever aquele livro/blog/podcast",
    "Ligar para aquela pessoa que perdi o contacto",
    "Começar a meditar",
    "Ter uma rotina de manhã",
    "Parar de dizer 'amanhã começo'",
    "Guardar 10% do salário todos os meses",
    "Aprender a cozinhar de verdade",
    "Fazer aquela viagem de sonho",
    "Ir ao ginásio mais de 2 vezes por semana",
    "Acabar de ver aquela série que ficou a meio",
    "Fazer um detox digital de 1 semana",
    "Acordar às 6h todos os dias",
    "Beber 2 litros de água por dia",
    "Redecor o quarto/sala",
    "Aprender a investir",
    "Ter um jardim ou plantas em casa",
    "Fazer voluntariado",
    "Aprender a dançar",
    "Tirar carta de condução/mota",
    "Criar uma aplicação/site próprio",
    "Finalmente limpar o email (inbox zero)",
];
```

**UX:**
1. Lista de 30 itens com checkboxes
2. Ao marcar: risco no texto + animação
3. Contador em tempo real: "X/30 coisas que nunca vais fazer"
4. Mensagens progressivas baseadas no total:
   - < 5: "Ainda tens esperança."
   - 10+: "Interessante."
   - 20+: "Agora estamos a falar."
   - 30: "Parabéns. A tua honestidade é refrescante."
5. Estado guardado em `localStorage` (sem PII)

**Cor de acento:** `orange`

---

---

# PROMPT 19 — Conquistas do Nada

## Conceito
**Gatilho psicológico:** Completionism + gamificação absurda
Sistema de conquistas (achievements) por fazer absolutamente nada de útil. Cada conquista tem nome épico, descrição séria e ícone de medalha. O absurdo é a distância entre a grandiosidade da apresentação e a inutilidade da acção.

**Viral porque:** "Ganhei a conquista 'Veterano do Scroll' no Clinky.cc" é partilhável por ser simultaneamente envergonhante e engraçado.

## SEO
```php
$seo = [
    'title'       => 'Conquistas do Nada — Desbloqueia Medalhas Por Não Fazer Nada',
    'description' => 'Sistema de conquistas para as tuas maiores não-realizações. Cada clique é uma medalha.',
    'og_title'    => '🏆 Desbloqueei a conquista "Mestre do Procrastinar"',
    'og_image'    => asset('images/og/conquistas.png'),
    'canonical'   => route('conquistas.index'),
];
```

## Tasks

### Route
```php
Route::prefix('conquistas')->name('conquistas.')->group(function () {
    Route::get('/', [ConquistasController::class, 'index'])->name('index');
});
```

### Controller `ConquistasController`
Sem Claude API. Lógica 100% no frontend com localStorage.

### Conquistas disponíveis (desbloqueadas por acções na página)

```javascript
const conquistas = [
    // Desbloqueadas ao entrar
    { id: 'chegou',    titulo: 'Pioneiro do Inútil',      desc: 'Visitaste o Clinky.cc pela primeira vez.',           emoji: '🎖️', trigger: 'visit' },

    // Por tempo na página
    { id: '30s',       titulo: 'Presente no Momento',     desc: 'Passaste 30 segundos sem fazer nada útil.',          emoji: '⏱️', trigger: 'time_30' },
    { id: '2min',      titulo: 'Mestre da Contemplação',  desc: 'Dois minutos de existência pura.',                   emoji: '🧘', trigger: 'time_120' },
    { id: '5min',      titulo: 'Lenda do Procrastinar',   desc: 'Cinco minutos. Já podias ter feito algo.',            emoji: '🏆', trigger: 'time_300' },

    // Por número de cliques (em qualquer coisa)
    { id: 'click10',   titulo: 'Dedo Activo',             desc: 'Clicaste 10 vezes. Sem propósito.',                  emoji: '👆', trigger: 'clicks_10' },
    { id: 'click50',   titulo: 'Veterano do Clique',      desc: '50 cliques. A tua motivação está mal aplicada.',     emoji: '🖱️', trigger: 'clicks_50' },

    // Por visitas (localStorage)
    { id: 'volta',     titulo: 'Sem Aprendizagem',        desc: 'Voltaste. Sabias que não havia nada novo.',          emoji: '🔄', trigger: 'return_visit' },
    { id: '5visitas',  titulo: 'Habitué do Nada',         desc: 'Quinta visita. Isto é preocupante.',                 emoji: '🎗️', trigger: 'visits_5' },

    // Secretas
    { id: 'scroll',    titulo: 'Veterano do Scroll',      desc: 'Chegaste ao fundo da página.',                      emoji: '📜', trigger: 'scroll_bottom' },
    { id: 'noturno',   titulo: 'Insónia Produtiva',       desc: 'Visitaste entre as 00h e as 5h.',                   emoji: '🌙', trigger: 'late_night' },
];
```

**UX:**
1. Grelha de medalhas — bloqueadas aparecem cinzentas com `?`
2. Ao desbloquear: animação de "pop" + toast notification "Conquista desbloqueada!"
3. Progresso: "X/10 conquistas desbloqueadas"
4. Conquistas secretas não listadas — aparecem como surpresa
5. Share: "Desbloqueei {X}/10 conquistas no Clinky.cc, incluindo '{conquista mais épica}'"

**Cor de acento:** `yellow` / `amber` (ouro das medalhas)

---

---

# PROMPT 20 — Já Ouviste Isto?

## Conceito
**Gatilho psicológico:** Curiosidade auditiva + memória involuntária
Claude API gera descrições de sons que o utilizador "nunca ouviu mas conhece" — o som de acordar e não saber onde estás, o som de uma memória que não consegues situar, o som de uma saudade específica. Completamente abstracto, completamente partilhável.

**Viral porque:** cria um momento de "isso é exactamente como soa" em coisas que nunca foram descritas.

## SEO
```php
$seo = [
    'title'       => 'Já Ouviste Isto? — Sons que Nunca Ouviste mas Conheces',
    'description' => 'Descrições de sons que não existem mas que reconheces. Gerado por IA. Inexplicavelmente preciso.',
    'og_title'    => '🎧 "O som de uma saudade que não sabes de quê" — acertou em cheio',
    'og_image'    => asset('images/og/ouviste.png'),
    'canonical'   => route('ouviste.index'),
];
```

## Tasks

### Route
```php
Route::prefix('ouviste')->name('ouviste.')->group(function () {
    Route::get('/', [OuvisteController::class, 'index'])->name('index');
    Route::post('/gerar', [OuvisteController::class, 'gerar'])->name('gerar');
});
```

### Controller `OuvisteController`
Com Claude API.

```php
private array $categorias = [
    'memória'    => 'sons ligados a memórias vagas ou de infância',
    'saudade'    => 'sons de emoções sem nome específico',
    'transição'  => 'sons de momentos entre estados (acordar, adormecer, esperar)',
    'silêncio'   => 'diferentes tipos de silêncio e ausência de som',
    'interior'   => 'sons do corpo ou da mente que ninguém descreve',
];

public function gerar(Request $request)
{
    $categoria = $request->input('categoria', array_rand($this->categorias));

    $systemPrompt = <<<PROMPT
    És um poeta-sonólogo que descreve sons que não têm nome mas que toda a gente conhece.
    Gera UMA descrição de um som da categoria: {$categoria}.
    Formato: "O som de [descrição poética em 1-2 frases]"
    Exemplos do tipo:
    - "O som de acordar a meio da noite e não saber que horas são."
    - "O som de uma casa quando toda a gente já saiu."
    - "O som de uma memória que estás quase a lembrar mas não consegues."
    
    A descrição deve:
    - Ser específica o suficiente para ser reconhecível
    - Ser vaga o suficiente para se aplicar a toda a gente (efeito Barnum)
    - Criar uma sensação física de reconhecimento
    - Ter entre 1-3 frases
    
    Escreve em português de Portugal. Só a descrição, nada mais.
    PROMPT;

    $som = $this->claude->generate($systemPrompt, "Gera um som da categoria {$categoria}.", 150);
    AnalyticsService::event('ouviste', 'generate');

    return response()->json(['som' => $som ?? 'O som do silêncio que não é bem silêncio.']);
}
```

### UX
1. Título: "Já ouviste isto?"
2. Botão: "Gerar um som"
3. Loading: animação de onda sonora
4. Resultado: texto grande em itálico, centrado, fundo escuro
5. Abaixo: emoji de reacção — "Reconheces?" com botões Sim/Não (só analytics, sem guardar)
6. Botão "Próximo som"
7. Share: "'{descrição do som}' — acertou em cheio"

**Cor de acento:** `teal` / `cyan`

---

## RUN-ALL — Versão Psicológica

Para executar todos os mini-sites em sequência:

```
Lê CLAUDE.md. Verifica o estado do projecto. Executa o próximo prompt não concluído em PROMPTS/. Confirma com ✅ quando terminar. Avança automaticamente para o seguinte. Para quando todos estiverem [x].
```
