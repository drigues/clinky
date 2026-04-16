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
                'url' => route('desculpometro.index'),
                'live' => true,
                'badge' => 'top',
            ],
            [
                'slug' => 'botao',
                'title' => 'Aperta o Botão',
                'emoji' => '🔴',
                'tagline' => 'Um botão. Sem explicação.',
                'color' => 'red',
                'url' => route('botao.index'),
                'live' => true,
                'badge' => 'trending',
            ],
            [
                'slug' => 'nomeador',
                'title' => 'Nomeador de Grupos',
                'emoji' => '💬',
                'tagline' => 'Nomes épicos para o teu WhatsApp',
                'color' => 'lime',
                'url' => route('nomeador.index'),
                'live' => true,
                'badge' => null,
            ],
            [
                'slug' => 'horoscopo',
                'title' => 'Horóscopo Inútil',
                'emoji' => '🔮',
                'tagline' => 'Previsões 100% inventadas',
                'color' => 'purple',
                'url' => route('horoscopo.index'),
                'live' => true,
                'badge' => null,
            ],
            [
                'slug' => 'nome',
                'title' => 'Analisador de Nome',
                'emoji' => '🧬',
                'tagline' => 'Descobre o que o teu nome diz',
                'color' => 'teal',
                'url' => route('nome.index'),
                'live' => true,
                'badge' => null,
            ],
            [
                'slug' => 'bingo',
                'title' => 'Bingo do Imigrante',
                'emoji' => '🎯',
                'tagline' => 'Reconheces a tua vida em Portugal?',
                'color' => 'teal',
                'url' => route('bingo.index'),
                'live' => true,
                'badge' => 'ptbr',
            ],
            [
                'slug' => 'conversor',
                'title' => 'Conversor PT ↔ BR',
                'emoji' => '🇵🇹🇧🇷',
                'tagline' => 'Traduz entre português e brasileiro',
                'color' => 'yellow',
                'url' => route('conversor.index'),
                'live' => true,
                'badge' => 'ptbr',
            ],
            [
                'slug' => 'quiz',
                'title' => 'Sou mais BR ou PT?',
                'emoji' => '🤔',
                'tagline' => 'Descobre o teu nível de sotaque',
                'color' => 'pink',
                'url' => route('quiz.index'),
                'live' => true,
                'badge' => 'ptbr',
            ],
            [
                'slug' => 'corporativo',
                'title' => 'Tradutor Corporativo',
                'emoji' => '💼',
                'tagline' => 'O que realmente querem dizer',
                'color' => 'teal',
                'url' => route('corporativo.index'),
                'live' => true,
                'badge' => null,
            ],
        ]);

        return view('hub.home', compact('sites'));
    }
}
