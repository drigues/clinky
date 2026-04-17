<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class ConquistasController extends Controller
{
    private array $conquistas = [
        [
            'id'      => 'primeiro_passo',
            'titulo'  => 'Primeiro Passo',
            'desc'    => 'Abriste esta página.',
            'emoji'   => '👣',
            'trigger' => 'visit',
            'secret'  => false,
        ],
        [
            'id'      => 'turista',
            'titulo'  => 'Turista do Nada',
            'desc'    => 'Ficaste 30 segundos aqui.',
            'emoji'   => '🏖️',
            'trigger' => 'time_30',
            'secret'  => false,
        ],
        [
            'id'      => 'residente',
            'titulo'  => 'Residente Permanente',
            'desc'    => 'Já estás aqui há 2 minutos.',
            'emoji'   => '🏠',
            'trigger' => 'time_120',
            'secret'  => false,
        ],
        [
            'id'      => 'filosofo',
            'titulo'  => 'Filósofo do Vazio',
            'desc'    => '5 minutos a olhar para medalhas.',
            'emoji'   => '🧘',
            'trigger' => 'time_300',
            'secret'  => false,
        ],
        [
            'id'      => 'clicker',
            'titulo'  => 'Clicker Curioso',
            'desc'    => 'Carregaste 10 vezes nesta página.',
            'emoji'   => '👆',
            'trigger' => 'clicks_10',
            'secret'  => false,
        ],
        [
            'id'      => 'obsessivo',
            'titulo'  => 'Obsessivo Compulsivo',
            'desc'    => '50 cliques. Porquê?',
            'emoji'   => '🫠',
            'trigger' => 'clicks_50',
            'secret'  => false,
        ],
        [
            'id'      => 'recidivista',
            'titulo'  => 'Recidivista',
            'desc'    => 'Voltaste. Outra vez.',
            'emoji'   => '🔄',
            'trigger' => 'return_visit',
            'secret'  => false,
        ],
        [
            'id'      => 'fiel',
            'titulo'  => 'Fiel ao Nada',
            'desc'    => '5 visitas. Isto já é rotina.',
            'emoji'   => '💍',
            'trigger' => 'visits_5',
            'secret'  => false,
        ],
        [
            'id'      => 'explorador',
            'titulo'  => 'Explorador do Fundo',
            'desc'    => 'Fizeste scroll até ao fim.',
            'emoji'   => '🔭',
            'trigger' => 'scroll_bottom',
            'secret'  => true,
        ],
        [
            'id'      => 'nocturno',
            'titulo'  => 'Criatura Nocturna',
            'desc'    => 'Visitas entre a meia-noite e as 5h.',
            'emoji'   => '🦇',
            'trigger' => 'late_night',
            'secret'  => true,
        ],
    ];

    public function index()
    {
        AnalyticsService::pageView('conquistas');

        return view('sites.conquistas.index', [
            'seo'              => $this->seo(),
            'conquistas_json'  => json_encode($this->conquistas),
            'total'            => count($this->conquistas),
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Conquistas do Nada — Medalhas por Zero',
            'description'    => 'Ganha medalhas por fazer absolutamente nada de útil. Visitar a página, clicar sem razão, ficar parado. Cada conquista é mais inútil que a anterior.',
            'og_title'       => '🏆 Já desbloqueei 3 conquistas do nada. E tu?',
            'og_description' => 'Medalhas por fazer absolutamente nada de útil. Até que ponto vais?',
            'og_image'       => asset('images/og/conquistas.png'),
            'canonical'      => route('conquistas.index'),
        ];
    }
}
