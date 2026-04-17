# uidesign-v2.md — Fix do Bento Homepage

Lê `CLAUDE.md` e `PROMPTS/uidesign.md` (versão 1, que ficou partida).

**Contexto:** A v1 deste redesign produziu um layout partido (hero e featured estreitos demais com texto cortado, ratios inconsistentes, Corporativo órfão no fundo). Este ficheiro substitui o `home.blade.php` por uma versão corrigida com grid determinístico.

**Diagnóstico do que partiu na v1:**
- `grid-auto-flow: dense` estava a fazer packing imprevisível
- `grid-row: span 2` no hero e featured em simultâneo com `aspect-ratio: 1/1` nos outros cards criava conflitos de altura
- A soma das colunas por linha não era explícita, deixando o browser a decidir
- `h1` do hero sem `white-space: nowrap` permitia quebra a meio da palavra

**Estratégia da v2:**
- Remover `grid-auto-flow: dense`
- Remover `grid-row: span 2` — hero e featured usam apenas `grid-column: span X` com `min-height` próprio
- Definir spans explícitos (`sz-3`, `sz-4`, `sz-5`, `sz-banner`) que somam exactamente 12 cols por linha no desktop
- `white-space: nowrap` no `h1` do hero e font-size max reduzido para garantir que "Clinky.cc" cabe sempre
- Corporativo passa a banner full-width no final (deixa de estar órfão, fica como rodapé visual)

---

## Layout final (desktop, 12 cols)

```
Linha 1:  [Hero (span 7)                      ] [Featured (span 5)]
Linha 2:  [Botão 3] [Nomeador 3] [Horóscopo 3] [Nome 3]
Linha 3:  [Bingo 4       ] [Conversor 5      ] [Quiz 3]
Linha 4:  [Corporativo — banner span 12                          ]
```

Cada linha soma exactamente 12 colunas. Zero packing ambíguo.

## Layout tablet (720-1079px, 4 cols)

```
Hero (span 4, full)
Featured (span 4, full)
[Botão 2][Nomeador 2]
[Horóscopo 2][Nome 2]
[Bingo 2][Conversor 2]
[Quiz 2][... ]
Corporativo (span 4, banner)
```

## Layout mobile (<720px, 2 cols)

```
Hero (span 2, full width, min-height 420px)
Featured (span 2, full width, min-height 420px)
[Botão 1][Nomeador 1]
[Horóscopo 1][Nome 1]
[Bingo 1][Conversor 1]
[Quiz 1][... ]
Corporativo (span 2, full width)
```

Todos os cards minisite são 1:1 no mobile, como pedido.

---

## Tasks

### 1. Backup da v1

```bash
cp resources/views/hub/home.blade.php resources/views/hub/home.blade.php.v1.bak
```

### 2. Substituir `resources/views/hub/home.blade.php`

Substitui o conteúdo integral do ficheiro por:

