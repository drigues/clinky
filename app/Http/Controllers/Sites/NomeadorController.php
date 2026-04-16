<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NomeadorController extends Controller
{
    private array $nomes = [
        'familia' => [
            'As Tias do Apocalipse',
            'Tribunal de Família',
            'Alerta Familiar',
            'A Família Que Deus Esqueceu',
            'Tios Sem Filtro',
            'Grupo do Drama Familiar',
            'Os Parentes de Quarentena',
            'DNA Questionável',
            'Herança em Risco',
            'Natal É Só Uma Vez (ainda bem)',
            'Sangue do Meu Sangue (infelizmente)',
            'Reunião Forçada',
            'Os Sobreviventes do Almoço de Domingo',
            'Quem Trouxe a Tia?',
            'Primos de 2.º Grau e Drama de 1.º',
            'A Família Adams Tuga',
            'Grupo de Terapia Familiar',
            'Alerta: Mãe Escreveu',
            'Quem Parte e Reparte Fica Com a Pior Parte',
            'Estamos Bem (mentira)',
        ],
        'trabalho' => [
            'Reunião que Devia Ser Email',
            'Stress Colectivo',
            'Sobreviventes da Segunda-feira',
            'Grupo do Mimimi',
            'Reunião às 17:59',
            'O Verdadeiro Trabalho',
            'LinkedIn Mas Sem Filtro',
            'Pausa Para Café (permanente)',
            'Burnout & Companhia',
            'Quem Mandou o Email?',
            'Escravatura Moderna',
            'Grupo de Suporte Laboral',
            'Os Que Fingem Trabalhar',
            'Sexta Já?',
            'Zoom Outra Vez Não',
            'Departamento do Caos',
            'Team Building (obrigatório)',
            'Relatório Que Ninguém Lê',
            'Feedback Construtivo (não)',
            'As 18h Nunca Mais Chegam',
        ],
        'amigos' => [
            'Os Sem Planos Mas Sempre Juntos',
            'Grupo de Apoio ao Preguiçoso',
            'Saímos Mas Não Saímos',
            'Desculpas Colectivas',
            'Os Que Nunca Respondem',
            'Plano Cancelado (como sempre)',
            'Grupo de Terapia Gratuita',
            'Os Inimputáveis',
            'Saída Adiada Para Sábado (ou não)',
            'Amigos de Infância e Trauma Partilhado',
            'Os Pontualmente Atrasados',
            'Cringe Colectivo',
            'Grupo dos Que Dizem "Depois Falamos"',
            'As Más Influências',
            'Não Contem Comigo (lá estarei)',
            'Os Embaixadores do Sofá',
            'Babes Sem Filtro',
            'Os Que Não Cresceram',
            'Conselho de Desesperados',
            'Alguém Tem Plano Para Hoje?',
        ],
        'casal' => [
            'Amor e Discussão',
            'Eles/Elas Não Sabem',
            'Queixinhas',
            'O Que Se Passa em Casa',
            'Casal em Terapia',
            'Amor Tóxico (mas funciona)',
            'Grupo de Apoio ao Casamento',
            'Já Falei Com Ele/Ela',
            'Contas do Mês e Drama',
            'O Netflix Não Se Escolhe Sozinho',
            'Cuecas e Sentimentos',
            'Amor Com Prazo de Validade',
            'Quem Cozinha Hoje?',
            'Grupo de Reclamações Amorosas',
            'Para Sempre (ou até sábado)',
            'Apaixonados e Irritados',
            'O Jantar Não Se Faz Sozinho',
            'Amor e Wi-Fi',
            'Discussão Construtiva (não)',
            'Red Flags que Ignorámos',
        ],
        'vizinhos' => [
            'Condomínio do Inferno',
            'Quem Deixou o Lixo Fora?',
            'Alerta: Obras às 8 da Manhã',
            'Vizinhos Anónimos',
            'O Gato de Quem É?',
            'Garagem Ocupada (outra vez)',
            'Reunião de Condomínio Impossível',
            'Grupo de Vigilância',
            'Barulho Às 3 da Manhã',
            'Os Que Nunca Pagam o Condomínio',
            'Quem Partiu o Elevador?',
            'Assembleia de Gritos',
            'Porteiro Fantasma',
            'Janelas Indiscretas',
            'Os Que Põem Música Alta',
            'Estacionamento Selvagem',
            'Churrasco na Varanda (outra vez)',
            'Festa Surpresa (para todos)',
            'Grupo de Espionagem Passiva',
            'Vizinho do 3.º Direito: Comunicado',
        ],
        'escola' => [
            'Trabalho de Grupo (eu faço tudo)',
            'Sobreviventes do Exame',
            'Apontamentos Que Ninguém Tem',
            'Quem Faltou Hoje?',
            'Grupo de Pânico Académico',
            'TPC Copiado',
            'Os Repetentes Orgulhosos',
            'Cantina Sobreviventes',
            'Professor Atrasou (vamos embora)',
            'Quem Tem o Resumo?',
            'Os Que Estudam na Véspera',
            'Grupo de Apoio ao Cábula',
            'Nota Mínima é Nota',
            'Erasmus Mas Sem Erasmus',
            'O PowerPoint Não Se Faz Sozinho',
            'Faltamos Todos?',
            'Os Intelectuais (ou não)',
            'Biblioteca Fechou (e agora?)',
            'Estágio: Trabalho Grátis',
            'Alguém Percebeu a Matéria?',
        ],
    ];

    public function index()
    {
        AnalyticsService::pageView('nomeador');

        return view('sites.nomeador.index', [
            'seo' => $this->seo(),
            'categorias' => array_keys($this->nomes),
        ]);
    }

    public function gerar(Request $request): JsonResponse
    {
        $request->validate([
            'categoria' => 'required|in:' . implode(',', array_keys($this->nomes)),
        ]);

        $lista = $this->nomes[$request->categoria];
        $keys = array_rand($lista, min(3, count($lista)));
        $sugestoes = array_map(fn($k) => $lista[$k], (array) $keys);

        AnalyticsService::event('nomeador', 'generate');

        return response()->json(['nomes' => array_values($sugestoes)]);
    }

    private function seo(): array
    {
        return [
            'title' => 'Nomeador de Grupos — Nomes Épicos para o Teu WhatsApp',
            'description' => 'Chega de grupos chamados "Família 🏠". Gera o nome perfeito para o teu grupo de WhatsApp.',
            'og_title' => '💬 O melhor nome de grupo que algures existiu',
            'og_description' => 'Gera nomes épicos para os teus grupos de WhatsApp. Grátis e partilhável.',
            'og_image' => asset('images/og/nomeador.png'),
            'canonical' => route('nomeador.index'),
        ];
    }
}
