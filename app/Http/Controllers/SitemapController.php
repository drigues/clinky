<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseDomain = config('app.base_domain');

        $sites = [
            ['url' => "https://{$baseDomain}", 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['url' => "https://desculpometro.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => "https://botao.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => "https://nomeador.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => "https://horoscopo.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => "https://nome.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => "https://bingo.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => "https://conversor.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => "https://quiz.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => "https://corporativo.{$baseDomain}", 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        $content = view('hub.sitemap', compact('sites'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
