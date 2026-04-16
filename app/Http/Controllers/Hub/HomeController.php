<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $sites = collect([
            [
                'slug' => 'desculpometro',
                'title' => 'Desculpómetro',
                'emoji' => '😅',
                'tagline' => 'Gera a desculpa perfeita',
                'color' => 'orange',
                'url' => 'https://desculpometro.' . config('app.base_domain'),
                'live' => false,
                'tag' => 'Top',
            ],
            [
                'slug' => 'botao',
                'title' => 'Aperta o Botão',
                'emoji' => '🔴',
                'tagline' => 'Um botão. Sem explicação.',
                'color' => 'red',
                'url' => 'https://botao.' . config('app.base_domain'),
                'live' => false,
                'tag' => 'Em Alta',
            ],
            [
                'slug' => 'nomeador',
                'title' => 'Nomeador de Grupos',
                'emoji' => '💬',
                'tagline' => 'Nomes épicos para o teu WhatsApp',
                'color' => 'lime',
                'url' => 'https://nomeador.' . config('app.base_domain'),
                'live' => false,
                'tag' => null,
            ],
            [
                'slug' => 'horoscopo',
                'title' => 'Horóscopo Inútil',
                'emoji' => '🔮',
                'tagline' => 'Previsões 100% inventadas',
                'color' => 'purple',
                'url' => 'https://horoscopo.' . config('app.base_domain'),
                'live' => false,
                'tag' => null,
            ],
            [
                'slug' => 'nome',
                'title' => 'Analisador de Nome',
                'emoji' => '🎯',
                'tagline' => 'Descobre o que o teu nome diz',
                'color' => 'blue',
                'url' => 'https://nome.' . config('app.base_domain'),
                'live' => false,
                'tag' => null,
            ],
            [
                'slug' => 'bingo',
                'title' => 'Bingo do Imigrante',
                'emoji' => '🎯',
                'tagline' => 'Reconheces a tua vida em Portugal?',
                'color' => 'teal',
                'url' => 'https://bingo.' . config('app.base_domain'),
                'live' => false,
                'tag' => 'PT/BR',
            ],
            [
                'slug' => 'conversor',
                'title' => 'Conversor PT ↔ BR',
                'emoji' => '🇵🇹🇧🇷',
                'tagline' => 'Traduz entre português e brasileiro',
                'color' => 'yellow',
                'url' => 'https://conversor.' . config('app.base_domain'),
                'live' => false,
                'tag' => 'PT/BR',
            ],
            [
                'slug' => 'quiz',
                'title' => 'Sou mais BR ou PT?',
                'emoji' => '🤔',
                'tagline' => 'Descobre o teu nível de sotaque',
                'color' => 'pink',
                'url' => 'https://quiz.' . config('app.base_domain'),
                'live' => false,
                'tag' => 'PT/BR',
            ],
            [
                'slug' => 'corporativo',
                'title' => 'Tradutor Corporativo',
                'emoji' => '💼',
                'tagline' => 'O que realmente querem dizer',
                'color' => 'teal',
                'url' => 'https://corporativo.' . config('app.base_domain'),
                'live' => false,
                'tag' => null,
            ],
        ]);

        return view('hub.home', compact('sites'));
    }
}
