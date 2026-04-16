<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ClaudeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalisadorNomeController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    public function index()
    {
        AnalyticsService::pageView('nome');

        return view('sites.nome.index', [
            'seo' => $this->seo(),
        ]);
    }

    public function analisar(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:50', 'regex:/^[\p{L}\s]+$/u'],
        ]);

        $nome = trim($request->nome);

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
            $analise = 'O nosso laboratório de análise de nomes entrou em colapso quântico. Tenta novamente.';
        }

        AnalyticsService::event('nome', 'analyze');

        return response()->json(['analise' => $analise, 'nome' => $nome]);
    }

    private function seo(): array
    {
        return [
            'title' => 'O Que o Teu Nome Diz? — Análise Científica* de Personalidade',
            'description' => 'Descobre o que o teu nome revela sobre ti. *Totalmente inventado, mas surpreendentemente preciso.',
            'og_title' => '🧬 Descobri o que o meu nome diz sobre mim',
            'og_description' => 'Análise de nome com IA. 100% inventada, surpreendentemente precisa.',
            'og_image' => asset('images/og/nome.png'),
            'canonical' => 'https://nome.' . config('app.base_domain'),
        ];
    }
}
