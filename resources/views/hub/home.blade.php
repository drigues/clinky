@extends('layouts.hub')

@section('content')
<div class="clinky-wrap max-w-[1400px] px-4">

    <div class="clinky-nav">
        <div class="brand">
            <span class="dot"></span>
            <span>Clinky.cc</span>
        </div>
        <div class="count">{{ $others->count() + ($featured ? 1 : 0) }} clinky-aaabsurd-sites</div>
    </div>

    <div class="bento">

        {{-- HERO --}}
        <div class="hero-card">
            <div class="eyebrow">✦ Testa a tua resistência ✦</div>
            <h1>Clin<span class="ky">ky</span><span class="dot-accent">.cc</span></h1>
            <p class="tagline">Não consegues parar. Nem devias tentar.<br>Scroll. Clica. Repete.</p>
            <div class="stats">
                <div class="stat"><span class="num">{{ str_pad($others->count() + ($featured ? 1 : 0), 2, '0', STR_PAD_LEFT) }}</span><span class="lbl">Mini-sites</span></div>
                <div class="stat"><span class="num">0</span><span class="lbl">Cookies</span></div>
                <div class="stat"><span class="num">∞</span><span class="lbl">Partilháveis</span></div>
            </div>
        </div>

        {{-- FEATURED --}}
        @if($featured)
            <a id="{{ $featured['slug'] }}" href="{{ route($featured['slug'] . '.index') }}" class="featured-card"
               style="background: {{ $featured['bg'] }}; color: {{ $featured['text'] === 'dark' ? '#0A0A0A' : '#fff' }};">
                @if(!empty($featured['tag']))
                    <span class="tag" style="color: {{ $featured['bg'] }};">{{ $featured['tag'] }}</span>
                @endif
                <div class="big-emoji">{{ $featured['emoji'] }}</div>
                <div>
                    <h2>{{ $featured['title'] }}</h2>
                    <p class="desc">{{ $featured['desc'] }}</p>
                    <span class="cta" style="color: {{ $featured['bg'] }};">Experimentar <span>→</span></span>
                </div>
            </a>
        @endif

        {{-- OTHERS — loop genérico --}}
        @foreach($others as $i => $site)
            @php
                $n        = $i + ($featured ? 2 : 1);
                $classes  = 'card sz-' . ($site['size'] ?? 'sm');
                if (($site['text'] ?? 'light') === 'dark') $classes .= ' on-light';
            @endphp

            <a id="{{ $site['slug'] }}" href="{{ route($site['slug'] . '.index') }}"
               class="{{ $classes }}"
               style="background: {{ $site['bg'] }};">

                {{-- TAG --}}
                @if(!empty($site['tag']))
                    <span class="tag {{ $site['tag_style'] ?? '' }}">{{ $site['tag'] }}</span>
                @endif

                {{-- ICON --}}
                <div class="icon">{{ $site['emoji'] }}</div>

                {{-- DECORATION (opcional) --}}
                @switch($site['decoration'] ?? null)
                    @case('pulse-ball')
                        <div class="deco-pulse-ball"></div>
                        @break
                    @case('stars')
                        <div class="deco-stars">
                            <span>✦</span><span>✧</span><span>✦</span><span>⋆</span><span>✦</span>
                        </div>
                        @break
                    @case('mini-grid')
                        <div class="deco-mini-grid">
                            <span class="on"></span><span></span><span class="on"></span><span></span>
                            <span></span><span class="on"></span><span class="on"></span><span></span>
                            <span class="on"></span><span></span><span class="on"></span><span class="on"></span>
                            <span></span><span class="on"></span><span></span><span class="on"></span>
                        </div>
                        @break
                    @case('flags')
                        <div class="deco-flags">🇵🇹 <span class="arrow-icon">↔</span> 🇧🇷</div>
                        @break
                    @case('bubbles')
                        <div class="deco-bubbles">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        @break
                    @case('progress')
                        <div class="deco-progress"></div>
                        @break
                    @case('void')
                        {{-- intencionalmente vazio --}}
                        @break
                @endswitch

                {{-- GHOST EMOJI --}}
                @if(($site['size'] ?? 'sm') !== 'banner' || !empty($site['ghost_emoji']))
                    <div class="ghost-emoji">{{ $site['ghost_emoji'] ?? $site['emoji'] }}</div>
                @endif

                {{-- LABEL --}}
                @if(!empty($site['category']))
                    <span class="label">{{ $site['category'] }} · {{ str_pad($n, 2, '0', STR_PAD_LEFT) }}</span>
                @endif

                {{-- BODY --}}
                <div class="body">
                    <h3>{{ $site['title'] }}</h3>
                    <p>{{ $site['desc'] }}</p>
                </div>

                <div class="arrow">→</div>
            </a>
        @endforeach

    </div>

    <footer class="clinky-footer">
        <div class="logo">Clinky<span>.cc</span></div>
        <nav>
            <a href="{{ route('privacidade') }}">Privacidade</a>
            <span>Feito com 💚</span>
            <span>© 2026</span>
        </nav>
    </footer>

