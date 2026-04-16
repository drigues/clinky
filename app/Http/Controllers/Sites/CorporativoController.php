<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CorporativoController extends Controller
{
    private array $dicionario = [
        'vamos alinhar' => '🗓️ Vamos ter uma reunião que devia ser um email',
        'synergy' => '🤝 Fazer o costume mas com slide de PowerPoint',
        'fora da caixa' => '📦 Ninguém sabe o que isto significa, incluindo quem disse',
        'quick win' => '🏆 Coisa pequena que fingimos ser estratégica',
        'deep dive' => '🤿 Reunião mais longa sobre o mesmo assunto',
        'move the needle' => '📊 Fazer algo que apareça no dashboard do chefe',
        'takeaways' => '📝 O que devias ter dito antes da reunião',
        'bandwidth' => '📡 Tempo que não tens mas vão pedir na mesma',
        'circling back' => '🔄 Fazer followup de algo que ninguém fez',
        'low-hanging fruit' => '🍎 A única coisa fácil que ninguém fez ainda',
        'stakeholders' => '👥 Pessoas que têm opinião mas não fazem nada',
        'pipeline' => '🔧 Lista de coisas que nunca vão acontecer',
        'onboarding' => '📋 Três semanas a aprender onde fica a impressora',
        'deliverables' => '📦 Coisas que prometeste e agora tens de entregar',
        'scalable' => '📈 Funciona se tivermos 10x mais dinheiro',
        'pivot' => '🔀 Admitir que o plano original falhou, mas com estilo',
        'leverage' => '💪 Usar algo que já existe e fingir que é estratégia',
        'touchpoint' => '📞 Mandar um email que ninguém vai ler',
        'action items' => '✅ Tarefas que vão ficar na ata e nunca ser feitas',
        'best practices' => '📖 O que toda a gente diz que faz mas ninguém segue',
        'proactive' => '⚡ Fazer algo antes de te pedirem (nunca acontece)',
        'empower' => '🦸 Dar-te mais trabalho mas chamar-lhe autonomia',
        'disruptive' => '💥 Copiar algo que já existe mas com uma app',
        'ecosystem' => '🌍 Muitas empresas juntas que não se entendem',
        'roi' => '💰 Número mágico que justifica qualquer decisão',
    ];

    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('corporativo');

        return view('sites.corporativo.index', [
            'seo' => $this->seo(),
            'termos' => array_keys($this->dicionario),
        ]);
    }

    public function traduzir(Request $request): JsonResponse
    {
        $request->validate([
            'texto' => ['required', 'string', 'max:300'],
        ]);

        $texto = trim($request->texto);

        // Verificar no dicionário curado
        $textoLower = mb_strtolower($texto);
        foreach ($this->dicionario as $termo => $traducao) {
            if (str_contains($textoLower, $termo)) {
                AnalyticsService::event('corporativo', 'translate');
                return response()->json(['traducao' => $traducao, 'fonte' => 'dicionario']);
            }
        }

        // Usar Claude API
        $systemPrompt = <<<PROMPT
        És o Tradutor Corporativo — traduz jargão de escritório para português real e directo.
        Exemplos:
        - "vamos alinhar" = "vamos ter uma reunião desnecessária"
        - "synergize" = "fazer o costume mas com slide bonito"
        - "fora da caixa" = "ninguém sabe o que isto significa"
        - "quick win" = "coisa fácil que fingimos ser estratégica"

        Dado um texto com jargão, devolve a tradução honesta em 1-2 frases.
        Começa com um emoji relevante.
        Tom: sarcástico mas amigável, como um colega de trabalho honesto.
        Escreve em português de Portugal.
        Responde APENAS com a tradução, sem introdução.
        PROMPT;

        $traducao = $this->claude->generate($systemPrompt, "Traduz: {$texto}");

        if (!$traducao) {
            $traducao = '🤖 O nosso tradutor também está em "modo reunião". Tenta noutra altura.';
        }

        AnalyticsService::event('corporativo', 'translate');

        return response()->json(['traducao' => $traducao, 'fonte' => 'ia']);
    }

    private function seo(): array
    {
        return [
            'title' => 'Tradutor Corporativo — O Que Significam Realmente as Palavras do Escritório',
            'description' => '"Synergize deliverables" = fazer o costume mas com slide. Descobre o que o teu chefe está mesmo a dizer.',
            'og_title' => '💼 "Vamos alinhar" = reunião desnecessária. Descobre mais!',
            'og_description' => 'Traduz jargão corporativo para português real. Finalmente vais perceber o que dizem nas reuniões.',
            'og_image' => asset('images/og/corporativo.png'),
            'canonical' => route('corporativo.index'),
        ];
    }
}
