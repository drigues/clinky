# uidesign.md — Redesign Homepage (Bento Pop)

Lê `CLAUDE.md`, `SKILL.md`, `REFERENCES/ARCHITECTURE.md` e `REFERENCES/COMPONENTS.md`.

Redesenha por completo `resources/views/hub/home.blade.php` substituindo o layout actual full-screen escuro por uma **bento grid assimétrica sobre fundo cremoso**, com cartões em cores saturadas (pop), emoji grande, tipografia bold e micro-animações por card.

Mobile-first com cards 1:1 em grelha de 2 colunas. Desktop com grid de 12 colunas e spans variáveis sem alinhamento rígido, estilo bento.

---

## Objectivo

Passar de **"landing page escura com secções full-screen scroll"** para **"bento grid luminoso com cada mini-site como tile de cor saturada"**, mantendo a mesma rota (`/`) e os mesmos links para os 9 mini-sites.

O gatilho psicológico do hub é a **curiosidade visual** — o olho salta de tile em tile, quer clicar. É o oposto do design actual, que obriga a scroll longo para ver tudo.

---

## Princípios do redesign

1. **Pop colors, não dark.** Cada card é uma cor saturada (`#FF5722`, `#E63946`, `#FF3E8A`, etc.). Fundo da página é cremoso (`#F0EDE3`), não preto.
2. **Mobile ratio 1:1.** Todos os cards são quadrados perfeitos em 2-col. Sem excepções no mobile.
3. **Desktop é bento.** Grid 12-col com cards de spans diferentes (3, 4, 5, 7 cols) e ratios variáveis (1:1, 4:3, 5:4, 2:1). Sem ordem vertical rígida.
4. **Cada card tem personalidade.** Botão tem bolha a pulsar. Horóscopo tem estrelas a cintilar. Bingo tem mini-grelha 4×4. Etc. — o visual do card é uma pista do mini-site.
5. **Tipografia com carácter.** Bricolage Grotesque (variable, bold, ligeiramente lúdica) para títulos. Figtree para corpo. JetBrains Mono para labels pequenas.

---

## Design tokens

### CSS variables (colocar em `:root`)

```css
:root {
    /* Fundo */
    --bg: #F0EDE3;
    --bg-2: #E8E3D5;
    --ink: #0A0A0A;
    --ink-soft: #3A3A3A;
    --ink-mute: #6B6B6B;

    /* Paleta dos cards — saturada, pop */
    --c-hero:        #0A0A0A;   /* hero card preto */
    --c-hero-pop:    #C6F432;   /* lime accent do hero */
    --c-desculpa:    #FF5722;   /* laranja */
    --c-botao:       #E63946;   /* vermelho */
    --c-nomeador:    #FF3E8A;   /* rosa quente */
    --c-horoscopo:   #7C3AED;   /* roxo */
    --c-nome:        #14B8A6;   /* teal */
    --c-bingo:       #FBBF24;   /* amarelo — texto escuro por cima */
    --c-conversor:   #2563EB;   /* azul */
    --c-quiz:        #84CC16;   /* lima — texto escuro */
    --c-corporativo: #0F172A;   /* slate preto */

    /* Geometria */
    --radius-card: 28px;
    --radius-inner: 14px;
    --gap: 14px;
}
```

### Tipografia

