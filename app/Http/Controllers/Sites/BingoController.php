<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class BingoController extends Controller
{
    private array $quadrados = [
        "Já disseste «tá bom» e as pessoas ficaram confusas",
        "Já tentaste pagar com PIX",
        "Já estavas 40 min em «vou já»",
        "Já ligaste o Spotify para ouvir funk e o teu chefe apareceu",
        "Já pediste «suco» e trouxeram «sumo»",
        "Já disseste «bicha» no sentido errado",
        "Já saudaste alguém com «oi» e sentiste o julgamento",
        "Já custou perceber um sotaque do Norte",
        "Já levaste choque no preço da electricidade",
        "Já comparaste o tempo de PT ao do Brasil 10 vezes",
        "Já pediste atendimento e esperaste 45 min",
        "Já ligaste o aquecedor em Julho",
        "FREE — És imigrante em Portugal",
        "Já mandaste áudio de 3 minutos no WhatsApp",
        "Já recebeste carta das Finanças e não percebeste nada",
        "Já disseste «não tem não» e alguém ficou com cara estranha",
        "Já saíste de casa com guarda-chuva e estava sol",
        "Já pagaste taxa de emissão do cartão de cidadão",
        "Já tentaste pedir churrasco e recebeste algo diferente",
        "Já comeste pastel de nata e achaste que era outro",
        "Já pediste «água com gás» e vieram com cara de «porquê?»",
        "Já explicaste que o Brasil tem mais de um estado",
        "Já enviaste dinheiro para o Brasil via Remessa Online",
        "Já saudaste alguém com 2 beijinhos e foi estranho",
        "Já faltaste a um evento por causa do SEF/AIMA",
    ];

    public function index()
    {
        AnalyticsService::pageView('bingo');

        return view('sites.bingo.index', [
            'seo' => $this->seo(),
            'quadrados' => $this->quadrados,
        ]);
    }

    private function seo(): array
    {
        return [
            'title' => 'Bingo do Imigrante Brasileiro em Portugal — Quantos Já Viveste?',
            'description' => 'A cartela que todo brasileiro em Portugal vai reconhecer. Marca os quadrados e descobre o teu nível de integração.',
            'og_title' => '🎯 Bingo do Imigrante — quantos já te aconteceram?',
            'og_description' => 'Já disseste "vou já" e ficaste 3 horas? Já ficaste confuso com "bicha"? Joga o Bingo!',
            'og_image' => asset('images/og/bingo.png'),
            'canonical' => route('bingo.index'),
        ];
    }
}