</div>
@endsection

@push('styles')
<style>
/* ═══════════════════════ CLINKY BENTO v3 ═══════════════════════ */
html { scroll-behavior: smooth; }
[id] { scroll-margin-top: 16px; }

:root {
    --bg: #F0EDE3;
    --ink: #0A0A0A;
    --ink-soft: #3A3A3A;
    --ink-mute: #6B6B6B;
    --c-hero: #0A0A0A;
    --c-hero-pop: #C6F432;
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

.clinky-wrap { position: relative; z-index: 2; max-width: 1400px; margin: 0 auto; padding: 20px 16px 80px; }
@media (min-width: 900px) { .clinky-wrap { padding: 32px 32px 120px; } }

.clinky-nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 6px 24px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--ink-soft);
}
.clinky-nav .brand { display: flex; align-items: center; gap: 8px; font-weight: 600; }
.clinky-nav .brand .dot { width: 8px; height: 8px; border-radius: 50%; background: #E63946; animation: pulse 2s ease-in-out infinite; }
.clinky-nav .count { color: var(--ink-mute); }
@keyframes pulse { 0%,100% { opacity:1; transform:scale(1);} 50% { opacity:0.6; transform:scale(1.3);} }

/* GRID */
.bento { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--gap); }
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
.card:hover { transform: translateY(-4px) scale(1.01); box-shadow: 0 12px 24px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.08); }
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
    font-size: clamp(1.1rem, 2.2vw, 1.7rem);
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
.card .tag.ghost { background: rgba(0,0,0,0.5); color: #fff; backdrop-filter: blur(6px); }

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

/* SIZES */
@media (min-width: 720px) {
    .card { grid-column: span 2; }
    .card.sz-banner { grid-column: span 4; aspect-ratio: 16/5; }
}
@media (min-width: 1080px) {
    .card.sz-sm { grid-column: span 3; aspect-ratio: 1/1; }
    .card.sz-md { grid-column: span 4; aspect-ratio: 1/1; }
    .card.sz-lg { grid-column: span 5; aspect-ratio: 5/4; }
    .card.sz-banner { grid-column: span 12; aspect-ratio: auto; min-height: 220px; }
}

/* BANNER horizontal layout */
.card.sz-banner { flex-direction: row; align-items: center; gap: 28px; }
.card.sz-banner .body { margin-top: 0; flex: 1; max-width: 600px; }
.card.sz-banner h3 { font-size: clamp(1.6rem, 2.8vw, 2.2rem); margin-bottom: 8px; }
.card.sz-banner .ghost-emoji { font-size: 240px; bottom: -70px; right: 30px; opacity: 0.08; }
@media (max-width: 720px) {
    .card.sz-banner { flex-direction: column; align-items: flex-start; }
}

/* HERO */
.hero-card {
    position: relative; grid-column: span 2; min-height: 420px; padding: 28px;
    border-radius: var(--radius); background: var(--c-hero); color: #fff;
    overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;
    isolation: isolate;
}
@media (min-width: 720px)  { .hero-card { grid-column: span 4; min-height: 400px; padding: 36px; } }
@media (min-width: 1080px) { .hero-card { grid-column: span 7; min-height: 560px; padding: 48px; } }
.hero-card::before {
    content: ''; position: absolute; inset: 0; z-index: 1;
    background:
        radial-gradient(circle at 15% 85%, rgba(198,244,50,0.18), transparent 50%),
        radial-gradient(circle at 85% 15%, rgba(255,87,34,0.14), transparent 55%);
}
.hero-card > * { position: relative; z-index: 2; }
.hero-card .eyebrow {
    font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.25em;
    color: var(--c-hero-pop);
    display: flex; align-items: center; gap: 10px;
}
.hero-card .eyebrow::before, .hero-card .eyebrow::after {
    content: ''; flex: 1; max-width: 50px; height: 1px;
    background: var(--c-hero-pop); opacity: 0.4;
}
.hero-card h1 {
    font-family: 'Bricolage Grotesque', serif; font-weight: 800;
    font-size: clamp(2.8rem, 10vw, 7rem); line-height: 0.85; letter-spacing: -0.04em;
    margin: 20px 0 14px;
    font-variation-settings: 'wdth' 95;
    white-space: nowrap;
}
.hero-card h1 .ky { font-style: italic; font-variation-settings: 'wdth' 75; }
.hero-card h1 .dot-accent { color: var(--c-hero-pop); }
.hero-card .tagline { font-size: clamp(14px, 1.6vw, 17px); color: rgba(255,255,255,0.7); max-width: 450px; line-height: 1.5; }
.hero-card .stats { display: flex; gap: 28px; margin-top: auto; padding-top: 24px; flex-wrap: wrap; }
.hero-card .stat { display: flex; flex-direction: column; }
.hero-card .stat .num { font-family: 'Bricolage Grotesque', serif; font-size: clamp(22px, 3.2vw, 34px); font-weight: 700; line-height: 1; color: var(--c-hero-pop); }
.hero-card .stat .lbl { font-family: 'JetBrains Mono', monospace; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.5); margin-top: 6px; }

