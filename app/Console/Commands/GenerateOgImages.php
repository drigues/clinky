<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateOgImages extends Command
{
    protected $signature = 'clinky:og-images {--site= : Gerar apenas um}';
    protected $description = 'Gera as OG images 1200x630 para todos os mini-sites';

    protected array $sites = [
        ['slug' => 'default',        'emoji' => '',   'titulo' => 'Clinky.cc',                 'sub' => 'Mini-sites virais, inuteis e partilhaveis', 'bg' => '#0a0a0a', 'accent' => '#c8f135'],
        ['slug' => 'desculpometro',  'emoji' => '',   'titulo' => 'Desculpometro',              'sub' => 'A desculpa perfeita, gerada por IA',         'bg' => '#1a0800', 'accent' => '#ff6b00'],
        ['slug' => 'botao',          'emoji' => '',   'titulo' => 'Aperta o Botao',              'sub' => 'Um botao. Sem explicacao.',                   'bg' => '#0a0a0a', 'accent' => '#c8f135'],
        ['slug' => 'nomeador',       'emoji' => '',   'titulo' => 'Nomeador de Grupos',          'sub' => 'Nomes epicos para o teu WhatsApp',           'bg' => '#1a0010', 'accent' => '#ff2d78'],
        ['slug' => 'horoscopo',      'emoji' => '',   'titulo' => 'Horoscopo Inutil',            'sub' => 'As estrelas encolheram os ombros',           'bg' => '#0d0020', 'accent' => '#a855f7'],
        ['slug' => 'nome',           'emoji' => '',   'titulo' => 'Analisador de Nome',          'sub' => 'O que o teu nome diz sobre ti',              'bg' => '#001a18', 'accent' => '#00d9c0'],
        ['slug' => 'bingo',          'emoji' => '',   'titulo' => 'Bingo do Imigrante',          'sub' => 'Quantas ja viveste em Portugal?',            'bg' => '#1a1500', 'accent' => '#ffd600'],
        ['slug' => 'conversor',      'emoji' => '',   'titulo' => 'Conversor PT / BR',           'sub' => 'As palavras que nos separam',                'bg' => '#001020', 'accent' => '#00aaff'],
        ['slug' => 'quiz',           'emoji' => '',   'titulo' => 'Sou mais BR ou PT?',          'sub' => 'Quiz de 5 perguntas',                        'bg' => '#0a1500', 'accent' => '#7bc900'],
        ['slug' => 'corporativo',    'emoji' => '',   'titulo' => 'Tradutor Corporativo',        'sub' => '"Vamos alinhar" = reuniao desnecessaria',    'bg' => '#1a0000', 'accent' => '#ff3b3b'],
        ['slug' => 'bolhas',         'emoji' => '',   'titulo' => 'Rebenta as Bolhas',           'sub' => 'Impossivel parar',                           'bg' => '#001a2e', 'accent' => '#00d4ff'],
        ['slug' => 'progresso',      'emoji' => '',   'titulo' => 'Progresso da Vida',           'sub' => 'Quanto ja passou?',                          'bg' => '#0a0a0a', 'accent' => '#c8f135'],
        ['slug' => 'nada',           'emoji' => '',   'titulo' => 'Nada.',                       'sub' => 'Milhares de pessoas ja viram',               'bg' => '#0a0a0a', 'accent' => '#ffffff'],
        ['slug' => 'proibido',       'emoji' => '',   'titulo' => 'O Botao Proibido',            'sub' => 'Nao carregues.',                             'bg' => '#1a0000', 'accent' => '#8b0000'],
        ['slug' => 'decisao',        'emoji' => '',   'titulo' => 'A Decisao Impossivel',        'sub' => 'Duas opcoes. Nenhuma e boa.',                 'bg' => '#0d0030', 'accent' => '#4F46E5'],
        ['slug' => 'oraculo',        'emoji' => '',   'titulo' => 'O Oraculo',                   'sub' => 'A resposta ja existe.',                      'bg' => '#1a0030', 'accent' => '#581C87'],
        ['slug' => 'lista',          'emoji' => '',   'titulo' => 'Coisas Que Nunca Vais Fazer', 'sub' => 'A lista honesta.',                           'bg' => '#1a0018', 'accent' => '#DB2777'],
        ['slug' => 'panico',         'emoji' => '',   'titulo' => 'Modo Panico',                 'sub' => 'Activa a crise.',                            'bg' => '#1a0000', 'accent' => '#DC2626'],
        ['slug' => 'conquistas',     'emoji' => '',   'titulo' => 'Conquistas do Nada',          'sub' => 'Medalhas por zero',                          'bg' => '#1a1500', 'accent' => '#CA8A04'],
    ];

    public function handle(): int
    {
        $dir = public_path('images/og');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filtro = $this->option('site');
        $sites = $filtro
            ? array_values(array_filter($this->sites, fn ($s) => $s['slug'] === $filtro))
            : $this->sites;

        foreach ($sites as $s) {
            $this->gerar($s, $dir);
            $this->info("  {$s['slug']}.png");
        }

        return 0;
    }

    protected function gerar(array $s, string $dir): void
    {
        $w = 1200;
        $h = 630;
        $img = imagecreatetruecolor($w, $h);

        // Fundo
        [$r1, $g1, $b1] = sscanf($s['bg'], '#%02x%02x%02x');
        $bg = imagecolorallocate($img, $r1, $g1, $b1);
        imagefill($img, 0, 0, $bg);

        // Barra de acento a esquerda
        [$r2, $g2, $b2] = sscanf($s['accent'], '#%02x%02x%02x');
        $accent = imagecolorallocate($img, $r2, $g2, $b2);
        imagefilledrectangle($img, 0, 0, 12, $h, $accent);

        $white = imagecolorallocate($img, 255, 255, 255);
        $grey  = imagecolorallocate($img, 160, 160, 160);

        // Encontrar font TTF disponivel
        $bold = $this->findFont('bold');
        $reg  = $this->findFont('regular');

        if ($bold && $reg) {
            // Titulo
            imagettftext($img, 58, 0, 80, 280, $white, $bold, $s['titulo']);
            // Subtitulo
            imagettftext($img, 24, 0, 80, 340, $grey, $reg, $s['sub']);
            // URL
            $urlText = 'clinky.cc' . ($s['slug'] !== 'default' ? '/' . $s['slug'] : '');
            imagettftext($img, 18, 0, 80, 560, $accent, $bold, $urlText);
        } else {
            // Fallback sem TTF
            imagestring($img, 5, 80, 260, $s['titulo'], $white);
            imagestring($img, 3, 80, 310, $s['sub'], $grey);
            $urlText = 'clinky.cc' . ($s['slug'] !== 'default' ? '/' . $s['slug'] : '');
            imagestring($img, 4, 80, 560, $urlText, $accent);
        }

        // Linha decorativa inferior
        imagefilledrectangle($img, 0, $h - 4, $w, $h, $accent);

        imagepng($img, "{$dir}/{$s['slug']}.png");
        imagedestroy($img);
    }

    protected function findFont(string $type): ?string
    {
        $candidates = $type === 'bold'
            ? [
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/System/Library/Fonts/HelveticaNeue.ttc',
                '/System/Library/Fonts/Helvetica.ttc',
                '/Library/Fonts/Arial Unicode.ttf',
                '/System/Library/Fonts/Geneva.ttf',
            ]
            : [
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/System/Library/Fonts/HelveticaNeue.ttc',
                '/System/Library/Fonts/Helvetica.ttc',
                '/Library/Fonts/Arial Unicode.ttf',
                '/System/Library/Fonts/Geneva.ttf',
            ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
