<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class QuizController extends Controller
{
    private array $perguntas = [
        [
            'pergunta' => 'Quando marcas encontro às 15h, a que horas chegas?',
            'opcoes' => [
                ['texto' => '15h em ponto', 'pt' => 10, 'br' => 0],
                ['texto' => '15h10, peço desculpa', 'pt' => 7, 'br' => 3],
                ['texto' => '15h30, "já vou"', 'pt' => 2, 'br' => 8],
                ['texto' => '16h, mando áudio a explicar', 'pt' => 0, 'br' => 10],
            ],
        ],
        [
            'pergunta' => 'O que é uma «bicha» para ti?',
            'opcoes' => [
                ['texto' => 'Uma fila de pessoas', 'pt' => 10, 'br' => 0],
                ['texto' => 'Um insecto', 'pt' => 0, 'br' => 5],
                ['texto' => 'Depende do contexto...', 'pt' => 5, 'br' => 5],
                ['texto' => 'Ainda me confundo', 'pt' => 2, 'br' => 8],
            ],
        ],
        [
            'pergunta' => 'Como pedes um café?',
            'opcoes' => [
                ['texto' => 'Uma bica, faz favor', 'pt' => 10, 'br' => 0],
                ['texto' => 'Um cafezinho, por favor', 'pt' => 0, 'br' => 10],
                ['texto' => 'Um expresso', 'pt' => 5, 'br' => 5],
                ['texto' => 'Aponto para o balcão', 'pt' => 3, 'br' => 7],
            ],
        ],
        [
            'pergunta' => 'Alguém te pisa no autocarro. O que fazes?',
            'opcoes' => [
                ['texto' => 'Digo "desculpe" mesmo sendo eu a vítima', 'pt' => 10, 'br' => 0],
                ['texto' => 'Olho feio mas não digo nada', 'pt' => 7, 'br' => 3],
                ['texto' => '"Ô amigo, cuidado aí!"', 'pt' => 0, 'br' => 10],
                ['texto' => 'Faço cara de paisagem', 'pt' => 5, 'br' => 5],
            ],
        ],
        [
            'pergunta' => 'Estás doente. O que tomas?',
            'opcoes' => [
                ['texto' => 'Chá de limão e mel', 'pt' => 8, 'br' => 2],
                ['texto' => 'Canja da avó', 'pt' => 5, 'br' => 5],
                ['texto' => 'Paracetamol e continuo', 'pt' => 10, 'br' => 0],
                ['texto' => 'Vou ao hospital por tudo', 'pt' => 0, 'br' => 10],
            ],
        ],
        [
            'pergunta' => 'Qual é o teu pequeno-almoço ideal?',
            'opcoes' => [
                ['texto' => 'Torrada com manteiga e galão', 'pt' => 10, 'br' => 0],
                ['texto' => 'Pão de queijo e café com leite', 'pt' => 0, 'br' => 10],
                ['texto' => 'Pastel de nata e bica', 'pt' => 8, 'br' => 2],
                ['texto' => 'Açaí com granola', 'pt' => 0, 'br' => 10],
            ],
        ],
        [
            'pergunta' => 'Está a chover. O que levas?',
            'opcoes' => [
                ['texto' => 'Guarda-chuva, é Portugal', 'pt' => 10, 'br' => 0],
                ['texto' => 'Nada, a chuva para daqui a pouco', 'pt' => 0, 'br' => 10],
                ['texto' => 'Casaco impermeável', 'pt' => 8, 'br' => 2],
                ['texto' => 'Cancelo os planos', 'pt' => 3, 'br' => 7],
            ],
        ],
        [
            'pergunta' => 'Como cumprimentas alguém que acabaste de conhecer?',
            'opcoes' => [
                ['texto' => 'Dois beijinhos na cara', 'pt' => 10, 'br' => 0],
                ['texto' => 'Um abraço apertado', 'pt' => 0, 'br' => 10],
                ['texto' => 'Aperto de mão', 'pt' => 7, 'br' => 3],
                ['texto' => 'Aceno de longe', 'pt' => 5, 'br' => 5],
            ],
        ],
        [
            'pergunta' => 'Recebes uma multa. Qual é a tua reacção?',
            'opcoes' => [
                ['texto' => 'Pago e reclamo em silêncio', 'pt' => 10, 'br' => 0],
                ['texto' => 'Contesto e peço recurso', 'pt' => 0, 'br' => 8],
                ['texto' => 'Pergunto se há desconto', 'pt' => 2, 'br' => 8],
                ['texto' => 'Publico no Facebook a queixar', 'pt' => 5, 'br' => 5],
            ],
        ],
        [
            'pergunta' => 'Domingo à tarde. O que fazes?',
            'opcoes' => [
                ['texto' => 'Passeio no jardim ou praia', 'pt' => 8, 'br' => 2],
                ['texto' => 'Churrasco com a família toda', 'pt' => 0, 'br' => 10],
                ['texto' => 'Café com amigos', 'pt' => 7, 'br' => 3],
                ['texto' => 'Netflix e sofá', 'pt' => 5, 'br' => 5],
            ],
        ],
    ];

    public function index()
    {
        AnalyticsService::pageView('quiz');

        // Baralhar e usar 5 aleatórias
        $perguntas = collect($this->perguntas)->shuffle()->take(5)->values()->all();

        return view('sites.quiz.index', [
            'seo' => $this->seo(),
            'perguntas' => $perguntas,
        ]);
    }

    private function seo(): array
    {
        return [
            'title' => 'Sou Mais Brasileiro ou Português? — Quiz de 5 Perguntas',
            'description' => 'Depois de anos em Portugal (ou no Brasil), quanto do outro país já absorbeste? Faz o quiz e descobre.',
            'og_title' => '🤔 Fiz o teste: sou X% português e Y% brasileiro',
            'og_description' => 'Quiz de 5 perguntas para descobrir se és mais BR ou PT. Resultado partilhável.',
            'og_image' => asset('images/og/quiz.png'),
            'canonical' => route('quiz.index'),
        ];
    }
}