Google Fonts a importar (adicionar no `<head>` de `layouts/hub.blade.php`):

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300..800&family=Figtree:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
```

- **Display (títulos dos cards e hero):** `'Bricolage Grotesque', serif` — weight 700-800
- **Body (parágrafos, tagline):** `'Figtree', system-ui, sans-serif` — weight 400-600
- **Labels monoespaçadas (eyebrows, tags, stats):** `'JetBrains Mono', monospace` — weight 600-700, uppercase, letter-spacing 0.1-0.25em

---

## Layout responsive

### Mobile (< 720px)
Grid 2 colunas. Todos os cards `aspect-ratio: 1/1`. Hero e featured são `grid-column: span 2` (full width) com `aspect-ratio: auto` e `min-height` próprio.

### Tablet (720–1080px)
Grid 4 colunas. Alguns cards viram `span 2` para quebrar monotonia. Hero e featured continuam full width.

### Desktop (≥ 1080px)
Grid 12 colunas com spans variáveis:
- **Hero (Clinky.cc):** `grid-column: span 7; grid-row: span 2;`
- **Featured (Desculpómetro):** `grid-column: span 5; grid-row: span 2;`
- **Botão:** `span 4` × `aspect-ratio 1/1`
- **Nomeador:** `span 3` × `1/1`
- **Horóscopo:** `span 3` × `1/1`
- **Analisador de Nome:** `span 4` × `aspect-ratio 4/3`
- **Bingo:** `span 4` × `1/1`
- **Conversor:** `span 5` × `aspect-ratio 5/4`
- **Quiz:** `span 3` × `1/1`
- **Corporativo:** `span 4` × `1/1`

Não forçar row-start — deixar o CSS Grid fluir naturalmente com `grid-auto-flow: dense`. Isto garante que os cards encaixam sem espaços vazios.

---

## Estrutura base de um card

```html
<a href="{{ route('slug.index') }}" class="card card-{slug} span-{n}">
    <span class="tag">BADGE</span>                 {{-- opcional --}}
    <div class="icon">{emoji}</div>                {{-- square translúcido top-left --}}
    <div class="ghost-emoji">{emoji}</div>         {{-- emoji gigante rodado no fundo, opacity 0.08 --}}
    <span class="label">Categoria · 0N</span>      {{-- monospace small --}}
    <div class="body">
        <h3>{título}</h3>
        <p>{descrição curta}</p>
    </div>
    <div class="arrow">→</div>                     {{-- círculo bottom-right --}}
</a>
```

---

## Hero card (Clinky.cc título)

Fundo `#0A0A0A`, accent lime `#C6F432`.

```html
<div class="hero-card">
    <div class="eyebrow">Mini-sites bestas que valem a pena</div>
    <h1>Clin<span class="ky">ky</span><span class="dot">.cc</span></h1>
    <p class="tagline">Sem registo. Sem cookies. Sem sentido. Mini-experiências virais, partilháveis num toque.</p>
    <div class="stats">
        <div class="stat"><span class="num">09</span><span class="lbl">Mini-sites</span></div>
        <div class="stat"><span class="num">0</span><span class="lbl">Cookies</span></div>
        <div class="stat"><span class="num">∞</span><span class="lbl">Partilháveis</span></div>
    </div>
</div>
```

- `h1` com `font-family: 'Bricolage Grotesque'`, `font-size: clamp(3.5rem, 12vw, 8rem)`, `line-height: 0.85`, `letter-spacing: -0.04em`
- `.ky` tem `font-style: italic` e `font-variation-settings: 'wdth' 75` (variable font strecht)
- `.dot` tem `color: var(--c-hero-pop)`
- Background tem `::before` com gradiente radial subtil (lime + laranja) para profundidade

---

## Featured card (Desculpómetro destacado)

Fundo `var(--c-desculpa)`, emoji 😅 gigante rodado -8°, CTA pill branca.

```html
<a href="{{ route('desculpometro.index') }}" class="featured-card">
    <span class="tag">TOP</span>
    <div class="big-emoji">😅</div>
    <div>
        <h2>Desculpómetro</h2>
        <p class="desc">Gera a desculpa perfeita em 1 segundo. Com IA, criatividade e zero responsabilidade.</p>
        <span class="cta">Experimentar <span>→</span></span>
    </div>
</a>
```

---

## Cards individuais — especificidades

Cada card tem um toque visual que remete para o mini-site. Implementa todos estes detalhes.

### `card-botao` (vermelho, span 4)
Background radial `radial-gradient(circle at 50% 55%, #E63946 0%, #B91C2C 80%)`.
Contém uma **bolha vermelha central** (pseudo `::before` dentro de `.giant-button`) com 50% de largura, `border-radius: 50%`, gradiente interno realista e **animação pulse 2s infinita** (`transform: scale(1) → 1.05`).

### `card-horoscopo` (roxo, span 3)
Contém `.stars` absolute com 5 `<span>` dispersos (emojis ✦ ✧ ⋆) com `animation: twinkle 3s` (opacity 0.3→1, scale 1→1.3, delays diferentes).

### `card-bingo` (amarelo, span 4, **texto escuro** — classe `.on-light`)
No top-right tem uma **mini-cartela 4×4** de `<span>`s, alguns com `.on` (fundo preto), outros vazios (fundo preto 12% alpha). Visualmente representa a grelha do bingo.

