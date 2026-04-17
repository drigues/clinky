<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ClinkyReview extends Command
{
    protected $signature = 'clinky:review
        {--site= : Rever apenas um slug específico}
        {--visual : Tirar screenshots mobile e desktop}
        {--fix : Mostrar sugestões de correcção}';

    protected $description = 'Revê todos os mini-sites do Clinky.cc';

    /** Lista canónica — manter sincronizada com config/clinky.php */
    protected array $sites = [
        ['slug' => '',               'nome' => 'Hub Homepage',          'usa_api' => false, 'categoria' => 'hub'],
        ['slug' => 'desculpometro',  'nome' => 'Desculpómetro',         'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'botao',          'nome' => 'Aperta o Botão',        'usa_api' => false, 'categoria' => 'db'],
        ['slug' => 'nomeador',       'nome' => 'Nomeador de Grupos',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'horoscopo',      'nome' => 'Horóscopo Inútil',      'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'nome',           'nome' => 'Analisador de Nome',    'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'bingo',          'nome' => 'Bingo do Imigrante',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'conversor',      'nome' => 'Conversor PT/BR',       'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'quiz',           'nome' => 'Sou mais BR ou PT?',    'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'corporativo',    'nome' => 'Tradutor Corporativo',  'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'bolhas',         'nome' => 'Rebenta as Bolhas',     'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'progresso',      'nome' => 'Progresso da Vida',     'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'nada',           'nome' => 'Nada',                  'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'proibido',       'nome' => 'O Botão Proibido',      'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'decisao',        'nome' => 'A Decisão Impossível',  'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'oraculo',        'nome' => 'O Oráculo',             'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'lista',          'nome' => 'Coisas Que Nunca Vais Fazer', 'usa_api' => false, 'categoria' => 'estatico'],
        ['slug' => 'panico',         'nome' => 'Modo Pânico',           'usa_api' => true,  'categoria' => 'ia'],
        ['slug' => 'conquistas',     'nome' => 'Conquistas do Nada',    'usa_api' => false, 'categoria' => 'estatico'],
    ];

    protected array $resultados = [];
    protected string $baseUrl;

    public function handle(): int
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
        $filtro = $this->option('site');

        $sites = $filtro
            ? array_values(array_filter($this->sites, fn ($s) => $s['slug'] === $filtro))
            : $this->sites;

        if (empty($sites)) {
            $this->error("Site '{$filtro}' não encontrado.");
            return 1;
        }

        $this->info('');
        $this->info('  A rever ' . count($sites) . ' sites em ' . $this->baseUrl);
        $this->info('');

        foreach ($sites as $site) {
            $this->revSite($site);
        }

        $this->mostrarTabela();
        $this->gravarRelatorio();

        if ($this->option('visual')) {
            $this->correrVisual($sites);
        }

        $totalFalhas = collect($this->resultados)->sum('falhas');
        $this->info('');
        $this->info("  Relatorio: storage/app/review-report.md");

        return $totalFalhas > 0 ? 1 : 0;
    }

    /* -------------------------------------------------------------- */
    /*  REVISÃO POR SITE                                              */
    /* -------------------------------------------------------------- */

    protected function revSite(array $site): void
    {
        $slug = $site['slug'];
        $url  = $slug === '' ? $this->baseUrl : $this->baseUrl . '/' . $slug;
        $this->line("  -> {$site['nome']} ({$url})");

        $checks = [];

        // 1. HTTP response
        try {
            $r = Http::timeout(10)->get($url);
            $checks['http_200']        = $r->status() === 200;
            $checks['content_length']  = strlen($r->body()) > 1000;
            $html                      = $r->body();
        } catch (\Throwable $e) {
            $this->warn("    X Falha de rede: {$e->getMessage()}");
            $this->resultados[$slug ?: 'hub'] = [
                'site'     => $site,
                'url'      => $url,
                'checks'   => ['http_200' => false],
                'falhas'   => 1,
                'sucessos' => 0,
                'avisos'   => [],
            ];
            return;
        }

        $crawler = new Crawler($html);
        $avisos  = [];

        // 2. SEO essencial
        $checks['tem_title']       = $crawler->filter('title')->count() > 0;
        $checks['tem_description'] = $crawler->filter('meta[name=description]')->count() > 0;
        $checks['tem_viewport']    = $crawler->filter('meta[name=viewport]')->count() > 0;
        $checks['tem_canonical']   = $crawler->filter('link[rel=canonical]')->count() > 0;

        // Title entre 30-65 chars
        if ($checks['tem_title']) {
            $titleText = trim($crawler->filter('title')->first()->text());
            $len = mb_strlen($titleText);
            $checks['title_len_ok'] = $len >= 30 && $len <= 65;
            if (!$checks['title_len_ok']) {
                $avisos[] = "Title tem {$len} chars (ideal 30-65): '{$titleText}'";
            }
        }

        // Description entre 120-160 chars
        if ($checks['tem_description']) {
            $desc = $crawler->filter('meta[name=description]')->first()->attr('content') ?? '';
            $len = mb_strlen($desc);
            $checks['desc_len_ok'] = $len >= 120 && $len <= 160;
            if (!$checks['desc_len_ok']) {
                $avisos[] = "Description tem {$len} chars (ideal 120-160)";
            }
        }

        // 3. Open Graph
        $checks['og_title']       = $crawler->filter('meta[property="og:title"]')->count() > 0;
        $checks['og_description'] = $crawler->filter('meta[property="og:description"]')->count() > 0;
        $checks['og_image']       = $crawler->filter('meta[property="og:image"]')->count() > 0;
        $checks['og_url']         = $crawler->filter('meta[property="og:url"]')->count() > 0;
        $checks['og_type']        = $crawler->filter('meta[property="og:type"]')->count() > 0;

        // OG image existe no disco?
        if ($checks['og_image']) {
            $ogImageUrl = $crawler->filter('meta[property="og:image"]')->first()->attr('content') ?? '';
            $parsedPath = parse_url($ogImageUrl, PHP_URL_PATH) ?? '';
            if ($parsedPath) {
                $path = public_path($parsedPath);
                $checks['og_image_existe'] = file_exists($path);
                if (!$checks['og_image_existe']) {
                    $avisos[] = "OG image nao existe em disco: {$path}";
                }
            }
        }

        // 4. JSON-LD (excepto hub)
        if ($slug !== '') {
            $checks['tem_jsonld'] = $crawler->filter('script[type="application/ld+json"]')->count() > 0;
        }

        // 5. Tipografia — o h1/h2 deve ser grande OU o site tem design headless legítimo
        $semHeadingPorDesign = ['', 'botao', 'proibido', 'nada']; // hub usa CSS puro; outros sem h1 por design

        if (in_array($slug, $semHeadingPorDesign, true)) {
            $checks['heading_grande'] = true; // skip — é por design
        } else {
            // Procura o primeiro heading em qualquer nível
            $headings = $crawler->filter('h1, h2, h3');
            $encontrouGrande = false;
            $classesExaminadas = [];

            $headings->each(function ($node) use (&$encontrouGrande, &$classesExaminadas) {
                $classes = $node->attr('class') ?? '';
                $classesExaminadas[] = $classes;
                // Aceita text-3xl em mobile (é legítimo) até text-9xl
                // Também aceita classes Tailwind arbitrárias tipo text-[...]
                if (Str::contains($classes, ['text-3xl', 'text-4xl', 'text-5xl', 'text-6xl', 'text-7xl', 'text-8xl', 'text-9xl'])
                    || preg_match('/text-\[\d+(?:\.\d+)?(?:rem|px|em|vw)\]/', $classes)
                    || preg_match('/text-\[clamp\(/', $classes)) {
                    $encontrouGrande = true;
                }
            });

            $checks['heading_grande'] = $encontrouGrande;
            if (!$encontrouGrande && $headings->count() > 0) {
                $avisos[] = "Nenhum heading com text-3xl+ ou arbitrário (classes vistas: " . implode(' | ', $classesExaminadas) . ")";
            } elseif ($headings->count() === 0) {
                $avisos[] = "Sem h1/h2/h3. Se for por design, adicionar slug a \$semHeadingPorDesign.";
            }
        }

        // 6. Margens mobile-first — varrer TODOS os elementos com classe, não só containers
        $html_classes = '';
        $crawler->filter('[class]')->each(function ($node) use (&$html_classes) {
            $html_classes .= ' ' . ($node->attr('class') ?? '');
        });

        $checks['tem_padding_mobile'] = Str::contains($html_classes, [
            'px-3', 'px-4', 'px-5', 'px-6', 'px-8',
            'p-3', 'p-4', 'p-6', 'p-8', 'p-12',
            'pl-4', 'pr-4', 'pl-6', 'pr-6',
        ]);
        $checks['tem_max_width'] = Str::contains($html_classes, [
            'max-w-', 'container', 'w-full',
        ]);

        if (!$checks['tem_padding_mobile']) {
            $avisos[] = "Nenhum padding horizontal (px-3/4/5/6/8) em nenhum elemento";
        }

        // 7. Componentes obrigatórios (excepto hub)
        if ($slug !== '') {
            // Share bar
            $temShare = Str::contains($html, ['wa.me/', 'whatsapp.com/send', 'navigator.share', 'x-data="share']);
            $checks['tem_share'] = $temShare;
            if (!$temShare) {
                $avisos[] = "Nao encontrei share bar (wa.me/navigator.share)";
            }

            // Link de volta ao hub
            $temLinkHub = $crawler->filter('a')->reduce(function ($node) {
                $href = $node->attr('href') ?? '';
                return $href === '/' || Str::contains($href, ['clinky.cc', '://clinky']) || $href === url('/');
            })->count() > 0;
            $checks['tem_link_hub'] = $temLinkHub;
        }

        // 8. Dark mode
        $checks['suporta_dark'] = Str::contains($html, ['dark:', 'prefers-color-scheme', 'bg-zinc-9', 'bg-[#0']);

        // 9. Alpine.js — não exigido na homepage (é estática)
        if ($slug !== '') {
            $checks['tem_alpine'] = Str::contains($html, ['x-data', 'Alpine.']);
        }

        // 10. CSRF (só em páginas com POST)
        if ($site['usa_api']) {
            $checks['tem_csrf'] = Str::contains($html, ['csrf-token', 'X-CSRF-TOKEN']);
        }

        // 11. Overflow
        $temOverflowFix = Str::contains($html, ['overflow-x-hidden', 'overflow-hidden']);
        if (!$temOverflowFix) {
            $avisos[] = "Nenhum overflow-x-hidden (podes querer adicionar no body)";
        }

        // 12. Acção POST (se aplicável)
        if ($site['usa_api'] && !empty($this->acaoPost($slug))) {
            $resultado = $this->testarAcao($slug);
            if ($resultado === 'skip') {
                $avisos[] = 'Teste POST saltado (CLAUDE_API_KEY não configurada em dev)';
            } else {
                $checks['acao_responde'] = $resultado;
            }
        }

        $falhas   = count(array_filter($checks, fn ($v) => $v === false));
        $sucessos = count(array_filter($checks, fn ($v) => $v === true));

        $this->resultados[$slug ?: 'hub'] = [
            'site'     => $site,
            'url'      => $url,
            'checks'   => $checks,
            'falhas'   => $falhas,
            'sucessos' => $sucessos,
            'avisos'   => $avisos,
        ];

        $status = $falhas === 0 ? (count($avisos) > 0 ? 'W ' : 'OK' ) : 'X ';
        $this->line("    {$status} {$sucessos} ok / {$falhas} falhas / " . count($avisos) . " avisos");
    }

    /* -------------------------------------------------------------- */
    /*  TESTE DE ACÇÕES (POST)                                        */
    /* -------------------------------------------------------------- */

    protected function acaoPost(string $slug): ?array
    {
        $rotas = [
            'desculpometro' => ['/desculpometro/gerar', ['situacao' => 'trabalho', 'absurdo' => 1]],
            'nome'          => ['/nome/analisar',       ['nome' => 'Teste']],
            'corporativo'   => ['/corporativo/traduzir', ['texto' => 'vamos alinhar']],
            'botao'         => ['/botao/pressionar',    []],
            'proibido'      => ['/proibido/carregar',   []],
            'decisao'       => ['/decisao/escolher',    ['escolha' => 'Nunca mais sentir frio', 'opcao' => 'a']],
            'oraculo'       => ['/oraculo/consultar',   ['pergunta' => 'Qual é o sentido da vida?']],
            'panico'        => ['/panico/activar',      ['situacao' => 'Tenho uma reunião amanhã']],
        ];

        return $rotas[$slug] ?? null;
    }

    protected function testarAcao(string $slug): bool|string
    {
        [$path, $data] = $this->acaoPost($slug);

        // Sites que dependem de Claude API: só testar se houver key configurada
        $sitesComApi = ['desculpometro', 'nome', 'corporativo', 'decisao', 'oraculo', 'panico', 'ouviste'];
        if (in_array($slug, $sitesComApi, true) && empty(config('services.anthropic.key') ?? env('CLAUDE_API_KEY'))) {
            return 'skip';
        }

        try {
            $home = Http::timeout(5)->get($this->baseUrl . '/' . $slug);
            preg_match('/<meta name="csrf-token" content="([^"]+)"/', $home->body(), $m);
            $token = $m[1] ?? '';

            $r = Http::timeout(30)
                ->withHeaders([
                    'X-CSRF-TOKEN' => $token,
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->post($this->baseUrl . $path, $data);

            return $r->status() === 200 && is_array($r->json());
        } catch (\Throwable $e) {
            return false;
        }
    }

    /* -------------------------------------------------------------- */
    /*  OUTPUT                                                         */
    /* -------------------------------------------------------------- */

    protected function mostrarTabela(): void
    {
        $this->info('');
        $this->info('===================================================');
        $this->info('  RESUMO');
        $this->info('===================================================');

        $rows = [];
        foreach ($this->resultados as $key => $res) {
            $status = $res['falhas'] === 0
                ? (count($res['avisos']) > 0 ? '<fg=yellow>AVISO</>' : '<fg=green>OK</>')
                : '<fg=red>FALHA</>';

            $rows[] = [
                $res['site']['nome'],
                $status,
                $res['sucessos'],
                $res['falhas'],
                count($res['avisos']),
            ];
        }

        $this->table(
            ['Site', 'Status', 'OK', 'Falhas', 'Avisos'],
            $rows
        );
    }

    protected function gravarRelatorio(): void
    {
        $md = "# Clinky.cc — Relatorio de Revisao\n\n";
        $md .= "_Gerado em " . now()->format('Y-m-d H:i:s') . "_\n\n";

        foreach ($this->resultados as $key => $res) {
            $icon = $res['falhas'] === 0 ? (count($res['avisos']) > 0 ? 'AVISO' : 'OK') : 'FALHA';
            $md .= "## [{$icon}] {$res['site']['nome']}\n";
            $md .= "- URL: `{$res['url']}`\n";
            $md .= "- OK: {$res['sucessos']} / Falhas: {$res['falhas']} / Avisos: " . count($res['avisos']) . "\n\n";

            $md .= "### Checks\n";
            foreach ($res['checks'] as $check => $valor) {
                $icon2 = $valor ? '[OK]' : '[FAIL]';
                $md .= "- {$icon2} `{$check}`\n";
            }

            if ($res['avisos']) {
                $md .= "\n### Avisos\n";
                foreach ($res['avisos'] as $a) {
                    $md .= "- {$a}\n";
                }
            }
            $md .= "\n---\n\n";
        }

        file_put_contents(storage_path('app/review-report.md'), $md);
    }

    /* -------------------------------------------------------------- */
    /*  VISUAL (opcional)                                              */
    /* -------------------------------------------------------------- */

    protected function correrVisual(array $sites): void
    {
        $script = base_path('review/visual.cjs');
        if (!file_exists($script)) {
            $this->warn('Visual review precisa do ficheiro review/visual.cjs (instalar com: npm i -D playwright)');
            return;
        }

        $this->info('');
        $this->info('  A gerar screenshots (mobile 375px + desktop 1280px)...');
        $slugs = implode(',', array_map(fn ($s) => $s['slug'] ?: '_hub', $sites));
        passthru("node {$script} --base={$this->baseUrl} --sites={$slugs}");
        $this->info('   Screenshots em: storage/app/screenshots/');
    }
}
