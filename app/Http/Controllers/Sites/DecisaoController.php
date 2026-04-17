<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DecisaoController extends Controller
{
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

    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('decisao');

        $dilema = $this->dilemas[array_rand($this->dilemas)];

        return view('sites.decisao.index', [
            'seo'         => $this->seo(),
            'dilema'      => $dilema,
            'dilemas_json' => json_encode($this->dilemas),
        ]);
    }

    public function escolher(Request $request): JsonResponse
    {
        $request->validate([
            'escolha' => 'required|string|max:300',
            'opcao'   => 'required|in:a,b',
        ]);

        $escolha = $request->escolha;
        $opcao   = $request->opcao;

        $systemPrompt = <<<PROMPT
        És um psicólogo fictício que analisa decisões impossíveis com tom sério mas levemente absurdo.
        Dado um dilema e a escolha feita, gera uma análise curta (2-3 frases) que:
        - Parece profunda mas é ligeiramente inventada
        - Revela algo sobre a personalidade da pessoa de forma vaga mas convincente (efeito Barnum)
        - É sempre positiva no tom mas existencialmente inquietante
        - Termina com uma observação filosófica curta
        Escreve em português de Portugal. Sem introdução. Apenas a análise directa.
        PROMPT;

        $analise = $this->claude->generate(
            $systemPrompt,
            "Dilema: '{$escolha}'. A pessoa escolheu a opção {$opcao}.",
            200
        );

        $percentagem = rand(38, 67);

        AnalyticsService::event('decisao', 'choose');

        return response()->json([
            'analise'     => $analise ?? 'A tua escolha revela uma profundidade que preferes não examinar.',
            'percentagem' => $percentagem,
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'A Decisão Impossível — Sem Resposta Certa',
            'description'    => 'Duas opções absurdas. Nenhuma é boa. Tens de escolher uma. A IA analisa o que isso diz sobre ti. Partilha e descobre o que os outros escolheriam.',
            'og_title'       => '🤯 Escolhi uma. Tu o que escolhias?',
            'og_description' => 'Não há resposta certa. Mas há uma análise.',
            'og_image'       => asset('images/og/decisao.png'),
            'canonical'      => route('decisao.index'),
        ];
    }
}
