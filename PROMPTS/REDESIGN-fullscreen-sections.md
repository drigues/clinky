# REDESIGN-fullscreen-sections.md

Lê CLAUDE.md e REFERENCES/COMPONENTS.md.

Redesenha a homepage `resources/views/hub/home.blade.php` com secções full-screen por mini-site, estilo landing page com scroll, inspirado no Linktree marketing site — cada mini-site é uma secção `min-h-screen` com fundo colorido, imagem de fundo, tipografia bold e animação de entrada ao fazer scroll.

---

## Layout geral da página

```
[HERO] — Clinky.cc — título gigante, fundo preto, acento lime
[SECÇÃO 1] — Desculpómetro — fundo laranja escuro
[SECÇÃO 2] — Aperta o Botão — fundo preto, imagem do botão
[SECÇÃO 3] — Nomeador de Grupos — fundo rosa escuro
[SECÇÃO 4] — Horóscopo Inútil — fundo roxo escuro, imagem da bola de cristal
[SECÇÃO 5] — Analisador de Nome — fundo teal escuro
[SECÇÃO 6] — Bingo do Imigrante — fundo amarelo escuro
[SECÇÃO 7] — Conversor PT/BR — fundo azul escuro, split verde/azul
[SECÇÃO 8] — Sou mais BR ou PT? — fundo verde/amarelo
[SECÇÃO 9] — Tradutor Corporativo — fundo vermelho escuro
[FOOTER] — fundo preto
```

---

## Estrutura de cada secção

Cada secção `<section>` segue este padrão HTML:

```html
<section class="relative min-h-screen flex items-center overflow-hidden"
         style="background-color: {cor-de-fundo}">

    {{-- Imagem de fundo (se existir) --}}
    <div class="absolute inset-0 z-0">
        <img src="/images/og/{slug}.png"
             alt=""
             class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
    </div>

    {{-- Gradiente de sobreposição --}}
    <div class="absolute inset-0 z-0"
         style="background: linear-gradient(135deg, {cor}ee 0%, {cor}88 50%, transparent 100%)">
    </div>

    {{-- Conteúdo --}}
    <div class="relative z-10 max-w-2xl mx-auto px-6 py-24 md:px-12">

        {{-- Número da secção --}}
        <p class="text-xs font-bold uppercase tracking-[0.3em] text-white/40 mb-4 reveal">
            0{n} — {categoria}
        </p>

        {{-- Emoji --}}
        <div class="text-8xl mb-6 reveal reveal-delay-1">{emoji}</div>

        {{-- Título --}}
        <h2 class="text-5xl md:text-7xl font-black text-white leading-[0.9] tracking-tight mb-4 reveal reveal-delay-2">
            {título}
        </h2>

        {{-- Descrição --}}
        <p class="text-xl text-white/70 max-w-md leading-relaxed mb-10 reveal reveal-delay-3">
            {descrição longa e apelativa}
        </p>

        {{-- CTA --}}
        <a href="/{slug}"
           class="inline-flex items-center gap-3 bg-white text-black font-bold text-lg px-8 py-4 rounded-full hover:scale-105 active:scale-95 transition-transform reveal reveal-delay-4">
            Experimentar agora
            <span class="text-2xl">→</span>
        </a>

        {{-- Badge --}}
        @if($site['tag'])
        <span class="inline-block ml-4 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full reveal reveal-delay-4"
              style="background: {cor-badge}; color: {cor-texto-badge}">
            {tag}
        </span>
        @endif

    </div>

    {{-- Decoração lateral (número gigante) --}}
    <div class="absolute right-0 top-1/2 -translate-y-1/2 text-[20rem] font-black text-white/5 select-none leading-none pr-4 hidden md:block">
        0{n}
    </div>

</section>
```

---

## Cores por secção

