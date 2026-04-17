<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Cache;

class NadaController extends Controller
{
    public function index()
    {
        AnalyticsService::pageView('nada');

        $base = 800 + (date('H') * 47) + (date('i') * 3);
        $viewers = Cache::remember('nada_viewers', 30, fn () => $base + rand(-50, 150));

        return view('sites.nada.index', [
            'seo'     => $this->seo(),
            'viewers' => $viewers,
        ]);
    }

    public function viewers()
    {
        $base = 800 + (date('H') * 47) + (date('i') * 3);
        $viewers = $base + rand(-80, 200);

        return response()->json(['viewers' => $viewers]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Nada — Literalmente Nada para Ver Aqui',
            'description'    => 'Nada aqui. Mesmo nada. E mesmo assim estás a ler isto. Milhares de pessoas já viram o nada hoje. Quanto tempo aguentas a olhar para nada?',
            'og_title'       => 'Nada.',
            'og_description' => 'Não há nada aqui. Milhares de pessoas já viram o nada hoje.',
            'og_image'       => asset('images/og/nada.png'),
            'canonical'      => route('nada.index'),
        ];
    }
}
