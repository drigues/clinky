<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PanicoController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('panico');

        return view('sites.panico.index', [
            'seo' => $this->seo(),
        ]);
    }

    public function activar(Request $request): JsonResponse
    {
        $request->validate([
            'situacao' => 'required|string|max:200',
        ]);

        $systemPrompt = <<<PROMPT
        És o Modo Pânico — um sistema de gestão de crises completamente exagerado.
        Dado uma situação banal, gera:
        1. NÍVEL DE CRISE: um número de 1-10 (sempre entre 8-10, nunca menos)
        2. ANÁLISE DA AMEAÇA: 1 frase dramática e absurda
        3. PLANO DE ACÇÃO: 3 passos completamente inúteis mas apresentados com seriedade
        4. MENSAGEM FINAL: 1 frase de encorajamento ridículo

        Formato de resposta (exactamente assim, sem desviar):
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

        return response()->json([
            'resposta' => $resposta ?? "NÍVEL: 10/10\nAMEAÇA: O sistema de pânico entrou em pânico.\nACÇÃO 1: Respira.\nACÇÃO 2: Continua a respirar.\nACÇÃO 3: Tenta novamente.\nFORÇA: Sobreviveste a 100% dos teus piores dias.",
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Modo Pânico — Activa a Crise',
            'description'    => 'Introduz qualquer situação banal e o Modo Pânico transforma-a numa emergência de nível 10 com plano de acção completamente inútil. Gerado por IA.',
            'og_title'       => '🚨 MODO PÂNICO ACTIVADO',
            'og_description' => 'Nível de crise: 9/10. Plano de acção: inútil. Drama: máximo.',
            'og_image'       => asset('images/og/panico.png'),
            'canonical'      => route('panico.index'),
        ];
    }
}