### `card-conversor` (azul, span 5)
No corpo tem um elemento `.flag-pt-br` grande: `🇵🇹 ↔ 🇧🇷` com emojis a clamp 2.5-4rem. Por cima, o título normal.

### Todos os outros (`desculpa`, `nomeador`, `nome`, `quiz`, `corporativo`)
Estrutura base. O `.ghost-emoji` (180px, opacity 0.08, rodado -12°, absolute bottom-right) dá personalidade sem ruído.

### Dois cards com texto escuro (classe `.on-light`)
- **Bingo** (amarelo): `color: var(--ink)`
- **Quiz** (lima): `color: var(--ink)`

Ambos ganham `.icon { background: rgba(0,0,0,0.08) }` e `.arrow { background: rgba(0,0,0,0.08) }` para contraste.

---

## Atmosfera / detalhes

- **Grain texture:** `body::before` com SVG noise inline base64, `opacity: 0.35`, `mix-blend-mode: multiply`, `position: fixed`, `pointer-events: none`. Dá textura analógica sem ficheiro externo.
- **Status dot no nav:** pequeno ponto vermelho com `animation: pulse 2s infinite` — sinal de "live".
- **Hover nos cards:** `transform: translateY(-4px) scale(1.01)` + sombra maior + `.arrow` translada 4px/-4px.
- **Entry animation:** cada card entra com `@keyframes rise` (translate 20px→0, opacity 0→1) e stagger de 50ms via `:nth-child()`.

---

## Ficheiro `home.blade.php` — código completo

Substitui integralmente `resources/views/hub/home.blade.php` por:

