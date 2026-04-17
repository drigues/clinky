<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OraculoController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('oraculo');

        return view('sites.oraculo.index', [
            'seo' => $this->seo(),
        ]);
    }

    public function consultar(Request $request): JsonResponse
    {
        $request->validate([
            'pergunta' => 'required|string|max:200',
        ]);

        $systemPrompt = <<<PROMPT
        És o Oráculo — um ser misterioso que responde perguntas com sabedoria propositalmente vaga.
        Regras da resposta:
        - 2-3 frases máximo
        - Usa o efeito Barnum: respostas que parecem específicas mas aplicam-se a qualquer pessoa
        - Tom: sério, misterioso, ligeiramente poético
        - Nunca diz "não sei" — o Oráculo sabe sempre
        - A resposta deve parecer profunda à primeira leitura
        - Pode incluir uma "acção" vaga ("presta atenção ao que sentes amanhã")
        - Escreve em português de Portugal. Sem introdução. Só a resposta directa.
        PROMPT;

        $resposta = $this->claude->generate($systemPrompt, $request->pergunta, 150);

        AnalyticsService::event('oraculo', 'consult');

        return response()->json([
            'resposta' => $resposta ?? 'O silêncio também é uma resposta.',
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'O Oráculo — A Resposta Já Existe',
            'description'    => 'Faz uma pergunta ao Oráculo. A resposta vai parecer estranhamente certa. Sempre. Gerado por IA com efeito Barnum — vago, poético e assustadoramente pessoal.',
            'og_title'       => '👁️ Perguntei ao Oráculo e acertou em cheio',
            'og_description' => 'A resposta já existe. Só precisas de perguntar.',
            'og_image'       => asset('images/og/oraculo.png'),
            'canonical'      => route('oraculo.index'),
        ];
    }
}
