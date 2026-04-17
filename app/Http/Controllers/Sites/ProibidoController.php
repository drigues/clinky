<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class ProibidoController extends Controller
{
    private array $resultados = [
        // Frequentes (peso 3)
        ['tipo' => 'nada',       'texto' => '...',                                                          'peso' => 3],
        ['tipo' => 'repreensao', 'texto' => 'Dissemos para não carregar.',                                  'peso' => 3],
        ['tipo' => 'nada2',      'texto' => 'Nada aconteceu. Satisfeito?',                                  'peso' => 3],

        // Médios (peso 2)
        ['tipo' => 'elogio',     'texto' => 'Tens uma energia muito especial. Não sabemos porquê.',         'peso' => 2],
        ['tipo' => 'filosofia',  'texto' => 'Se uma árvore cair numa floresta e ninguém ouvir, carregaste no botão na mesma.', 'peso' => 2],
        ['tipo' => 'parabens',   'texto' => 'Parabéns. Não ganhou nada.',                                   'peso' => 2],

        // Raros (peso 1)
        ['tipo' => 'raro',        'texto' => '🎉 Encontraste o resultado raro! (não há prémio)',            'peso' => 1],
        ['tipo' => 'existencial', 'texto' => 'O botão também te escolheu a ti.',                            'peso' => 1],
        ['tipo' => 'silencio',    'texto' => '',                                                             'peso' => 1],
    ];

    public function index()
    {
        AnalyticsService::pageView('proibido');

        return view('sites.proibido.index', [
            'seo' => $this->seo(),
        ]);
    }

    public function carregar(Request $request)
    {
        $pool = [];
        foreach ($this->resultados as $r) {
            for ($i = 0; $i < $r['peso']; $i++) {
                $pool[] = $r;
            }
        }

        $resultado = $pool[array_rand($pool)];

        AnalyticsService::event('proibido', 'press');

        return response()->json([
            'tipo'  => $resultado['tipo'],
            'texto' => $resultado['texto'],
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'O Botão Proibido — Não Carregues',
            'description'    => 'Há um botão. Disseram-te para não carregar. O que vais fazer?',
            'og_title'       => '🚫 Não carregues neste botão.',
            'og_description' => 'Sério. Não carregues. Por favor.',
            'og_image'       => asset('images/og/proibido.png'),
            'canonical'      => route('proibido.index'),
        ];
    }
}
