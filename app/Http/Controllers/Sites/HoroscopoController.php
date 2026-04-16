<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class HoroscopoController extends Controller
{
    private array $signos = [
        'aries'       => ['♈', 'Áries', '21 Mar – 19 Abr'],
        'touro'       => ['♉', 'Touro', '20 Abr – 20 Mai'],
        'gemeos'      => ['♊', 'Gémeos', '21 Mai – 20 Jun'],
        'caranguejo'  => ['♋', 'Caranguejo', '21 Jun – 22 Jul'],
        'leao'        => ['♌', 'Leão', '23 Jul – 22 Ago'],
        'virgem'      => ['♍', 'Virgem', '23 Ago – 22 Set'],
        'balanca'     => ['♎', 'Balança', '23 Set – 22 Out'],
        'escorpiao'   => ['♏', 'Escorpião', '23 Out – 21 Nov'],
        'sagitario'   => ['♐', 'Sagitário', '22 Nov – 21 Dez'],
        'capricornio' => ['♑', 'Capricórnio', '22 Dez – 19 Jan'],
        'aquario'     => ['♒', 'Aquário', '20 Jan – 18 Fev'],
        'peixes'      => ['♓', 'Peixes', '19 Fev – 20 Mar'],
    ];

    public function index()
    {
        AnalyticsService::pageView('horoscopo');

        return view('sites.horoscopo.index', [
            'seo' => $this->seo(),
            'signos' => $this->signos,
        ]);
    }

    public function signo(string $signo)
    {
        if (!isset($this->signos[$signo])) {
            abort(404);
        }

        AnalyticsService::pageView('horoscopo');
        AnalyticsService::event('horoscopo', 'generate');

        $data = now()->format('Y-m-d');
        $previsao = $this->gerarPrevisao($signo, $data);
        $info = $this->signos[$signo];

        return view('sites.horoscopo.signo', [
            'seo' => $this->seoSigno($signo, $info),
            'signo' => $signo,
            'emoji' => $info[0],
            'nome' => $info[1],
            'datas' => $info[2],
            'previsao' => $previsao,
            'signos' => $this->signos,
        ]);
    }

    private function gerarPrevisao(string $signo, string $data): string
    {
        $seed = crc32($signo . $data);
        mt_srand($seed);

        $diaSemana = now()->locale('pt')->dayName;

        $inicio = [
            "Hoje é {$diaSemana}.",
            'As estrelas repararam em ti hoje.',
            'O universo processou o teu signo.',
            'Marte está a fazer o que Marte faz.',
            ucfirst($this->signos[$signo][1]) . ': o cosmos tomou nota.',
            'Vénus e Júpiter olharam um para o outro e encolheram os ombros.',
            'Saturno mandou-te um sinal. Ou não.',
        ];

        $meio = [
            'Algo pode ou não acontecer.',
            "Cuidado com as {$diaSemana}s.",
            'Uma pessoa que conheces pode falar contigo.',
            'Considera as tuas opções antes de as ignorares.',
            'O teu telemóvel vai tocar. Ou não.',
            'Evita decisões importantes. Ou toma-as. Tanto faz.',
            'Alguém vai pedir-te algo. Diz que sim. Ou não.',
            'Há uma pequena probabilidade de tudo correr bem.',
            'O café de hoje vai saber a café.',
        ];

        $fim = [
            'Número da sorte: ' . (mt_rand(1, 97)) . '.',
            'Cor do dia: ' . $this->corAleatoria() . '.',
            'Nível de estrelas: ' . str_repeat('⭐', mt_rand(1, 4)) . '.',
            'Previsão com ' . mt_rand(70, 99) . '% de certeza.',
            'Compatibilidade máxima: ' . $this->signos[array_keys($this->signos)[mt_rand(0, 11)]][1] . '.',
            'Sorte no amor: indefinida.',
        ];

        mt_srand();

        $seed2 = crc32($signo . $data);
        mt_srand($seed2);
        $i = mt_rand(0, count($inicio) - 1);
        $m = mt_rand(0, count($meio) - 1);
        $f = mt_rand(0, count($fim) - 1);
        mt_srand();

        return $inicio[$i] . ' ' . $meio[$m] . ' ' . $fim[$f];
    }

    private function corAleatoria(): string
    {
        $cores = ['azul profundo', 'verde esperança', 'roxo cósmico', 'laranja tímido', 'amarelo indeciso', 'rosa duvidoso', 'cinzento existencial', 'vermelho discreto'];
        return $cores[mt_rand(0, count($cores) - 1)];
    }

    private function seo(): array
    {
        $dia = now()->locale('pt')->dayName;

        return [
            'title' => 'Horóscopo Inútil — O Teu Futuro em Palavras Que Não Significam Nada',
            'description' => "O horóscopo mais honesto do mundo. Hoje é {$dia}. As estrelas não sabem mais do que isso.",
            'og_title' => '🔮 Horóscopo de hoje: as estrelas encolheram os ombros',
            'og_description' => 'Previsões diárias 100% inventadas. As estrelas não se responsabilizam.',
            'og_image' => asset('images/og/horoscopo.png'),
            'canonical' => route('horoscopo.index'),
        ];
    }

    private function seoSigno(string $signo, array $info): array
    {
        return [
            'title' => "Horóscopo de {$info[1]} — Previsão de Hoje",
            'description' => "Previsão diária para {$info[1]} ({$info[2]}). 100% inventada, surpreendentemente precisa.",
            'og_title' => "{$info[0]} Horóscopo de {$info[1]} — O que as estrelas dizem hoje",
            'og_description' => 'Descobre a tua previsão diária. As estrelas não se responsabilizam.',
            'og_image' => asset('images/og/horoscopo.png'),
            'canonical' => route('horoscopo.signo', $signo),
        ];
    }
}
