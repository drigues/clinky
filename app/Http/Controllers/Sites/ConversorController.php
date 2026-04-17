<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class ConversorController extends Controller
{
    private array $dicionario = [
        ['pt' => 'bicha', 'br' => 'fila', 'emoji' => '🧍', 'exemplo_pt' => 'Há uma bicha enorme no supermercado', 'exemplo_br' => 'A fila do banco está enorme'],
        ['pt' => 'pequeno-almoço', 'br' => 'café da manhã', 'emoji' => '☕'],
        ['pt' => 'autocarro', 'br' => 'ônibus', 'emoji' => '🚌'],
        ['pt' => 'telemóvel', 'br' => 'celular', 'emoji' => '📱'],
        ['pt' => 'frigorífico', 'br' => 'geladeira', 'emoji' => '🧊'],
        ['pt' => 'casa de banho', 'br' => 'banheiro', 'emoji' => '🚿'],
        ['pt' => 'sandes', 'br' => 'sanduíche', 'emoji' => '🥪'],
        ['pt' => 'sumo', 'br' => 'suco', 'emoji' => '🍊'],
        ['pt' => 'comboio', 'br' => 'trem', 'emoji' => '🚆'],
        ['pt' => 'ecrã', 'br' => 'tela', 'emoji' => '🖥️'],
        ['pt' => 'rebuçado', 'br' => 'bala', 'emoji' => '🍬'],
        ['pt' => 'talho', 'br' => 'açougue', 'emoji' => '🥩'],
        ['pt' => 'peúgas', 'br' => 'meias', 'emoji' => '🧦'],
        ['pt' => 'cuecas', 'br' => 'calcinha', 'emoji' => '👙', 'exemplo_pt' => 'Preciso de cuecas novas (roupa interior)', 'exemplo_br' => 'Preciso de calcinha nova'],
        ['pt' => 'rapariga', 'br' => 'menina', 'emoji' => '👧', 'exemplo_pt' => 'Aquela rapariga é simpática', 'exemplo_br' => 'Aquela menina é simpática'],
        ['pt' => 'puto', 'br' => 'menino/moleque', 'emoji' => '👦'],
        ['pt' => 'fixe', 'br' => 'legal', 'emoji' => '😎'],
        ['pt' => 'giro', 'br' => 'bonito/legal', 'emoji' => '✨'],
        ['pt' => 'betão', 'br' => 'concreto', 'emoji' => '🧱'],
        ['pt' => 'passeio', 'br' => 'calçada', 'emoji' => '🚶'],
        ['pt' => 'canalizador', 'br' => 'encanador', 'emoji' => '🔧'],
        ['pt' => 'hospedeira', 'br' => 'aeromoça', 'emoji' => '✈️'],
        ['pt' => 'empregado de mesa', 'br' => 'garçom', 'emoji' => '🍽️'],
        ['pt' => 'polícia', 'br' => 'polícia', 'emoji' => '👮', 'nota' => 'Igual! Mas em PT diz-se «o polícia» (masculino)'],
        ['pt' => 'propina', 'br' => 'mensalidade', 'emoji' => '🎓', 'exemplo_pt' => 'As propinas da faculdade estão caras', 'exemplo_br' => 'A mensalidade da faculdade está cara'],
        ['pt' => 'camisola', 'br' => 'blusa/suéter', 'emoji' => '🧥'],
        ['pt' => 'fato', 'br' => 'terno', 'emoji' => '🤵'],
        ['pt' => 'impermeável', 'br' => 'capa de chuva', 'emoji' => '🧥'],
        ['pt' => 'conduzir', 'br' => 'dirigir', 'emoji' => '🚗'],
        ['pt' => 'carta de condução', 'br' => 'carteira de motorista', 'emoji' => '🪪'],
        ['pt' => 'portagem', 'br' => 'pedágio', 'emoji' => '🛣️'],
        ['pt' => 'rotunda', 'br' => 'rotatória', 'emoji' => '🔄'],
        ['pt' => 'câmara municipal', 'br' => 'prefeitura', 'emoji' => '🏛️'],
        ['pt' => 'pingo', 'br' => 'café com leite', 'emoji' => '☕'],
        ['pt' => 'bica', 'br' => 'café expresso', 'emoji' => '☕'],
        ['pt' => 'pastelaria', 'br' => 'padaria/confeitaria', 'emoji' => '🥐'],
        ['pt' => 'conta (restaurante)', 'br' => 'conta', 'emoji' => '🧾', 'nota' => 'Igual, mas em PT nunca se gorjeta'],
        ['pt' => 'multibanco', 'br' => 'caixa eletrônico', 'emoji' => '🏧'],
        ['pt' => 'elevador', 'br' => 'elevador', 'emoji' => '🛗', 'nota' => 'Igual! Mas em PT alguns dizem «ascensor»'],
        ['pt' => 'relvado', 'br' => 'gramado', 'emoji' => '🌿'],
        ['pt' => 'esquadra', 'br' => 'delegacia', 'emoji' => '🏢'],
        ['pt' => 'miúdo', 'br' => 'criança', 'emoji' => '👶'],
        ['pt' => 'código postal', 'br' => 'CEP', 'emoji' => '📮'],
        ['pt' => 'berma', 'br' => 'acostamento', 'emoji' => '🛣️'],
        ['pt' => 'autocarro', 'br' => 'ônibus', 'emoji' => '🚌'],
        ['pt' => 'ficheiro', 'br' => 'arquivo', 'emoji' => '📁'],
        ['pt' => 'apelido', 'br' => 'sobrenome', 'emoji' => '📝', 'exemplo_pt' => 'Qual é o teu apelido?', 'exemplo_br' => 'Qual é o seu sobrenome?'],
        ['pt' => 'rato (computador)', 'br' => 'mouse', 'emoji' => '🖱️'],
        ['pt' => 'teclado', 'br' => 'teclado', 'emoji' => '⌨️', 'nota' => 'Igual! Mas as teclas têm layout diferente'],
        ['pt' => 'paragem', 'br' => 'ponto (de ônibus)', 'emoji' => '🚏'],
    ];

    public function index()
    {
        AnalyticsService::pageView('conversor');

        return view('sites.conversor.index', [
            'seo' => $this->seo(),
            'dicionario' => $this->dicionario,
        ]);
    }

    private function seo(): array
    {
        return [
            'title' => 'Conversor PT ↔ BR — O Dicionário Divertido',
            'description' => 'Bicha ou fila? Autocarro ou ônibus? Pequeno-almoço ou café da manhã? O dicionário interactivo PT ↔ BR com 50+ palavras e exemplos.',
            'og_title' => '🔁 Bicha em PT = Fila em BR. Descobre mais!',
            'og_description' => 'O dicionário divertido das diferenças entre português de Portugal e do Brasil.',
            'og_image' => asset('images/og/conversor.png'),
            'canonical' => route('conversor.index'),
        ];
    }
}
