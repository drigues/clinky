<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class BolhasController extends Controller
{
    public function index()
    {
        AnalyticsService::pageView('bolhas');

        return view('sites.bolhas.index', [
            'seo' => $this->seo(),
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Rebenta as Bolhas — Satisfação Garantida',
            'description'    => 'Bolhas infinitas para rebentar no browser. Sem propósito. Sem fim. Completamente viciante. Não digas que não avisámos antes de começares.',
            'og_title'       => '🫧 Rebenta as Bolhas — impossível parar',
            'og_description' => 'Já rebentaste 0 bolhas. Daqui a 5 minutos esse número vai ser muito maior.',
            'og_image'       => asset('images/og/bolhas.png'),
            'canonical'      => route('bolhas.index'),
        ];
    }
}