```blade
@extends('layouts.hub')

@section('content')
<div class="clinky-wrap">

    <div class="clinky-nav">
        <div class="brand">
            <span class="dot"></span>
            <span>Clinky.cc · v1</span>
        </div>
        <div class="count">09 mini-sites · +11 a caminho</div>
    </div>

    <div class="bento">

        {{-- HERO (span 7 desktop) --}}
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

        {{-- FEATURED — Desculpómetro (span 5 desktop) --}}
        <a href="{{ route('desculpometro.index') }}" class="featured-card">
            <span class="tag">TOP</span>
            <div class="big-emoji">😅</div>
            <div>
                <h2>Desculpómetro</h2>
                <p class="desc">Gera a desculpa perfeita em 1 segundo. Com IA, criatividade e zero responsabilidade.</p>
                <span class="cta">Experimentar <span>→</span></span>
            </div>
        </a>

        {{-- ROW 2 — 4 quadrados span 3 cada --}}
        <a href="{{ route('botao.index') }}" class="card card-botao sz-3">
            <span class="tag ghost">EM ALTA</span>
            <div class="icon">🔴</div>
            <div class="giant-button"></div>
            <div class="body">
                <h3>Aperta o Botão</h3>
                <p>1M+ apertaram.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('nomeador.index') }}" class="card card-nomeador sz-3">
            <div class="icon">💬</div>
            <div class="ghost-emoji">💬</div>
            <span class="label">WhatsApp · 03</span>
            <div class="body">
                <h3>Nomeador de Grupos</h3>
                <p>Chega de "Família 🏠".</p>
            </div>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('horoscopo.index') }}" class="card card-horoscopo sz-3">
            <div class="stars">
                <span>✦</span><span>✧</span><span>✦</span><span>⋆</span><span>✦</span>
            </div>
            <div class="icon">🔮</div>
            <span class="label">Pseudo · 04</span>
            <div class="body">
                <h3>Horóscopo Inútil</h3>
                <p>100% inventado.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('nome.index') }}" class="card card-nome sz-3">
            <div class="icon">🧬</div>
            <div class="ghost-emoji">🧬</div>
            <span class="label">IA · 05</span>
            <div class="body">
                <h3>Analisador de Nome</h3>
                <p>73% de "ciência".</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- ROW 3 — 4 + 5 + 3 = 12 --}}
        <a href="{{ route('bingo.index') }}" class="card card-bingo on-light sz-4">
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

        <a href="{{ route('conversor.index') }}" class="card card-conversor sz-5">
            <span class="tag ghost">PT / BR</span>
            <div class="icon">🔁</div>
            <div class="ghost-emoji">🇵🇹</div>
            <span class="label">Língua · 07</span>
            <div class="body">
                <div class="flag-pt-br">🇵🇹 <span class="arrow-icon">↔</span> 🇧🇷</div>
                <h3>Conversor PT ↔ BR</h3>
                <p>Bicha ou fila? Autocarro ou ônibus? O guia definitivo das palavras que nos separam.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('quiz.index') }}" class="card card-quiz on-light sz-3">
            <span class="tag">PT / BR</span>
            <div class="icon">🤔</div>
            <span class="label">Quiz · 08</span>
            <div class="body">
                <h3>Sou mais BR ou PT?</h3>
                <p>5 perguntas.</p>
            </div>
            <div class="arrow">→</div>
        </a>

        {{-- ROW 4 — Banner full-width --}}
        <a href="{{ route('corporativo.index') }}" class="card card-corporativo sz-banner">
            <div class="ghost-emoji">💼</div>
            <div class="icon">💼</div>
            <div class="body">
                <span class="label">Escritório · 09</span>
                <h3>Tradutor Corporativo</h3>
                <p>"Vamos alinhar" = reunião que devia ser email. Traduz o jargão do escritório para português real.</p>
            </div>
            <div class="arrow">→</div>
        </a>

    </div>

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
/* ═══════════════════════ CLINKY BENTO v2 ═══════════════════════ */
:root {
    --bg: #F0EDE3;
    --ink: #0A0A0A;
    --ink-soft: #3A3A3A;
    --ink-mute: #6B6B6B;
    --c-hero: #0A0A0A;
    --c-hero-pop: #C6F432;
    --c-desculpa: #FF5722;
    --c-botao: #E63946;
    --c-nomeador: #FF3E8A;
    --c-horoscopo: #7C3AED;
    --c-nome: #14B8A6;
    --c-bingo: #FBBF24;
    --c-conversor: #2563EB;
    --c-quiz: #84CC16;
    --c-corporativo: #0F172A;
    --radius: 28px;
    --radius-inner: 14px;
    --gap: 14px;
}

body {
    background: var(--bg);
    color: var(--ink);
    font-family: 'Figtree', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
}

body::before {
    content: '';
    position: fixed; inset: 0;
    pointer-events: none; z-index: 1;
    opacity: 0.3; mix-blend-mode: multiply;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' /%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.18'/%3E%3C/svg%3E");
}

.clinky-wrap {
    position: relative; z-index: 2;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 16px 80px;
}
@media (min-width: 900px) { .clinky-wrap { padding: 32px 32px 120px; } }

/* NAV */
.clinky-nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 6px 24px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--ink-soft);
}
.clinky-nav .brand { display: flex; align-items: center; gap: 8px; font-weight: 600; }
.clinky-nav .brand .dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--c-botao);
    animation: pulse 2s ease-in-out infinite;
}
.clinky-nav .count { color: var(--ink-mute); }
@keyframes pulse { 0%,100% { opacity:1; transform:scale(1);} 50% { opacity:0.6; transform:scale(1.3);} }

/* GRID — sem auto-flow dense, sem row-spans */
.bento {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--gap);
}
@media (min-width: 720px)  { .bento { grid-template-columns: repeat(4, 1fr); gap: 16px; } }
@media (min-width: 1080px) { .bento { grid-template-columns: repeat(12, 1fr); gap: 18px; } }

/* CARD BASE */
.card {
    position: relative;
    grid-column: span 1;
    aspect-ratio: 1 / 1;
    padding: 20px;
    border-radius: var(--radius);
    overflow: hidden;
    display: flex; flex-direction: column; justify-content: space-between;
    text-decoration: none; color: #fff;
    isolation: isolate;
    transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
}
@media (min-width: 720px)  { .card { padding: 24px; } }
@media (min-width: 1080px) { .card { padding: 28px; } }
.card:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.08);
}
.card > * { position: relative; z-index: 2; }

.card .icon {
    width: 48px; height: 48px; border-radius: var(--radius-inner);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
}
.card.on-light { color: var(--ink); }
.card.on-light .icon { background: rgba(0,0,0,0.08); }

.card .label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.15em;
    opacity: 0.7; margin-top: 10px;
}
.card .body { margin-top: auto; }
.card h3 {
    font-family: 'Bricolage Grotesque', serif;
    font-weight: 700;
    font-size: clamp(1.2rem, 2.4vw, 1.75rem);
    line-height: 0.95; letter-spacing: -0.02em;
    margin-bottom: 6px;
    overflow-wrap: break-word;
}
.card p { font-size: 13px; line-height: 1.4; opacity: 0.85; max-width: 95%; }
@media (min-width: 1080px) { .card p { font-size: 14px; } }

.card .tag {
    position: absolute; top: 18px; right: 18px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px; font-weight: 700;
    padding: 5px 10px; border-radius: 100px;
    background: rgba(255,255,255,0.95); color: var(--ink);
    text-transform: uppercase; letter-spacing: 0.1em; z-index: 3;
}
.card .tag.ghost {
    background: rgba(0,0,0,0.5); color: #fff;
    backdrop-filter: blur(6px);
}
.card .arrow {
    position: absolute; bottom: 18px; right: 18px;
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), background 0.3s;
}
.card.on-light .arrow { background: rgba(0,0,0,0.1); }
.card:hover .arrow { transform: translate(4px, -4px); background: rgba(255,255,255,0.35); }
.card.on-light:hover .arrow { background: rgba(0,0,0,0.18); }

.card .ghost-emoji {
    position: absolute;
    font-size: 170px; opacity: 0.1;
    bottom: -40px; right: -30px;
    pointer-events: none;
    transform: rotate(-12deg);
    z-index: 0; line-height: 1;
}

/* SIZE MODIFIERS */
@media (min-width: 720px) {
    .card { grid-column: span 2; }
    .card.sz-banner { grid-column: span 4; aspect-ratio: 16/5; }
}
@media (min-width: 1080px) {
    .card.sz-3 { grid-column: span 3; aspect-ratio: 1/1; }
    .card.sz-4 { grid-column: span 4; aspect-ratio: 1/1; }
    .card.sz-5 { grid-column: span 5; aspect-ratio: 5/4; }
    .card.sz-banner { grid-column: span 12; aspect-ratio: auto; min-height: 240px; }
}

/* HERO */
.hero-card {
    position: relative;
    grid-column: span 2;
    min-height: 420px;
    padding: 28px;
    border-radius: var(--radius);
    background: var(--c-hero);
    color: #fff;
    overflow: hidden;
    display: flex; flex-direction: column; justify-content: space-between;
    isolation: isolate;
}
@media (min-width: 720px)  { .hero-card { grid-column: span 4; min-height: 400px; padding: 36px; } }
@media (min-width: 1080px) { .hero-card { grid-column: span 7; min-height: 560px; padding: 48px; } }

.hero-card::before {
    content: '';
    position: absolute; inset: 0; z-index: 1;
    background:
        radial-gradient(circle at 15% 85%, rgba(198,244,50,0.18), transparent 50%),
        radial-gradient(circle at 85% 15%, rgba(255,87,34,0.14), transparent 55%);
}
.hero-card > * { position: relative; z-index: 2; }
.hero-card .eyebrow {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.25em;
    color: var(--c-hero-pop);
    display: flex; align-items: center; gap: 10px;
}
.hero-card .eyebrow::before, .hero-card .eyebrow::after {
    content: ''; flex: 1; max-width: 50px; height: 1px;
    background: var(--c-hero-pop); opacity: 0.4;
}
.hero-card h1 {
    font-family: 'Bricolage Grotesque', serif;
    font-weight: 800;
    font-size: clamp(2.8rem, 10vw, 7rem);
    line-height: 0.85; letter-spacing: -0.04em;
    margin: 20px 0 14px;
    font-variation-settings: 'wdth' 95;
    white-space: nowrap;
}
.hero-card h1 .ky { font-style: italic; font-variation-settings: 'wdth' 75; }
.hero-card h1 .dot-accent { color: var(--c-hero-pop); }
.hero-card .tagline {
    font-size: clamp(14px, 1.6vw, 17px);
    color: rgba(255,255,255,0.7);
    max-width: 450px; line-height: 1.5;
}
.hero-card .stats {
    display: flex; gap: 28px; margin-top: auto; padding-top: 24px; flex-wrap: wrap;
}
.hero-card .stat { display: flex; flex-direction: column; }
.hero-card .stat .num {
    font-family: 'Bricolage Grotesque', serif;
    font-size: clamp(22px, 3.2vw, 34px);
    font-weight: 700; line-height: 1;
    color: var(--c-hero-pop);
}
.hero-card .stat .lbl {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px; text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.5); margin-top: 6px;
}

/* FEATURED */
.featured-card {
    position: relative;
    grid-column: span 2;
    min-height: 420px;
    padding: 28px;
    border-radius: var(--radius);
    background: var(--c-desculpa);
    color: #fff;
    overflow: hidden;
    display: flex; flex-direction: column; justify-content: space-between;
    text-decoration: none;
    isolation: isolate;
    transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
}
@media (min-width: 720px)  { .featured-card { grid-column: span 4; min-height: 400px; padding: 32px; } }
@media (min-width: 1080px) { .featured-card { grid-column: span 5; min-height: 560px; padding: 40px; } }

.featured-card:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
}
.featured-card::before {
    content: '';
    position: absolute; inset: 0; z-index: 1;
    background: radial-gradient(circle at 90% 10%, rgba(255,255,255,0.15), transparent 40%);
}
.featured-card > * { position: relative; z-index: 2; }
.featured-card .tag {
    position: absolute; top: 20px; right: 20px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px; font-weight: 700;
    padding: 5px 10px; border-radius: 100px;
    background: rgba(255,255,255,0.95); color: var(--c-desculpa);
    text-transform: uppercase; letter-spacing: 0.1em; z-index: 3;
}
.featured-card .big-emoji {
    font-size: clamp(4.5rem, 11vw, 8rem);
    line-height: 1;
    filter: drop-shadow(0 6px 20px rgba(0,0,0,0.2));
    transform: rotate(-8deg);
    align-self: flex-start;
}
.featured-card h2 {
    font-family: 'Bricolage Grotesque', serif;
    font-weight: 800;
    font-size: clamp(1.9rem, 4.2vw, 3rem);
    line-height: 0.92; letter-spacing: -0.03em;
    margin: 14px 0 10px;
}
.featured-card .desc {
    font-size: clamp(14px, 1.4vw, 16px);
    line-height: 1.45; opacity: 0.92; max-width: 92%;
}
.featured-card .cta {
    display: inline-flex; align-items: center; gap: 10px;
    background: #fff; color: var(--c-desculpa);
    padding: 12px 22px; border-radius: 100px;
    font-weight: 700; font-size: 14px;
    margin-top: 18px; width: fit-content;
    transition: transform 0.3s;
}
.featured-card:hover .cta { transform: scale(1.04); }

/* CARD BACKGROUNDS */
.card-botao       { background: radial-gradient(circle at 50% 55%, #E63946 0%, #B91C2C 85%); overflow: hidden; }
.card-nomeador    { background: var(--c-nomeador); }
.card-horoscopo   { background: var(--c-horoscopo); }
.card-nome        { background: var(--c-nome); }
.card-bingo       { background: var(--c-bingo); }
.card-conversor   { background: var(--c-conversor); }
.card-quiz        { background: var(--c-quiz); }
.card-corporativo { background: var(--c-corporativo); }

/* BOTÃO bolha pulsante */
.card-botao .giant-button {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none; z-index: 1;
}
.card-botao .giant-button::before {
    content: '';
    width: 58%; aspect-ratio: 1; border-radius: 50%;
    background: radial-gradient(circle at 35% 28%, #FF6B6B 0%, #8B0000 92%);
    box-shadow:
        inset 0 -8px 22px rgba(0,0,0,0.45),
        inset 0 4px 8px rgba(255,255,255,0.22),
        0 6px 24px rgba(0,0,0,0.35);
    animation: beat 2s ease-in-out infinite;
}
@keyframes beat { 0%,100% { transform: scale(1);} 50% { transform: scale(1.05);} }

/* HORÓSCOPO estrelas */
.card-horoscopo .stars { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
.card-horoscopo .stars span {
    position: absolute;
    color: rgba(255,255,255,0.5);
    animation: twinkle 3s ease-in-out infinite;
}
.card-horoscopo .stars span:nth-child(1) { top: 18%; left: 22%; font-size: 12px; animation-delay: 0s; }
.card-horoscopo .stars span:nth-child(2) { top: 32%; right: 26%; font-size: 14px; animation-delay: 0.5s; }
.card-horoscopo .stars span:nth-child(3) { top: 58%; left: 18%; font-size: 10px; animation-delay: 1s; }
.card-horoscopo .stars span:nth-child(4) { bottom: 32%; right: 18%; font-size: 12px; animation-delay: 1.5s; }
.card-horoscopo .stars span:nth-child(5) { top: 72%; left: 58%; font-size: 10px; animation-delay: 2s; }
@keyframes twinkle { 0%,100% { opacity: 0.3; transform: scale(1);} 50% { opacity: 1; transform: scale(1.3);} }

/* BINGO mini-grid */
.card-bingo .mini-grid {
    position: absolute; top: 20px; right: 20px;
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 3px; width: 56px; z-index: 2;
}
.card-bingo .mini-grid span {
    aspect-ratio: 1;
    background: rgba(0,0,0,0.15);
    border-radius: 2px;
}
.card-bingo .mini-grid span.on { background: var(--ink); }

/* CONVERSOR bandeiras */
.card-conversor .flag-pt-br {
    font-size: clamp(2.2rem, 4.5vw, 3.5rem);
    line-height: 1; letter-spacing: -0.1em;
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 8px;
}
.card-conversor .flag-pt-br .arrow-icon {
    font-family: 'Bricolage Grotesque', serif;
    font-weight: 800; font-size: 0.55em;
    color: rgba(255,255,255,0.85);
}

/* CORPORATIVO banner (layout horizontal em desktop) */
.card-corporativo.sz-banner {
    flex-direction: row;
    align-items: center;
    gap: 32px;
}
.card-corporativo.sz-banner .body { margin-top: 0; flex: 1; }
.card-corporativo.sz-banner h3 {
    font-size: clamp(1.8rem, 3vw, 2.4rem);
    margin-top: 10px;
}
.card-corporativo.sz-banner p { max-width: 500px; }
.card-corporativo.sz-banner .ghost-emoji {
    font-size: 260px; bottom: -80px; right: 20px; opacity: 0.08;
}
@media (max-width: 720px) {
    .card-corporativo.sz-banner { flex-direction: column; }
}

/* FOOTER */
.clinky-footer {
    margin-top: 80px; padding: 40px 20px 20px;
    display: flex; flex-direction: column; gap: 20px;
    align-items: center; text-align: center;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--ink-mute);
}
@media (min-width: 720px) {
    .clinky-footer { flex-direction: row; justify-content: space-between; text-align: left; }
}
.clinky-footer .logo {
    font-family: 'Bricolage Grotesque', serif;
    font-weight: 800; font-size: 24px;
    color: var(--ink);
    text-transform: none; letter-spacing: -0.02em;
}
.clinky-footer .logo span { color: var(--c-desculpa); }
.clinky-footer nav { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
.clinky-footer a { color: var(--ink-mute); text-decoration: none; transition: color 0.2s; }
.clinky-footer a:hover { color: var(--ink); }

/* ENTRY ANIMATION */
@keyframes rise {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: translateY(0); }
}
.hero-card, .featured-card, .card {
    animation: rise 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) backwards;
}
.hero-card     { animation-delay: 0s; }
.featured-card { animation-delay: 0.08s; }
.bento > :nth-child(3)  { animation-delay: 0.14s; }
.bento > :nth-child(4)  { animation-delay: 0.18s; }
.bento > :nth-child(5)  { animation-delay: 0.22s; }
.bento > :nth-child(6)  { animation-delay: 0.26s; }
.bento > :nth-child(7)  { animation-delay: 0.30s; }
.bento > :nth-child(8)  { animation-delay: 0.34s; }
.bento > :nth-child(9)  { animation-delay: 0.38s; }
.bento > :nth-child(10) { animation-delay: 0.42s; }
.bento > :nth-child(11) { animation-delay: 0.46s; }
</style>
@endpush
```

