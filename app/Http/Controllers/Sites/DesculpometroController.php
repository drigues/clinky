<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesculpometroController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('desculpometro');
        $totalGeradas = AnalyticsService::getCount('desculpometro', 'generate', 365);

        return view('sites.desculpometro.index', [
            'seo' => $this->seo(),
            'totalGeradas' => max($totalGeradas, 1247),
        ]);
    }

    public function gerar(Request $request): JsonResponse
    {
        $request->validate([
            'situacao' => 'required|in:trabalho,ginasio,familia,encontro,reuniao,aula,consulta,outro',
            'absurdo' => 'required|integer|min:0|max:3',
        ]);

        $niveis = ['realista', 'criativo', 'épico', 'completamente absurdo'];
        $nivel = $niveis[$request->absurdo];
        $situacao = $request->situacao;

        $systemPrompt = <<<PROMPT
        És o Desculpómetro — um gerador de desculpas {$nivel}.
        Gera UMA desculpa curta (máximo 2 frases) para justificar: {$situacao}.
        A desculpa deve ser {$nivel} e divertida, mas plausível para o nível pedido.
        Responde APENAS com a desculpa, sem introdução, sem aspas, sem explicações.
        Escreve em português de Portugal.
        PROMPT;

        $desculpa = $this->claude->generate($systemPrompt, "Gera uma desculpa {$nivel} para {$situacao}.");

        if (!$desculpa) {
            $desculpa = 'O meu gerador de desculpas teve uma crise existencial. Tenta outra vez.';
        }

        AnalyticsService::event('desculpometro', 'generate');

        return response()->json(['desculpa' => $desculpa]);
    }

    private function seo(): array
    {
        return [
            'title' => 'Desculpómetro — Gera a Desculpa Perfeita em 1 Segundo',
            'description' => 'Gerador de desculpas absurdas com IA. Ideal para quando precisas de uma razão épica para faltar, cancelar ou desaparecer.',
            'og_title' => '😅 Desculpómetro — a culpa foi do gato filosófico',
            'og_description' => 'Gera a tua desculpa perfeita agora. Grátis, anónimo, partilhável.',
            'og_image' => asset('images/og/desculpometro.png'),
            'canonical' => 'https://desculpometro.' . config('app.base_domain'),
        ];
    }
}
