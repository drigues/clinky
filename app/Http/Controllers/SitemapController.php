<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sites = [
            ['url' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['url' => route('desculpometro.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => route('botao.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => route('nomeador.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => route('horoscopo.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => route('nome.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => route('bingo.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('conversor.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('quiz.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('corporativo.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        $content = view('hub.sitemap', compact('sites'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