### 3. Verificar Google Fonts em `layouts/hub.blade.php`

Confirmar que os `<link>` das fonts estão no `<head>`. Se não estiverem (ou se foram removidos por engano), adicionar:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300..800&family=Figtree:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
```

Confirmar também que existe `@stack('styles')` no `<head>`, depois dos `@vite(...)`.

### 4. Limpar cache

```bash
php artisan view:clear
php artisan route:clear
```

### 5. Testar

```bash
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000
```

Deve devolver `200`.

### 6. Verificação visual em 3 breakpoints

Abrir o browser e testar:

**Mobile 375px:**
- Grid de 2 colunas
- Hero e Featured ocupam 2-col (full width) cada, empilhados
- "Clinky.cc" visível e não truncado
- "Desculpómetro" visível no featured
- Os 7 minisites aparecem em pares (2 por linha) como quadrados 1:1
- Corporativo no fim, empilhado vertical

**Tablet 900px:**
- Grid de 4 colunas
- Hero e Featured 4-col cada (full width), empilhados
- Minisites 2-col cada (2 por linha)
- Corporativo banner 4-col (full width)

**Desktop 1440px:**
- Grid de 12 colunas
- Linha 1: Hero (7 cols) + Featured (5 cols) = 12 ✓
- Linha 2: 4 quadrados de 3 cols cada = 12 ✓
- Linha 3: Bingo 4 + Conversor 5 + Quiz 3 = 12 ✓
- Linha 4: Corporativo banner 12 cols = 12 ✓
- Hero com "Clinky.cc" completo, sem `white-space: wrap`
- Featured com emoji 😅 gigante e título "Desculpómetro" completo
- Botão com bolha vermelha a pulsar no centro
- Horóscopo com 5 estrelas a cintilar
- Bingo com mini-cartela 4×4 top-right
- Conversor com 🇵🇹↔🇧🇷 grande
- Corporativo em layout horizontal (row) com emoji 💼 gigante no background

### 7. Diff com a v1

Se algo parecer diferente do esperado, mostrar diff das 3 diferenças principais entre v1 e v2:
- v1 tinha `grid-auto-flow: dense` → v2 não tem
- v1 tinha `grid-row: span 2` no hero e featured → v2 não tem (usa `min-height`)
- v1 não tinha `white-space: nowrap` no `h1` do hero → v2 tem

### 8. ✅ Concluir

Após confirmação visual nos 3 breakpoints, apagar o backup `home.blade.php.v1.bak`.

---

## Notas importantes

- **Nada no controller precisa de mudar.** Se o `HomeController` ainda passa `$sites` para a view, ignora — esta view não usa essa collection (cards são hand-crafted).
- **Sem `grid-row` spans.** Intencional. Hero e Featured usam `min-height` para controlar altura; os minisites usam `aspect-ratio` para altura derivada da largura.
- **Sem `grid-auto-flow: dense`.** Intencional. Cada linha é explícita — zero ambiguidade no packing.
- **Se a fonte Bricolage Grotesque não carregar**, o fallback `serif` entra automaticamente. Layout continua intacto.
- **Dark mode:** mantém-se o fundo cremoso. O contraste está garantido pela paleta dos cards.
- **Acessibilidade:** contraste AAA nos cards escuros, AA+ nos amarelo/lima (via `color: var(--ink)`).

Se algo falhar (route não existir, @stack não estar no layout, etc.) **para e pergunta antes de remendar**.