```php
$sites = [
    [
        'slug'        => 'desculpometro',
        'emoji'       => '😅',
        'title'       => 'Desculpómetro',
        'desc'        => 'Gera a desculpa perfeita em 1 segundo. Com inteligência artificial, criatividade e zero responsabilidade.',
        'bg'          => '#1a0800',
        'accent'      => '#ff6b00',
        'tag'         => 'TOP',
        'tag_bg'      => '#ff6b00',
        'tag_text'    => '#fff',
        'has_image'   => true,
        'n'           => 1,
        'cat'         => 'IA Generativa',
    ],
    [
        'slug'        => 'botao',
        'emoji'       => '🔴',
        'title'       => 'Aperta o Botão',
        'desc'        => 'Um botão. Sem propósito. Sem explicação. Já apertaram mais de um milhão de vezes.',
        'bg'          => '#0a0a0a',
        'accent'      => '#c8f135',
        'tag'         => 'EM ALTA',
        'tag_bg'      => '#c8f135',
        'tag_text'    => '#000',
        'has_image'   => true,
        'n'           => 2,
        'cat'         => 'Experiência',
    ],
    [
        'slug'        => 'nomeador',
        'emoji'       => '💬',
        'title'       => 'Nomeador de Grupos',
        'desc'        => 'Chega de grupos chamados "Família 🏠". Gera o nome épico que o teu grupo merece.',
        'bg'          => '#1a0010',
        'accent'      => '#ff2d78',
        'tag'         => null,
        'has_image'   => false,
        'n'           => 3,
        'cat'         => 'WhatsApp',
    ],
    [
        'slug'        => 'horoscopo',
        'emoji'       => '🔮',
        'title'       => 'Horóscopo Inútil',
        'desc'        => 'Previsões 100% inventadas, 100% precisas. As estrelas não sabem mais do que isto.',
        'bg'          => '#0d0020',
        'accent'      => '#a855f7',
        'tag'         => null,
        'has_image'   => true,
        'n'           => 4,
        'cat'         => 'Pseudo-ciência',
    ],
    [
        'slug'        => 'nome',
        'emoji'       => '🧬',
        'title'       => 'Analisador de Nome',
        'desc'        => 'Descobre o que o teu nome diz sobre ti. Resultados com 73% de confiança científica*.',
        'bg'          => '#001a18',
        'accent'      => '#00d9c0',
        'tag'         => null,
        'has_image'   => false,
        'n'           => 5,
        'cat'         => 'IA Generativa',
    ],
    [
        'slug'        => 'bingo',
        'emoji'       => '🎯',
        'title'       => 'Bingo do Imigrante',
        'desc'        => 'Quantas destas situações já te aconteceram em Portugal? A cartela que toda a comunidade BR vai reconhecer.',
        'bg'          => '#1a1500',
        'accent'      => '#ffd600',
        'tag'         => 'PT/BR',
        'tag_bg'      => '#a855f7',
        'tag_text'    => '#fff',
        'has_image'   => false,
        'n'           => 6,
        'cat'         => 'Comunidade BR/PT',
    ],
    [
        'slug'        => 'conversor',
        'emoji'       => '🔁',
        'title'       => 'Conversor PT ↔ BR',
        'desc'        => 'Bicha ou fila? Autocarro ou ônibus? O guia definitivo das palavras que nos separam.',
        'bg'          => '#001020',
        'accent'      => '#00aaff',
        'tag'         => 'PT/BR',
        'tag_bg'      => '#a855f7',
        'tag_text'    => '#fff',
        'has_image'   => false,
        'n'           => 7,
        'cat'         => 'Língua',
    ],
    [
        'slug'        => 'quiz',
        'emoji'       => '🤔',
        'title'       => 'Sou mais BR ou PT?',
        'desc'        => 'Depois de anos entre os dois países, quanto do outro já absorbeste? 5 perguntas. Resultado imprevisto.',
        'bg'          => '#0a1500',
        'accent'      => '#7bc900',
        'tag'         => 'PT/BR',
        'tag_bg'      => '#a855f7',
        'tag_text'    => '#fff',
        'has_image'   => false,
        'n'           => 8,
        'cat'         => 'Quiz',
    ],
    [
        'slug'        => 'corporativo',
        'emoji'       => '💼',
        'title'       => 'Tradutor Corporativo',
        'desc'        => '"Vamos alinhar" = reunião que devia ser email. Traduz o jargão do escritório para português real.',
        'bg'          => '#1a0000',
        'accent'      => '#ff3b3b',
        'tag'         => null,
        'has_image'   => false,
        'n'           => 9,
        'cat'         => 'Escritório',
    ],
];
```