```blade
@extends('layouts.hub')

@section('content')
<div class="clinky-wrap">

    {{-- NAV --}}
    <div class="clinky-nav">
        <div class="brand">
            <span class="dot"></span>
            <span>Clinky.cc · v1</span>
        </div>
        <div class="count">09 mini-sites · +11 a caminho</div>
    </div>

    {{-- BENTO GRID --}}
    <div class="bento">

        {{-- HERO --}}
        <div class="hero-card">
            <div class="eyebrow">Mini-sites bestas que valem a pena</div>
            <h1>Clin<span class="ky">ky</span><span class="dot-accent">.cc</span></h1>
            <p class="tagline">Sem registo. Sem cookies. Sem sentido. Mini-experiências virais, partilháveis num toque.</p>
            <div class="stats">
                <div class="stat"><span class="num">09</span><span class="lbl">Mini-sites</span></div>
                <div class="stat"><span class="num">0</span><span class="lbl">Cookies</span></div>
                <div class="stat"><span class="num">∞</span><span class="lbl">Partilháveis</span></div>
            </div>
        </div>

        {{-- FEATURED — DESCULPÓMETRO --}}
        <a href="{{ route('desculpometro.index') }}" class="featured-card">
            <span class="tag">TOP</span>
            <div class="big-emoji">😅</div>
            <div>
                <h2>Desculpómetro</h2>
                <p class="desc">Gera a desculpa perfeita em 1 segundo. Com IA, criatividade e zero responsabilidade.</p>
                <span class="cta">Experimentar <span>→</span></span>
            </div>
        </a>

        {{-- BOTÃO --}}
        <a href="{{ route('botao.index') }}" class="card card-botao span-4">
            <span class="tag ghost">EM ALTA</span>
            <div class="icon">🔴</div>
            <div class="giant-button"></div>
            <div class="body">
                <h3>Aperta o Botão</h3>
                <p>1M+ apertaram. Tu vais ser o próximo.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- NOMEADOR --}}
        <a href="{{ route('nomeador.index') }}" class="card card-nomeador span-3">
            <div class="icon">💬</div>
            <div class="ghost-emoji">💬</div>
            <span class="label">WhatsApp · 03</span>
            <div class="body">
                <h3>Nomeador de Grupos</h3>
                <p>Chega de "Família 🏠".</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- HORÓSCOPO --}}
        <a href="{{ route('horoscopo.index') }}" class="card card-horoscopo span-3">
            <div class="stars">
                <span>✦</span><span>✧</span><span>✦</span><span>⋆</span><span>✦</span>
            </div>
            <div class="icon">🔮</div>
            <span class="label">Pseudo · 04</span>
            <div class="body">
                <h3>Horóscopo Inútil</h3>
                <p>Previsões 100% inventadas.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- ANALISADOR DE NOME --}}
        <a href="{{ route('nome.index') }}" class="card card-nome span-4-w">
            <div class="icon">🧬</div>
            <div class="ghost-emoji">🧬</div>
            <span class="label">IA · 05</span>
            <div class="body">
                <h3>Analisador de Nome</h3>
                <p>Descobre o que o teu nome diz. Com 73% de "ciência".</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- BINGO --}}
        <a href="{{ route('bingo.index') }}" class="card card-bingo on-light span-4">
            <span class="tag">PT / BR</span>
            <div class="mini-grid">
                <span class="on"></span><span></span><span class="on"></span><span></span>
                <span></span><span class="on"></span><span class="on"></span><span></span>
                <span class="on"></span><span></span><span class="on"></span><span class="on"></span>
                <span></span><span class="on"></span><span></span><span class="on"></span>
            </div>
            <div class="icon">🎯</div>
            <span class="label">Imigrante · 06</span>
            <div class="body">
                <h3>Bingo do Imigrante</h3>
                <p>Quantas já te aconteceram em Portugal?</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- CONVERSOR --}}
        <a href="{{ route('conversor.index') }}" class="card card-conversor span-5">
            <span class="tag ghost">PT / BR</span>
            <div class="icon">🔁</div>
            <div class="ghost-emoji">🇵🇹</div>
            <span class="label">Língua · 07</span>
            <div class="body">
                <div class="flag-pt-br">🇵🇹 <span class="arrow-icon">↔</span> 🇧🇷</div>
                <h3 style="margin-top:10px;">Conversor PT ↔ BR</h3>
                <p>Bicha ou fila? Autocarro ou ônibus? O guia definitivo.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- QUIZ --}}
        <a href="{{ route('quiz.index') }}" class="card card-quiz on-light span-3">
            <span class="tag">PT / BR</span>
            <div class="icon">🤔</div>
            <span class="label">Quiz · 08</span>
            <div class="body">
                <h3>Sou mais BR ou PT?</h3>
                <p>5 perguntas. Resultado imprevisto.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- CORPORATIVO --}}
        <a href="{{ route('corporativo.index') }}" class="card card-corporativo span-4">
            <div class="icon">💼</div>
            <div class="ghost-emoji">💼</div>
            <span class="label">Escritório · 09</span>
            <div class="body">
                <h3>Tradutor Corporativo</h3>
                <p>"Vamos alinhar" = reunião que devia ser email.</p>
            </div>
            <div class="arrow">→</div>
        </a>

    </div>

    {{-- FOOTER --}}
    <footer class="clinky-footer">
        <div class="logo">Clinky<span>.cc</span></div>
        <nav>
            <a href="{{ route('privacidade') }}">Privacidade</a>
            <a href="https://thr33.xyz" target="_blank" rel="noopener">thr33.xyz</a>
            <span>© 2026</span>
        </nav>
    </footer>

</div>
@endsection

@push('styles')
<style>
    /* ======================= BENTO POP STYLES ======================= */
    :root {
        --bg: #F0EDE3;
        --bg-2: #E8E3D5;
        --ink: #0A0A0A;
        --ink-soft: #3A3A3A;
        --ink-mute: #6B6B6B;
        --c-hero:        #0A0A0A;
        --c-hero-pop:    #C6F432;
        --c-desculpa:    #FF5722;
        --c-botao:       #E63946;
        --c-nomeador:    #FF3E8A;
        --c-horoscopo:   #7C3AED;
        --c-nome:        #14B8A6;
        --c-bingo:       #FBBF24;
        --c-conversor:   #2563EB;
        --c-quiz:        #84CC16;
        --c-corporativo: #0F172A;
        --radius-card: 28px;
        --radius-inner: 14px;
        --gap: 14px;
    }
    body { background: var(--bg); color: var(--ink); font-family: 'Figtree', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
    body::before {
        content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 1; opacity: 0.35; mix-blend-mode: multiply;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' /%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.18'/%3E%3C/svg%3E");
    }

    .clinky-wrap { position: relative; z-index: 2; max-width: 1400px; margin: 0 auto; padding: 20px 16px 80px; }
    @media (min-width: 900px) { .clinky-wrap { padding: 32px 32px 120px; } }

    /* NAV */
    .clinky-nav {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 6px 24px; font-family: 'JetBrains Mono', monospace;
        font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--ink-soft);
    }
    .clinky-nav .brand { display: flex; align-items: center; gap: 8px; font-weight: 600; }
    .clinky-nav .brand .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--c-botao); animation: pulse 2s ease-in-out infinite; }
    .clinky-nav .count { color: var(--ink-mute); }
    @keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.6; transform:scale(1.3); } }

    /* GRID */
    .bento { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--gap); grid-auto-flow: dense; }
    @media (min-width: 720px)  { .bento { grid-template-columns: repeat(4, 1fr); } }
    @media (min-width: 1080px) { .bento { grid-template-columns: repeat(12, 1fr); gap: 16px; } }

    /* CARD BASE */
    .card {
        position: relative; aspect-ratio: 1 / 1; padding: 20px; border-radius: var(--radius-card);
        overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;
        background: #fff; color: var(--ink); text-decoration: none; isolation: isolate;
        transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
    }
    @media (min-width: 720px)  { .card { padding: 24px; } }
    @media (min-width: 1080px) { .card { padding: 28px; } }
    .card:hover { transform: translateY(-4px) scale(1.01); box-shadow: 0 12px 24px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.08); }
    .card:active { transform: translateY(-1px) scale(0.995); }
    .card > * { position: relative; z-index: 2; }

    .card .icon {
        width: 48px; height: 48px; border-radius: var(--radius-inner);
        display: flex; align-items: center; justify-content: center; font-size: 26px;
        background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    }
    .card.on-light .icon { background: rgba(0,0,0,0.08); }

    .card .label {
        font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.15em; opacity: 0.7; margin-top: 10px;
    }
    .card .body { margin-top: auto; }
    .card h3 {
        font-family: 'Bricolage Grotesque', serif; font-weight: 700;
        font-size: clamp(1.3rem, 3.5vw, 1.9rem); line-height: 0.95; letter-spacing: -0.02em;
        margin-bottom: 6px; font-variation-settings: 'wdth' 100;
    }
    .card p { font-size: 13px; line-height: 1.35; opacity: 0.82; max-width: 95%; }
    @media (min-width: 1080px) { .card p { font-size: 14px; } }

    .card .tag {
        position: absolute; top: 20px; right: 20px;
        font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 700;
        padding: 5px 10px; border-radius: 100px; background: rgba(255,255,255,0.95);
        color: var(--ink); text-transform: uppercase; letter-spacing: 0.1em; z-index: 3;
    }
    .card .tag.ghost { background: rgba(0,0,0,0.5); color: #fff; backdrop-filter: blur(6px); }

    .card .arrow {
        position: absolute; bottom: 20px; right: 20px; width: 36px; height: 36px; border-radius: 50%;
        background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;
        font-size: 16px; transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), background 0.3s;
    }
    .card.on-light .arrow { background: rgba(0,0,0,0.08); }
    .card:hover .arrow { transform: translate(4px, -4px); background: rgba(255,255,255,0.3); }
    .card.on-light:hover .arrow { background: rgba(0,0,0,0.15); }

    .card .ghost-emoji {
        position: absolute; font-size: 180px; opacity: 0.08; bottom: -40px; right: -30px;
        pointer-events: none; transform: rotate(-12deg); z-index: 0; line-height: 1;
    }

    /* SPANS — desktop bento */
    @media (min-width: 720px) {
        .hero-card, .featured-card { grid-column: span 4; aspect-ratio: auto; }
    }
    @media (min-width: 1080px) {
        .hero-card     { grid-column: span 7; grid-row: span 2; aspect-ratio: auto; }
        .featured-card { grid-column: span 5; grid-row: span 2; aspect-ratio: auto; }
        .span-3   { grid-column: span 3; aspect-ratio: 1/1; }
        .span-4   { grid-column: span 4; aspect-ratio: 1/1; }
        .span-5   { grid-column: span 5; aspect-ratio: 5/4; }
        .span-4-w { grid-column: span 4; aspect-ratio: 4/3; }
    }

    /* HERO CARD */
    .hero-card {
        background: var(--c-hero); color: #fff; padding: 32px; border-radius: var(--radius-card);
        position: relative; overflow: hidden; grid-column: span 2; aspect-ratio: auto;
        display: flex; flex-direction: column; justify-content: space-between; min-height: 260px;
    }
    @media (min-width: 720px)  { .hero-card { min-height: 340px; } }
    @media (min-width: 1080px) { .hero-card { padding: 48px; min-height: auto; } }
    .hero-card::before {
        content: ''; position: absolute; inset: 0; z-index: 1;
        background: radial-gradient(circle at 20% 80%, rgba(198,244,50,0.15), transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(255,87,34,0.12), transparent 60%);
    }
    .hero-card > * { position: relative; z-index: 2; }
    .hero-card .eyebrow {
        font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.25em; color: var(--c-hero-pop);
        display: flex; align-items: center; gap: 10px;
    }
    .hero-card .eyebrow::before, .hero-card .eyebrow::after {
        content: ''; flex: 1; max-width: 40px; height: 1px; background: var(--c-hero-pop); opacity: 0.4;
    }
    .hero-card h1 {
        font-family: 'Bricolage Grotesque', serif; font-weight: 800;
        font-size: clamp(3.5rem, 12vw, 8rem); line-height: 0.85; letter-spacing: -0.04em;
        margin: 24px 0 16px; font-variation-settings: 'wdth' 90;
    }
    .hero-card h1 .ky { font-style: italic; font-variation-settings: 'wdth' 75; }
    .hero-card h1 .dot-accent { color: var(--c-hero-pop); }
    .hero-card .tagline {
        font-size: clamp(15px, 2vw, 18px); color: rgba(255,255,255,0.7);
        max-width: 420px; line-height: 1.45;
    }
    .hero-card .stats { display: flex; gap: 24px; margin-top: auto; padding-top: 20px; flex-wrap: wrap; }
    .hero-card .stat { display: flex; flex-direction: column; }
    .hero-card .stat .num {
        font-family: 'Bricolage Grotesque', serif; font-size: clamp(24px, 4vw, 36px);
        font-weight: 700; line-height: 1; color: var(--c-hero-pop);
    }
    .hero-card .stat .lbl {
        font-family: 'JetBrains Mono', monospace; font-size: 10px;
        text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.5); margin-top: 6px;
    }

    /* FEATURED CARD */
    .featured-card {
        background: var(--c-desculpa); color: #fff; padding: 32px;
        border-radius: var(--radius-card); position: relative; overflow: hidden;
        grid-column: span 2; aspect-ratio: auto; min-height: 260px;
        display: flex; flex-direction: column; justify-content: space-between;
        text-decoration: none; isolation: isolate;
        transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
    }
    @media (min-width: 720px)  { .featured-card { min-height: 340px; padding: 36px; } }
    @media (min-width: 1080px) { .featured-card { padding: 48px; min-height: auto; } }
    .featured-card:hover { transform: translateY(-4px) scale(1.01); box-shadow: 0 12px 24px rgba(0,0,0,0.12); }
    .featured-card::before {
        content: ''; position: absolute; inset: 0; z-index: 1;
        background: radial-gradient(circle at 90% 10%, rgba(255,255,255,0.15), transparent 40%);
    }
    .featured-card > * { position: relative; z-index: 2; }
    .featured-card .big-emoji {
        font-size: clamp(5rem, 14vw, 9rem); line-height: 1;
        filter: drop-shadow(0 6px 20px rgba(0,0,0,0.2)); transform: rotate(-8deg);
    }
    .featured-card h2 {
        font-family: 'Bricolage Grotesque', serif; font-weight: 800;
        font-size: clamp(2rem, 5vw, 3.2rem); line-height: 0.9; letter-spacing: -0.03em;
        margin: 16px 0 10px;
    }
    .featured-card .desc { font-size: 15px; line-height: 1.4; opacity: 0.9; max-width: 90%; }
    @media (min-width: 1080px) { .featured-card .desc { font-size: 17px; } }
    .featured-card .cta {
        display: inline-flex; align-items: center; gap: 10px;
        background: #fff; color: var(--c-desculpa); padding: 12px 22px; border-radius: 100px;
        font-weight: 700; font-size: 14px; margin-top: 18px; width: fit-content;
        transition: transform 0.3s;
    }
    .featured-card:hover .cta { transform: scale(1.03); }

    /* INDIVIDUAL CARDS */
    .card-desculpa    { background: var(--c-desculpa); color: #fff; }
    .card-botao       { background: radial-gradient(circle at 50% 55%, #E63946 0%, #B91C2C 80%); color: #fff; overflow: hidden; }
    .card-nomeador    { background: var(--c-nomeador); color: #fff; }
    .card-horoscopo   { background: var(--c-horoscopo); color: #fff; }
    .card-nome        { background: var(--c-nome); color: #fff; }
    .card-bingo       { background: var(--c-bingo); color: var(--ink); }
    .card-conversor   { background: var(--c-conversor); color: #fff; }
    .card-quiz        { background: var(--c-quiz); color: var(--ink); }
    .card-corporativo { background: var(--c-corporativo); color: #fff; }

    /* BOTÃO — bolha a pulsar */
    .card-botao .giant-button {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        pointer-events: none; z-index: 1;
    }
    .card-botao .giant-button::before {
        content: ''; width: 50%; aspect-ratio: 1; border-radius: 50%;
        background: radial-gradient(circle at 35% 30%, #FF6B6B 0%, #8B0000 90%);
        box-shadow: inset 0 -8px 20px rgba(0,0,0,0.4), inset 0 4px 8px rgba(255,255,255,0.2), 0 6px 20px rgba(0,0,0,0.3);
        animation: beat 2s ease-in-out infinite;
    }
    @keyframes beat { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }

    /* HORÓSCOPO — estrelas a cintilar */
    .card-horoscopo .stars { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .card-horoscopo .stars span { position: absolute; color: rgba(255,255,255,0.5); font-size: 10px; animation: twinkle 3s ease-in-out infinite; }
    .card-horoscopo .stars span:nth-child(1) { top: 15%; left: 20%; animation-delay: 0s; }
    .card-horoscopo .stars span:nth-child(2) { top: 30%; right: 25%; animation-delay: 0.5s; font-size: 14px; }
    .card-horoscopo .stars span:nth-child(3) { top: 55%; left: 15%; animation-delay: 1s; font-size: 8px; }
    .card-horoscopo .stars span:nth-child(4) { bottom: 30%; right: 15%; animation-delay: 1.5s; font-size: 12px; }
    .card-horoscopo .stars span:nth-child(5) { top: 75%; left: 60%; animation-delay: 2s; font-size: 9px; }
    @keyframes twinkle { 0%,100% { opacity: 0.3; transform: scale(1); } 50% { opacity: 1; transform: scale(1.3); } }

    /* BINGO — mini-cartela */
    .card-bingo .mini-grid {
        position: absolute; top: 20px; right: 20px;
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; width: 52px; z-index: 2;
    }
    .card-bingo .mini-grid span { aspect-ratio: 1; background: rgba(0,0,0,0.12); border-radius: 2px; }
    .card-bingo .mini-grid span.on { background: var(--ink); }

    /* CONVERSOR — bandeiras */
    .card-conversor .flag-pt-br {
        font-size: clamp(2.5rem, 6vw, 4rem); line-height: 1; letter-spacing: -0.1em;
        display: flex; align-items: center; gap: 8px;
    }
    .card-conversor .flag-pt-br .arrow-icon {
        font-family: 'Bricolage Grotesque', serif; font-weight: 800;
        font-size: 0.6em; color: rgba(255,255,255,0.8);
    }

    /* FOOTER */
    .clinky-footer {
        margin-top: 80px; padding: 40px 20px 20px;
        display: flex; flex-direction: column; gap: 20px; align-items: center; text-align: center;
        font-family: 'JetBrains Mono', monospace; font-size: 11px;
        text-transform: uppercase; letter-spacing: 0.1em; color: var(--ink-mute);
    }
    @media (min-width: 720px) { .clinky-footer { flex-direction: row; justify-content: space-between; text-align: left; } }
    .clinky-footer .logo {
        font-family: 'Bricolage Grotesque', serif; font-weight: 800; font-size: 24px;
        color: var(--ink); text-transform: none; letter-spacing: -0.02em;
    }
    .clinky-footer .logo span { color: var(--c-desculpa); }
    .clinky-footer nav { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
    .clinky-footer a { color: var(--ink-mute); text-decoration: none; transition: color 0.2s; }
    .clinky-footer a:hover { color: var(--ink); }

    /* ENTRY ANIMATION */
    @keyframes rise { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .card, .hero-card, .featured-card { animation: rise 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) backwards; }
    .hero-card     { animation-delay: 0s; }
    .featured-card { animation-delay: 0.1s; }
    .bento > :nth-child(3)  { animation-delay: 0.15s; }
    .bento > :nth-child(4)  { animation-delay: 0.2s; }
    .bento > :nth-child(5)  { animation-delay: 0.25s; }
    .bento > :nth-child(6)  { animation-delay: 0.3s; }
    .bento > :nth-child(7)  { animation-delay: 0.35s; }
    .bento > :nth-child(8)  { animation-delay: 0.4s; }
    .bento > :nth-child(9)  { animation-delay: 0.45s; }
    .bento > :nth-child(10) { animation-delay: 0.5s; }
    .bento > :nth-child(11) { animation-delay: 0.55s; }
</style>
@endpush
```