/* FEATURED */
.featured-card {
    position: relative; grid-column: span 2; min-height: 420px; padding: 28px;
    border-radius: var(--radius); overflow: hidden;
    display: flex; flex-direction: column; justify-content: space-between;
    text-decoration: none; isolation: isolate;
    transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
}
@media (min-width: 720px)  { .featured-card { grid-column: span 4; min-height: 400px; padding: 32px; } }
@media (min-width: 1080px) { .featured-card { grid-column: span 5; min-height: 560px; padding: 40px; } }
.featured-card:hover { transform: translateY(-4px) scale(1.01); box-shadow: 0 12px 24px rgba(0,0,0,0.12); }
.featured-card::before {
    content: ''; position: absolute; inset: 0; z-index: 1;
    background: radial-gradient(circle at 90% 10%, rgba(255,255,255,0.15), transparent 40%);
}
.featured-card > * { position: relative; z-index: 2; }
.featured-card .tag {
    position: absolute; top: 20px; right: 20px;
    font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 700;
    padding: 5px 10px; border-radius: 100px;
    background: #fff;
    text-transform: uppercase; letter-spacing: 0.1em; z-index: 3;
}
.featured-card .big-emoji {
    font-size: clamp(4.5rem, 11vw, 8rem); line-height: 1;
    filter: drop-shadow(0 6px 20px rgba(0,0,0,0.2));
    transform: rotate(-8deg);
    align-self: flex-start;
}
.featured-card h2 {
    font-family: 'Bricolage Grotesque', serif; font-weight: 800;
    font-size: clamp(1.9rem, 4.2vw, 3rem); line-height: 0.92; letter-spacing: -0.03em;
    margin: 14px 0 10px;
}
.featured-card .desc { font-size: clamp(14px, 1.4vw, 16px); line-height: 1.45; opacity: 0.92; max-width: 92%; }
.featured-card .cta {
    display: inline-flex; align-items: center; gap: 10px;
    background: #fff; padding: 12px 22px; border-radius: 100px;
    font-weight: 700; font-size: 14px; margin-top: 18px; width: fit-content;
    transition: transform 0.3s;
}
.featured-card:hover .cta { transform: scale(1.04); }

/* ═══════════════ DECORATIONS ═══════════════ */

