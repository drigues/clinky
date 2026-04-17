<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class ListaController extends Controller
{
    private array $itens = [
        'Aprender a tocar guitarra',
        'Começar a correr regularmente',
        'Ler todos os livros que comprei',
        'Organizar as fotos do telemóvel',
        'Aprender uma língua nova',
        'Fazer o curso online que comprei em promoção',
        'Responder àquele email de há 3 meses',
        'Ir ao médico para aquela coisa',
        'Limpar o closet de uma vez por todas',
        'Escrever aquele livro/blog/podcast',
        'Ligar para aquela pessoa que perdi o contacto',
        'Começar a meditar',
        'Ter uma rotina de manhã',
        'Parar de dizer "amanhã começo"',
        'Guardar 10% do salário todos os meses',
        'Aprender a cozinhar de verdade',
        'Fazer aquela viagem de sonho',
        'Ir ao ginásio mais de 2 vezes por semana',
        'Acabar de ver aquela série que ficou a meio',
        'Fazer um detox digital de 1 semana',
        'Acordar às 6h todos os dias',
        'Beber 2 litros de água por dia',
        'Redecorar o quarto/sala',
        'Aprender a investir',
        'Ter um jardim ou plantas em casa',
        'Fazer voluntariado',
        'Aprender a dançar',
        'Tirar carta de condução/mota',
        'Criar uma aplicação/site próprio',
        'Finalmente limpar o email (inbox zero)',
    ];

    public function index()
    {
        AnalyticsService::pageView('lista');

        return view('sites.lista.index', [
            'seo'        => $this->seo(),
            'itens_json' => json_encode($this->itens),
            'total'      => count($this->itens),
        ]);
    }

    private function seo(): array
    {
        return [
            'title'          => 'Coisas Que Nunca Vais Fazer — A Lista',
            'description'    => 'A bucket list que ninguém vai completar. Marca as que se aplicam a ti e descobre o teu nível de procrastinação existencial. Sem registo, sem julgamento.',
            'og_title'       => '😅 Marquei quase tudo. E tu?',
            'og_description' => 'A lista honesta de coisas que nunca vais fazer. Marca as tuas.',
            'og_image'       => asset('images/og/lista.png'),
            'canonical'      => route('lista.index'),
        ];
    }
}