---

## Hero section (topo)

```html
<section class="relative min-h-screen flex flex-col items-center justify-center bg-[#0a0a0a] overflow-hidden">

    {{-- Fundo animado: grid de pontos --}}
    <div class="absolute inset-0 opacity-10"
         style="background-image: radial-gradient(circle, #c8f135 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    {{-- Gradiente radial central --}}
    <div class="absolute inset-0"
         style="background: radial-gradient(ellipse at center, #c8f13515 0%, transparent 70%)">
    </div>

    <div class="relative z-10 text-center px-6">
        <p class="text-xs font-bold uppercase tracking-[0.4em] text-[#c8f135]/60 mb-8 reveal">
            ✦ Sites bestas que valem a pena ✦
        </p>
        <h1 class="text-[clamp(4rem,15vw,10rem)] font-black text-white leading-[0.85] tracking-tight mb-6 reveal reveal-delay-1">
            Clin<span style="color: #c8f135">ky</span>.cc
        </h1>
        <p class="text-lg text-white/50 max-w-sm mx-auto leading-relaxed mb-12 reveal reveal-delay-2">
            Mini-sites virais, inúteis e partilháveis.<br>
            Sem registo. Sem dados. Sem sentido.
        </p>
        <div class="flex items-center justify-center gap-2 text-white/30 text-sm reveal reveal-delay-3">
            <span class="w-1.5 h-1.5 rounded-full bg-[#c8f135] animate-pulse"></span>
            Scroll para explorar
        </div>
    </div>

    {{-- Seta scroll --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/20 animate-bounce">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12l7 7 7-7"/>
        </svg>
    </div>

</section>
```

---

## Animações de scroll (IntersectionObserver — vanilla JS)

Adicionar no final do ficheiro, antes do `@endsection` dos scripts:

```html
<style>
.reveal {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}
.reveal-delay-1 { transition-delay: 0.1s; }
.reveal-delay-2 { transition-delay: 0.2s; }
.reveal-delay-3 { transition-delay: 0.35s; }
.reveal-delay-4 { transition-delay: 0.5s; }
</style>

<script>
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
        }
    });
}, { threshold: 0.15 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
```

---

## Footer

```html
<footer class="bg-[#0a0a0a] border-t border-white/10 py-12 text-center">
    <p class="text-3xl font-black text-white mb-2">Clinky<span style="color:#c8f135">.cc</span></p>
    <p class="text-white/30 text-sm mb-6">Mini-sites virais, inúteis e partilháveis.</p>
    <div class="flex justify-center gap-6 text-white/30 text-xs">
        <a href="/privacidade" class="hover:text-white/60 transition-colors">Privacidade</a>
        <span>·</span>
        <span>© 2026 Clinky.cc</span>
        <span>·</span>
        <a href="https://thr33.xyz" class="hover:text-white/60 transition-colors">thr33.xyz</a>
    </div>
</footer>
```

---

## Tasks para Claude Code

1. Substitui o conteúdo completo de `resources/views/hub/home.blade.php` com o novo layout
2. Mantém o `@extends('layouts.hub')` e `@section('content')` correctos
3. Implementa o hero, as 9 secções (loop ou manual) e o footer
4. Adiciona o CSS das animações `.reveal` no `@push('styles')` ou inline
5. Adiciona o JS do IntersectionObserver no `@push('scripts')`
6. Confirma que todos os `href` apontam para `/{slug}` (routing por directoria)
7. Corre `php artisan view:clear`
8. Confirma que `http://127.0.0.1:8000` carrega sem erro com `curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000`

Confirma cada task com ✅ antes de avançar.