/* pulse-ball (botao) */
.deco-pulse-ball { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 1; }
.deco-pulse-ball::before {
    content: ''; width: 58%; aspect-ratio: 1; border-radius: 50%;
    background: radial-gradient(circle at 35% 28%, #FF6B6B 0%, #8B0000 92%);
    box-shadow: inset 0 -8px 22px rgba(0,0,0,0.45), inset 0 4px 8px rgba(255,255,255,0.22), 0 6px 24px rgba(0,0,0,0.35);
    animation: beat 2s ease-in-out infinite;
}
@keyframes beat { 0%,100% { transform: scale(1);} 50% { transform: scale(1.05);} }

/* stars (horoscopo) */
.deco-stars { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
.deco-stars span { position: absolute; color: rgba(255,255,255,0.5); animation: twinkle 3s ease-in-out infinite; }
.deco-stars span:nth-child(1) { top: 18%; left: 22%; font-size: 12px; animation-delay: 0s; }
.deco-stars span:nth-child(2) { top: 32%; right: 26%; font-size: 14px; animation-delay: 0.5s; }
.deco-stars span:nth-child(3) { top: 58%; left: 18%; font-size: 10px; animation-delay: 1s; }
.deco-stars span:nth-child(4) { bottom: 32%; right: 18%; font-size: 12px; animation-delay: 1.5s; }
.deco-stars span:nth-child(5) { top: 72%; left: 58%; font-size: 10px; animation-delay: 2s; }
@keyframes twinkle { 0%,100% { opacity: 0.3; transform: scale(1);} 50% { opacity: 1; transform: scale(1.3);} }

/* mini-grid (bingo) */
.deco-mini-grid { position: absolute; top: 20px; right: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; width: 56px; z-index: 2; }
.deco-mini-grid span { aspect-ratio: 1; background: rgba(0,0,0,0.15); border-radius: 2px; }
.deco-mini-grid span.on { background: var(--ink); }

/* flags (conversor) */
.deco-flags { font-size: clamp(2.2rem, 4.5vw, 3.5rem); line-height: 1; letter-spacing: -0.1em; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.deco-flags .arrow-icon { font-family: 'Bricolage Grotesque', serif; font-weight: 800; font-size: 0.55em; color: rgba(255,255,255,0.85); }

/* bubbles (bolhas) */
.deco-bubbles { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
.deco-bubbles span { position: absolute; border-radius: 50%; background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.4), rgba(255,255,255,0.08)); animation: float 4s ease-in-out infinite; }
.deco-bubbles span:nth-child(1) { width: 40px; height: 40px; top: 20%; left: 15%; animation-delay: 0s; }
.deco-bubbles span:nth-child(2) { width: 24px; height: 24px; top: 45%; right: 20%; animation-delay: 1s; }
.deco-bubbles span:nth-child(3) { width: 30px; height: 30px; bottom: 30%; left: 30%; animation-delay: 2s; }
.deco-bubbles span:nth-child(4) { width: 18px; height: 18px; top: 30%; right: 35%; animation-delay: 0.5s; }
@keyframes float { 0%,100% { transform: translateY(0); opacity: 0.7; } 50% { transform: translateY(-8px); opacity: 1; } }

/* progress (progresso) */
.deco-progress { position: absolute; bottom: 0; left: 0; right: 0; height: 6px; background: rgba(0,0,0,0.25); z-index: 2; }
.deco-progress::after { content: ''; position: absolute; top: 0; left: 0; height: 100%; width: 67%; background: linear-gradient(90deg, #C6F432, #FF5722); animation: grow 3s ease-out forwards; }
@keyframes grow { from { width: 0; } to { width: 67%; } }

/* FOOTER */
.clinky-footer {
    margin-top: 80px; padding: 40px 20px 20px;
    display: flex; flex-direction: column; gap: 20px;
    align-items: center; text-align: center;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--ink-mute);
}
@media (min-width: 720px) { .clinky-footer { flex-direction: row; justify-content: space-between; text-align: left; } }
.clinky-footer .logo { font-family: 'Bricolage Grotesque', serif; font-weight: 800; font-size: 24px; color: var(--ink); text-transform: none; letter-spacing: -0.02em; }
.clinky-footer .logo span { color: #FF5722; }
.clinky-footer nav { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
.clinky-footer a { color: var(--ink-mute); text-decoration: none; transition: color 0.2s; }
.clinky-footer a:hover { color: var(--ink); }

/* ENTRY ANIMATION */
@keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
.hero-card, .featured-card, .card { animation: rise 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) backwards; }
.bento > *:nth-child(1)  { animation-delay: 0s; }
.bento > *:nth-child(2)  { animation-delay: 0.06s; }
.bento > *:nth-child(3)  { animation-delay: 0.10s; }
.bento > *:nth-child(4)  { animation-delay: 0.14s; }
.bento > *:nth-child(5)  { animation-delay: 0.18s; }
.bento > *:nth-child(6)  { animation-delay: 0.22s; }
.bento > *:nth-child(7)  { animation-delay: 0.26s; }
.bento > *:nth-child(8)  { animation-delay: 0.30s; }
.bento > *:nth-child(9)  { animation-delay: 0.34s; }
.bento > *:nth-child(10) { animation-delay: 0.38s; }
.bento > *:nth-child(n+11) { animation-delay: 0.42s; }
</style>
@endpush
