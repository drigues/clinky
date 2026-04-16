<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Models\ButtonPress;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class BotaoController extends Controller
{
    public function index()
    {
        AnalyticsService::pageView('botao');
        $total = $this->getTotal();

        return view('sites.botao.index', [
            'seo' => $this->seo($total),
            'total' => $total,
        ]);
    }

    public function pressionar(): JsonResponse
    {
        $record = ButtonPress::firstOrCreate([], ['total' => 0]);
        $record->increment('total');

        AnalyticsService::event('botao', 'press');

        return response()->json(['total' => $record->total]);
    }

    public function total(): JsonResponse
    {
        return response()->json(['total' => $this->getTotal()]);
    }

    private function getTotal(): int
    {
        $record = ButtonPress::first();
        return $record ? $record->total : 0;
    }

    private function seo(int $total): array
    {
        $formatted = number_format($total, 0, ',', '.');

        return [
            'title' => "Aperta o Botão — Já Apertaram {$formatted} Vezes",
            'description' => 'Um botão. Sem explicação. Sem propósito. Já apertaram milhares de vezes. Qual é a tua resistência?',
            'og_title' => "🔴 Aperta o Botão — {$formatted} pessoas não resistiram",
            'og_description' => 'Um botão vermelho. Sem motivo. Quantas vezes vais apertar?',
            'og_image' => asset('images/og/botao.png'),
            'canonical' => 'https://botao.' . config('app.base_domain'),
        ];
    }
}