---

## Alterações a `layouts/hub.blade.php`

Adicionar no `<head>`, antes do `@vite` ou do CSS compilado:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300..800&family=Figtree:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
```

Garantir que existe `@stack('styles')` no `<head>` (depois dos assets) para o `@push('styles')` do home.blade.php funcionar.

---

## Alterações a `HomeController.php`

**Opcional.** Como os cards são hand-crafted (cada um tem elementos visuais próprios — bolha, estrelas, mini-grid, bandeiras), o `$sites` collection do controller não é mais usado na view. Podes:

- **Opção A:** deixar o controller como está — o `$sites` simplesmente não é consumido pela nova view. Sem custo.
- **Opção B (limpo):** simplificar o método `index()` para:

```php
public function index()
{
    return view('hub.home');
}
```

Vai por B se quiseres código limpo.

---

## Tasks para o Claude Code

1. Ler `resources/views/hub/home.blade.php` e `resources/views/layouts/hub.blade.php` actuais. Mostrar diff resumido do que vai mudar.
2. Fazer backup do `home.blade.php` actual para `home.blade.php.bak`.
3. Substituir `resources/views/hub/home.blade.php` integralmente pelo código completo acima.
4. Adicionar os `<link>` do Google Fonts em `resources/views/layouts/hub.blade.php` dentro do `<head>`.
5. Confirmar que `layouts/hub.blade.php` tem `@stack('styles')` no `<head>`. Se não tiver, adicionar.
6. Simplificar `HomeController::index()` para `return view('hub.home');` (Opção B).
7. Correr `php artisan view:clear` e `php artisan route:clear`.
8. Correr `curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000` — deve devolver `200`.
9. Testar visualmente em 3 breakpoints:
   - Mobile 375px — grid 2-col, todos os cards 1:1
   - Tablet 900px — grid 4-col
   - Desktop 1440px — bento 12-col com spans variáveis
10. Confirmar que cada card linka para a route correcta (`/desculpometro`, `/botao`, etc.).
11. Verificar que as fonts Bricolage Grotesque e Figtree carregam (DevTools → Network → filter "fonts").

Confirma cada passo com ✅ antes de avançar. Se algum `route('slug.index')` falhar, mostra o erro e pergunta antes de remendar.

---

## Notas

- **Dark mode:** este redesign usa fundo cremoso fixo. A identidade visual assenta na cor dos cards — não faz sentido inverter. Se o utilizador tiver `prefers-color-scheme: dark`, o browser respeita; o design mantém-se igual porque a paleta já tem contraste suficiente.
- **Grain texture:** é SVG inline em data-URL, zero HTTP requests. Se quiseres remover, apaga o bloco `body::before`.
- **Fonts fallback:** se o Bricolage falhar a carregar, a cadeia `'Figtree', system-ui, sans-serif` assume. Não quebra layout.
- **Performance:** todo o CSS é `<20kb` minificado. Sem JS. Bestas.
- **Acessibilidade:** contraste AAA nos cards escuros e AA nos amarelos/lima (por isso usam `color: var(--ink)`). Focus ring mantém-se default do browser — podes adicionar `:focus-visible` customizado se quiseres.
