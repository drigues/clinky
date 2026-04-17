<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class ProgressoController extends Controller
{
    public function index()
    {
        AnalyticsService::pageView('progresso');

        return view('sites.progresso.index', [
            'seo' => $this->seo(),
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Barra de Progresso da Vida — Quanto já passou?',
            'description'    => 'Introduz a tua data de nascimento. Vê exactamente que % da tua vida já passou. Actualiza em tempo real. Não é motivacional.',
            'og_title'       => '⏳ A minha vida está X% completa',
            'og_description' => 'Um número que não queres ver mas não consegues ignorar.',
            'og_image'       => asset('images/og/progresso.png'),
            'canonical'      => route('progresso.index'),
        ];
    }
}
