<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clinky.cc — Registo de mini-sites
    |--------------------------------------------------------------------------
    |
    | Fonte única de verdade para a homepage. Adicionar site = adicionar entrada.
    | A homepage lê este array via HomeController e renderiza genericamente.
    |
    | Campos obrigatórios: slug, title, emoji, desc, bg, text, size, live
    | Campos opcionais: category, tag, tag_style, decoration, ghost_emoji, featured
    |
    | Apenas UM site pode ter 'featured' => true (fica destacado ao lado do hero).
    | Cada linha no desktop soma 12 colunas: sm=3, md=4, lg=5, banner=12.
    |
    */

    'sites' => [

        // ───────── FEATURED (destacado ao lado do hero) ─────────
        [
            'slug'       => 'desculpometro',
            'title'      => 'Desculpómetro',
            'emoji'      => '😅',
            'desc'       => 'Gera a desculpa perfeita em 1 segundo. Com IA, criatividade e zero responsabilidade.',
            'bg'         => '#FF5722',
            'text'       => 'light',
            'size'       => 'featured',
            'category'   => 'IA',
            'tag'        => 'TOP',
            'live'       => true,
            'featured'   => true,
        ],

        // ───────── ROW 2 · 4 × sm = 12 ─────────
        [
            'slug'        => 'botao',
            'title'       => 'Aperta o Botão',
            'emoji'       => '🔴',
            'desc'        => '1M+ apertaram.',
            'bg'          => 'radial-gradient(circle at 50% 55%, #E63946 0%, #B91C2C 85%)',
            'text'        => 'light',
            'size'        => 'sm',
            'category'    => 'Experiência',
            'tag'         => 'EM ALTA',
            'tag_style'   => 'ghost',
            'decoration'  => 'pulse-ball',
            'live'        => true,
        ],
        [
            'slug'     => 'nomeador',
            'title'    => 'Nomeador de Grupos',
            'emoji'    => '💬',
            'desc'     => 'Chega de "Família 🏠".',
            'bg'       => '#FF3E8A',
            'text'     => 'light',
            'size'     => 'sm',
            'category' => 'WhatsApp',
            'live'     => true,
        ],
        [
            'slug'        => 'horoscopo',
            'title'       => 'Horóscopo Inútil',
            'emoji'       => '🔮',
            'desc'        => '100% inventado.',
            'bg'          => '#7C3AED',
            'text'        => 'light',
            'size'        => 'sm',
            'category'    => 'Pseudo',
            'decoration'  => 'stars',
            'live'        => true,
        ],
        [
            'slug'     => 'nome',
            'title'    => 'Analisador de Nome',
            'emoji'    => '🧬',
            'desc'     => '73% de "ciência".',
            'bg'       => '#14B8A6',
            'text'     => 'light',
            'size'     => 'sm',
            'category' => 'IA',
            'live'     => true,
        ],

        // ───────── ROW 3 · md + lg + sm = 12 ─────────
        [
            'slug'        => 'bingo',
            'title'       => 'Bingo do Imigrante',
            'emoji'       => '🎯',
            'desc'        => 'Quantas já te aconteceram em Portugal?',
            'bg'          => '#FBBF24',
            'text'        => 'dark',
            'size'        => 'md',
            'category'    => 'Imigrante',
            'tag'         => 'PT / BR',
            'decoration'  => 'mini-grid',
            'live'        => true,
        ],
        [
            'slug'        => 'conversor',
            'title'       => 'Conversor PT ↔ BR',
            'emoji'       => '🔁',
            'desc'        => 'Bicha ou fila? Autocarro ou ônibus? O guia definitivo.',
            'bg'          => '#2563EB',
            'text'        => 'light',
            'size'        => 'lg',
            'category'    => 'Língua',
            'tag'         => 'PT / BR',
            'tag_style'   => 'ghost',
            'decoration'  => 'flags',
            'ghost_emoji' => '🇵🇹',
            'live'        => true,
        ],
        [
            'slug'     => 'quiz',
            'title'    => 'Sou mais BR ou PT?',
            'emoji'    => '🤔',
            'desc'     => '5 perguntas.',
            'bg'       => '#84CC16',
            'text'     => 'dark',
            'size'     => 'sm',
            'category' => 'Quiz',
            'tag'      => 'PT / BR',
            'live'     => true,
        ],

        // ───────── ROW 4 · 4 × sm = 12 ─────────
        [
            'slug'     => 'corporativo',
            'title'    => 'Tradutor Corporativo',
            'emoji'    => '💼',
            'desc'     => 'Traduz o jargão.',
            'bg'       => '#0F172A',
            'text'     => 'light',
            'size'     => 'sm',
            'category' => 'Escritório',
            'live'     => true,
        ],
        [
            'slug'        => 'bolhas',
            'title'       => 'Rebenta as Bolhas',
            'emoji'       => '🫧',
            'desc'        => 'Não consegues parar.',
            'bg'          => '#06B6D4',
            'text'        => 'light',
            'size'        => 'sm',
            'category'    => 'Sensorial',
            'decoration'  => 'bubbles',
            'live'        => true,
        ],
        [
            'slug'        => 'progresso',
            'title'       => 'Progresso da Vida',
            'emoji'       => '⏳',
            'desc'        => 'Quanto já passou?',
            'bg'          => '#18181B',
            'text'        => 'light',
            'size'        => 'sm',
            'category'    => 'Closure',
            'decoration'  => 'progress',
            'live'        => true,
        ],
        [
            'slug'        => 'nada',
            'title'       => 'Nada',
            'emoji'       => '·',
            'desc'        => 'Literalmente nada.',
            'bg'          => '#0A0A0A',
            'text'        => 'light',
            'size'        => 'sm',
            'category'    => 'Curiosidade',
            'decoration'  => 'void',
            'live'        => true,
        ],

        // ───────── PENDENTES (live: false até serem construídos) ─────────

        // ROW 5 · lg + md + sm = 12
        [
            'slug'     => 'decisao',
            'title'    => 'A Decisão Impossível',
            'emoji'    => '🤯',
            'desc'     => 'Duas opções. Nenhuma é boa. O que diz isso sobre ti?',
            'bg'       => '#4F46E5',
            'text'     => 'light',
            'size'     => 'lg',
            'category' => 'Paralisia',
            'live'     => false,
        ],
        [
            'slug'     => 'oraculo',
            'title'    => 'O Oráculo',
            'emoji'    => '👁️',
            'desc'     => 'Pergunta. A resposta já existe.',
            'bg'       => '#581C87',
            'text'     => 'light',
            'size'     => 'md',
            'category' => 'Barnum',
            'live'     => false,
        ],
        [
            'slug'      => 'proibido',
            'title'     => 'Botão Proibido',
            'emoji'     => '🚫',
            'desc'      => 'Não carregues.',
            'bg'        => '#881337',
            'text'      => 'light',
            'size'      => 'sm',
            'category'  => 'Reactance',
            'live'      => true,
        ],

        // ROW 6 · 4 × sm = 12
        [
            'slug'     => 'panico',
            'title'    => 'Modo Pânico',
            'emoji'    => '🚨',
            'desc'     => 'Activa a crise.',
            'bg'       => '#DC2626',
            'text'     => 'light',
            'size'     => 'sm',
            'category' => 'Urgência',
            'live'     => false,
        ],
        [
            'slug'     => 'tempo',
            'title'    => 'Quanto Tempo Perdeste?',
            'emoji'    => '⏰',
            'desc'     => 'Vais querer saber.',
            'bg'       => '#D97706',
            'text'     => 'light',
            'size'     => 'sm',
            'category' => 'Culpa',
            'live'     => false,
        ],
        [
            'slug'     => 'lista',
            'title'    => 'Coisas Que Nunca Vais Fazer',
            'emoji'    => '✅',
            'desc'     => 'A lista honesta.',
            'bg'       => '#DB2777',
            'text'     => 'light',
            'size'     => 'sm',
            'category' => 'Identificação',
            'live'     => false,
        ],
        [
            'slug'     => 'conquistas',
            'title'    => 'Conquistas do Nada',
            'emoji'    => '🏆',
            'desc'     => 'Medalhas por zero.',
            'bg'       => '#CA8A04',
            'text'     => 'dark',
            'size'     => 'sm',
            'category' => 'Completionism',
            'live'     => false,
        ],

        // ROW 7 · banner = 12
        [
            'slug'     => 'ouviste',
            'title'    => 'Já Ouviste Isto?',
            'emoji'    => '🎧',
            'desc'     => 'Sons que nunca ouviste mas conheces. Descrições geradas por IA, inexplicavelmente precisas.',
            'bg'       => '#0369A1',
            'text'     => 'light',
            'size'     => 'banner',
            'category' => 'Sons',
            'live'     => false,
        ],

    ],

];
