# PROMPT 14 — A Decisão Impossível

## Conceito
**Gatilho psicológico:** Paralisia de análise + necessidade de validação social
Apresenta duas opções absurdas sem saída correcta. O utilizador escolhe, recebe uma "análise" da escolha, e partilha para pedir a opinião dos outros. É o formato "isso ou aquilo" do Instagram mas com Claude API a gerar análises pseudo-psicológicas da escolha.

**Viral porque:** toda a gente quer saber o que os outros escolheriam, e a análise é sempre estranhamente precisa.

## SEO
```php
$seo = [
    'title'          => 'A Decisão Impossível — Não Há Resposta Certa',
    'description'    => 'Duas opções. Nenhuma é boa. Tens de escolher uma. O que diz isso sobre ti?',
    'og_title'       => '🤯 Escolhi X. Tu o que escolhias?',
    'og_description' => 'Não há resposta certa. Mas há uma análise.',
    'og_image'       => asset('images/og/decisao.png'),
    'canonical'      => route('decisao.index'),
];
```

## UX — Fluxo do utilizador
1. Duas opções em ecrã dividido — esquerda vs direita
2. Utilizador carrega numa das opções
3. Loading: "A analisar a tua escolha..."
4. Claude API gera análise pseudo-psicológica da escolha (2-3 frases)
5. Mostra também: "X% das pessoas escolheu o mesmo" (simulado mas convincente)
6. Botão "Próxima decisão impossível"
7. Share: "Escolhi [opção]. Tu escolhias o quê?"

## Tasks

### Route
```php
Route::prefix('decisao')->name('decisao.')->group(function () {
    Route::get('/', [DecisaoController::class, 'index'])->name('index');
    Route::post('/escolher', [DecisaoController::class, 'escolher'])->name('escolher');
});
```

### Controller `DecisaoController`
Com Claude API para análise. Dilemas curados em array.

```php
private array $dilemas = [
    [
        'id'  => 1,
        'a'   => 'Saber todas as respostas mas nunca poder explicar porquê',
        'b'   => 'Saber explicar tudo mas nunca ter a resposta certa',
    ],
    [
        'id'  => 2,
        'a'   => 'Dormir sempre bem mas acordar sempre cansado',
        'b'   => 'Acordar sempre descansado mas nunca conseguir dormir',
    ],
    [
        'id'  => 3,
        'a'   => 'Rir em momentos inapropriados para sempre',
        'b'   => 'Chorar em momentos inapropriados para sempre',
    ],
    [
        'id'  => 4,
        'a'   => 'Cada vez que comes algo delicioso, alguém no mundo come algo horrível',
        'b'   => 'Cada vez que comes algo horrível, alguém no mundo come algo delicioso',
    ],
    [
        'id'  => 5,
        'a'   => 'Saber exactamente quando vais morrer',
        'b'   => 'Não saber mas que seja nos próximos 10 anos',
    ],
    [
        'id'  => 6,
        'a'   => 'Falar todas as línguas mas não conseguir ler',
        'b'   => 'Ler tudo mas não conseguir falar',
    ],
    [
        'id'  => 7,
        'a'   => 'Voltar atrás 10 anos com a memória que tens agora',
        'b'   => 'Avançar 10 anos para ver como fica',
    ],
    [
        'id'  => 8,
        'a'   => 'Nunca mais sentir frio',
        'b'   => 'Nunca mais sentir calor',
    ],
];

public function index()
{
    $dilema = $this->dilemas[array_rand($this->dilemas)];
    AnalyticsService::pageView('decisao');
    return view('sites.decisao.index', [
        'seo'    => $this->seo(),
        'dilema' => $dilema,
        'dilemas_json' => json_encode($this->dilemas),
    ]);
}

public function escolher(Request $request)
{
    $request->validate([
        'escolha' => 'required|string|max:300',
        'opcao'   => 'required|in:a,b',
    ]);

    // PRIVACIDADE: não guardamos a escolha
    $escolha = $request->escolha;
    $opcao   = $request->opcao;

    $systemPrompt = <<<PROMPT
    És um psicólogo fictício que analisa decisões impossíveis com tom sério mas levemente absurdo.
    Dado um dilema e a escolha feita, gera uma análise curta (2-3 frases) que:
    - Parece profunda mas é ligeiramente inventada
    - Revela algo sobre a personalidade da pessoa de forma vaga mas convincente (efeito Barnum)
    - É sempre positiva no tom mas existencialmente inquietante
    - Termina com uma observação filosófica curta
    Escreve em português de Portugal. Sem introdução. Apenas a análise.
    PROMPT;

    $analise = $this->claude->generate(
        $systemPrompt,
        "Dilema: '{$escolha}'. A pessoa escolheu esta opção.",
        200
    );

    // Percentagem simulada mas realista
    $percentagem = rand(38, 67);

    AnalyticsService::event('decisao', 'choose');

    return response()->json([
        'analise'     => $analise ?? 'A tua escolha revela profundidade que preferes não examinar.',
        'percentagem' => $percentagem,
    ]);
}
```

### View `resources/views/sites/decisao/index.blade.php`

**Layout ecrã dividido:**
```html
<div class="grid grid-cols-2 gap-3 min-h-[50vh]" x-data="decisao()">
    <button @click="escolher('a')"
            class="flex flex-col items-center justify-center p-6 rounded-2xl
                   bg-zinc-900 border-2 border-zinc-800
                   hover:border-purple-500 hover:bg-purple-950/30
                   active:scale-95 transition-all text-center">
        <span class="text-4xl mb-4">{{ $dilema['a'] }}</span>
    </button>
    <button @click="escolher('b')"
            class="...mesmo estilo...">
        <span class="text-4xl mb-4">{{ $dilema['b'] }}</span>
    </button>
</div>

{{-- VS no centro --}}
<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
            bg-zinc-950 border border-zinc-700 rounded-full w-10 h-10
            flex items-center justify-center text-xs font-bold text-zinc-400">
    VS
</div>
```

**Cor de acento:** `purple`

**Texto de partilha WhatsApp:**
```
Escolhi "{opção escolhida}" e a análise disse: "{primeira frase da análise}"

E tu, o que escolhias? → https://clinky.cc/decisao
```

## JSON-LD
```json
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "A Decisão Impossível",
  "description": "Duas opções. Nenhuma é boa. O que diz isso sobre ti?",
  "url": "https://clinky.cc/decisao",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT"
}
```

## OG Image
Fundo roxo escuro. Ecrã dividido ao meio com "VS" no centro. Esquerda mais escura, direita mais clara. Tipografia bold branca. Vibe de escolha impossível, tensão visual.
