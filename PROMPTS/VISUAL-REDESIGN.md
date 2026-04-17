# VISUAL-REDESIGN.md — Impacto Visual + Back-link com Anchor

Lê `CLAUDE.md`, `REFERENCES/COMPONENTS.md` e `REDESIGN-fullscreen-sections.md` antes de começar.

Este prompt resolve 5 problemas visíveis nos screenshots:
1. Back-link `← clinky.cc` não volta ao ponto de partida da homepage
2. Tipografia fina e pequena — os headings não têm peso nem escala
3. Layout centrado em corredor estreito — desperdiça o desktop
4. Contraste fraco (zinc-500/600 em fundos escuros) — difícil de ler
5. Cores de acento quase invisíveis — nenhum site tem personalidade visual forte

Ordem de execução: PARTE A → B → C → D → E. Confirma cada parte antes de avançar.

---

## PARTE A — Back-link com anchor para homepage

Objectivo: ao clicar `← clinky.cc` num mini-site, voltar a `clinky.cc#{slug}` e fazer scroll à secção correspondente.

### A.1 — Adicionar `id` a cada secção do hub

Em `resources/views/hub/home.blade.php`, cada `<section>` de mini-site deve ter o id do slug:

```blade
@foreach($sites as $site)
<section id="{{ $site['slug'] }}"
         class="relative min-h-screen flex items-center overflow-hidden scroll-mt-0"
         style="background-color: {{ $site['bg'] }}">
    {{-- ... conteúdo existente ... --}}
</section>
@endforeach
```

Garante também, no topo do ficheiro Blade (no `<head>` do layout ou dentro de `@push('styles')`):

```css
html { scroll-behavior: smooth; }
section[id] { scroll-margin-top: 0; }
```

### A.2 — Passar o slug ao layout do mini-site

Em `resources/views/layouts/minisite.blade.php`, no topo do `<body>`, substitui o back-link actual por:

```blade
@php
    $slug = $slug ?? request()->segment(1) ?? '';
    $backUrl = $slug ? '/#' . $slug : '/';
@endphp

<a href="{{ $backUrl }}"
   class="fixed top-4 left-4 md:top-6 md:left-6 z-50
          inline-flex items-center gap-2
          px-3 py-2 rounded-full
          bg-black/40 backdrop-blur-sm border border-white/10
          text-white/70 hover:text-white hover:bg-black/60
          text-sm font-semibold transition-all">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    clinky.cc
</a>
```

Nota: o `request()->segment(1)` devolve automaticamente o slug porque as rotas usam `/desculpometro`, `/botao`, etc. Se algum controller passa `$slug` explicitamente ao view, é respeitado.

### A.3 — Confirmar que funciona

```bash
# Abrir homepage, fazer scroll até à secção do Bingo, clicar "Experimentar"
# Chegado ao mini-site, clicar "← clinky.cc"
# Deve voltar a clinky.cc#bingo com scroll suave
```

---

## PARTE B — Sistema de tipografia forte

### B.1 — Registar a font Inter (900) em `resources/css/app.css`

No topo do ficheiro:

```css
@import url('https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap');

@theme {
    --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
    --font-feature-settings: 'cv11', 'ss01', 'ss03';
}

/* Defaults globais para mini-sites */
@layer base {
    body {
        font-family: var(--font-sans);
        font-feature-settings: var(--font-feature-settings);
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }

    h1, h2 {
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 0.95;
    }

    h3, h4 {
        font-weight: 800;
        letter-spacing: -0.02em;
    }
}
```

### B.2 — Criar componente `<x-hero>` para o header de cada mini-site

`resources/views/components/hero.blade.php`:

```blade
@props([
    'emoji'    => null,
    'title',
    'tagline'  => null,
    'accent'   => '#c8f135',
    'eyebrow'  => null, // texto curto antes do título, ex: "MINI-SITE 06"
])

<header class="relative text-center pt-24 pb-12 md:pt-32 md:pb-20">

    @if($eyebrow)
        <p class="text-[11px] md:text-xs font-bold uppercase tracking-[0.3em] mb-5"
           style="color: {{ $accent }}99">
            {{ $eyebrow }}
        </p>
    @endif

    @if($emoji)
        <div class="text-6xl md:text-8xl mb-6 md:mb-8 leading-none select-none">
            {{ $emoji }}
        </div>
    @endif

    <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white
               tracking-tight leading-[0.9]
               max-w-4xl mx-auto px-4">
        {{ $title }}
    </h1>

    @if($tagline)
        <p class="mt-6 md:mt-8 text-lg md:text-2xl text-white/60 font-medium
                  max-w-2xl mx-auto px-6 leading-relaxed">
            {{ $tagline }}
        </p>
    @endif

    <div class="mt-10 md:mt-14 mx-auto h-[3px] w-16 rounded-full"
         style="background: {{ $accent }}"></div>

</header>
```

Depois, em cada view `resources/views/sites/{slug}/index.blade.php`, substituir o header actual por:

```blade
<x-hero
    emoji="🔮"
    title="Horóscopo Inútil"
    tagline="Previsões 100% inventadas, 100% partilháveis. As estrelas não sabem mais do que isto."
    accent="#a855f7"
    eyebrow="PSEUDO-CIÊNCIA · 04" />
```

### B.3 — Regra de contraste

Substitui **todas** as ocorrências destas classes nos views dos mini-sites:

| Substituir | Por |
|---|---|
| `text-zinc-500` | `text-white/60` |
| `text-zinc-600` | `text-white/50` |
| `text-zinc-400` | `text-white/70` |
| `text-gray-500` | `text-white/60` |
| `text-gray-400` | `text-white/70` |

Fazer com sed:

```bash
find resources/views/sites -name '*.blade.php' -exec sed -i \
    -e 's/text-zinc-500/text-white\/60/g' \
    -e 's/text-zinc-600/text-white\/50/g' \
    -e 's/text-zinc-400/text-white\/70/g' \
    -e 's/text-gray-500/text-white\/60/g' \
    -e 's/text-gray-400/text-white\/70/g' {} \;
```

Correr `php artisan view:clear` depois.

---

## PARTE C — Layout responsivo que usa o desktop

O problema: em desktop, o conteúdo fica num corredor de 672px (`max-w-2xl`) ao centro. Desperdiça 60% do ecrã.

### C.1 — Novo container responsivo no layout

Em `resources/views/layouts/minisite.blade.php`, o bloco principal deve ser:

```blade
<main class="min-h-screen">
    <div class="container mx-auto px-5 py-8 md:px-10 md:py-16 lg:px-16">
        <div class="mx-auto w-full max-w-3xl lg:max-w-5xl xl:max-w-6xl">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>
</main>
```

Importante: `max-w-3xl` (768px) no mobile/tablet e até `max-w-6xl` (1152px) no desktop. A `<x-hero>` continua a limitar os headings com `max-w-4xl` internamente.

### C.2 — Regras por tipo de mini-site

**Sites com grelha (Bingo, Horóscopo, Conversor, Lista):**
o corpo deve crescer para `max-w-5xl` ou `max-w-6xl` para aproveitar as colunas extra em desktop.

**Sites focais (Decisão, Proibido, Oráculo, Botão, Nada, Progresso):**
o corpo mantém `max-w-2xl` porque o impacto está na peça central, não em quantidade de conteúdo.

Em cada view, envolver o conteúdo no wrapper correcto:

```blade
{{-- Bingo, Horóscopo, etc --}}
<div class="mx-auto w-full max-w-5xl">
    {{-- grelha --}}
</div>

{{-- Decisão, Proibido, Oráculo --}}
<div class="mx-auto w-full max-w-2xl">
    {{-- peça central --}}
</div>
```

---

## PARTE D — Fixes específicos por site (dos screenshots)

### D.1 — Horóscopo (o menu de definições precisa de alma)

Em `resources/views/sites/horoscopo/index.blade.php`, na grelha de signos:

```blade
<div class="grid grid-cols-3 md:grid-cols-4 gap-3 md:gap-5 mx-auto max-w-5xl">
    @foreach($signos as $slug => [$emoji, $nome, $periodo])
    <a href="{{ route('horoscopo.signo', $slug) }}"
       class="group relative aspect-[4/5] flex flex-col items-center justify-center
              rounded-2xl p-4 md:p-6
              bg-gradient-to-br from-purple-950/60 to-purple-900/20
              border border-purple-500/20
              hover:border-purple-400 hover:from-purple-900/80 hover:to-purple-800/40
              hover:scale-[1.03] active:scale-95
              transition-all duration-300
              overflow-hidden">

        {{-- glow effect --}}
        <div class="absolute inset-0 bg-purple-500/0 group-hover:bg-purple-500/10 transition-colors"></div>

        <div class="relative text-4xl md:text-6xl mb-3 md:mb-4
                    group-hover:scale-110 group-hover:rotate-6 transition-transform">
            {{ $emoji }}
        </div>
        <div class="relative font-black text-white text-sm md:text-lg mb-1">{{ $nome }}</div>
        <div class="relative text-[10px] md:text-xs text-purple-200/60 font-medium tracking-wide">
            {{ $periodo }}
        </div>
    </a>
    @endforeach
</div>
```

Fundo da página muda para gradiente purple:

```blade
@section('body-class', 'bg-gradient-to-b from-[#0d0020] via-[#1a0033] to-[#0d0020]')
```

### D.2 — Decisão (está vazia, precisa de tensão)

Em `resources/views/sites/decisao/index.blade.php`, substituir o bloco das opções por:

```blade
<div class="relative mx-auto max-w-4xl mt-8 md:mt-16" x-data="decisao()">

    <p class="text-center text-xs md:text-sm font-bold uppercase tracking-[0.4em] text-purple-400/60 mb-10">
        Escolhe um lado
    </p>

    <div class="relative grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 min-h-[320px] md:min-h-[420px]">

        {{-- Opção A --}}
        <button @click="escolher('a')"
                class="group relative flex items-center justify-center p-8 md:p-12 rounded-3xl
                       bg-gradient-to-br from-red-950/40 via-zinc-900 to-zinc-950
                       border-2 border-red-900/40
                       hover:border-red-500 hover:from-red-900/60
                       active:scale-[0.98] transition-all duration-300
                       text-center overflow-hidden">
            <div class="absolute inset-0 bg-red-500/0 group-hover:bg-red-500/5 transition-colors"></div>
            <span class="relative text-2xl md:text-4xl font-black text-white leading-tight tracking-tight">
                {{ $dilema['a'] }}
            </span>
        </button>

        {{-- Opção B --}}
        <button @click="escolher('b')"
                class="group relative flex items-center justify-center p-8 md:p-12 rounded-3xl
                       bg-gradient-to-br from-blue-950/40 via-zinc-900 to-zinc-950
                       border-2 border-blue-900/40
                       hover:border-blue-500 hover:from-blue-900/60
                       active:scale-[0.98] transition-all duration-300
                       text-center overflow-hidden">
            <div class="absolute inset-0 bg-blue-500/0 group-hover:bg-blue-500/5 transition-colors"></div>
            <span class="relative text-2xl md:text-4xl font-black text-white leading-tight tracking-tight">
                {{ $dilema['b'] }}
            </span>
        </button>

        {{-- VS central --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                    w-14 h-14 md:w-20 md:h-20 rounded-full
                    bg-black border-2 border-white/20
                    flex items-center justify-center
                    text-white font-black text-sm md:text-lg tracking-wider
                    shadow-[0_0_40px_rgba(168,85,247,0.4)]">
            VS
        </div>

    </div>

    <p class="mt-10 text-center text-white/40 text-sm italic">
        Não há resposta certa. Mas há uma análise.
    </p>
</div>
```

### D.3 — Bingo (texto micro, fundos sem personalidade)

Em `resources/views/sites/bingo/index.blade.php`, a grelha deve ser:

```blade
<div class="mx-auto max-w-5xl">
    <div class="grid grid-cols-3 md:grid-cols-5 gap-2 md:gap-3">
        @foreach($quadrados as $idx => $texto)
        <button @click="toggle({{ $idx }})"
                :class="marcados.includes({{ $idx }})
                    ? 'bg-yellow-400 border-yellow-300 text-black'
                    : 'bg-zinc-900 border-zinc-700 text-white hover:border-yellow-500/60'"
                class="aspect-square p-2 md:p-4 rounded-xl md:rounded-2xl
                       border-2 transition-all duration-200
                       flex items-center justify-center text-center
                       text-[11px] md:text-sm font-bold leading-tight
                       hover:scale-[1.03] active:scale-95">
            <span x-show="!marcados.includes({{ $idx }})">{{ $texto }}</span>
            <span x-show="marcados.includes({{ $idx }})" class="text-2xl md:text-4xl">✓</span>
        </button>
        @endforeach
    </div>
</div>
```

Alterações chave: `aspect-square` em vez de altura variável, `text-[11px]` mobile / `text-sm` desktop (legível), marca com preenchimento sólido amarelo + check grande em vez do texto ilegível.

### D.4 — Lista de Coisas Que Nunca Vais Fazer

Em `resources/views/sites/lista/index.blade.php`, os itens devem ter mais presença:

```blade
<ul class="mx-auto max-w-2xl space-y-2">
    @foreach($itens as $idx => $item)
    <li>
        <label class="group flex items-center gap-4 p-4 md:p-5 rounded-xl
                      bg-zinc-900/50 hover:bg-zinc-900
                      border border-zinc-800 hover:border-orange-500/40
                      cursor-pointer transition-all">
            <input type="checkbox"
                   @change="toggle({{ $idx }})"
                   :checked="marcados.includes({{ $idx }})"
                   class="w-5 h-5 rounded accent-orange-500 cursor-pointer">
            <span :class="marcados.includes({{ $idx }}) ? 'line-through text-white/30' : 'text-white'"
                  class="text-base md:text-lg font-semibold transition-colors">
                {{ $item }}
            </span>
        </label>
    </li>
    @endforeach
</ul>
```

Contador em cima fica mais bold:

```blade
<div class="text-center mb-10">
    <div class="inline-flex items-baseline gap-2">
        <span x-text="marcados.length" class="text-6xl md:text-8xl font-black text-orange-500 tabular-nums"></span>
        <span class="text-2xl md:text-3xl text-white/40 font-bold">/ 30</span>
    </div>
    <p class="mt-2 text-white/60 font-medium" x-text="mensagem"></p>
</div>
```

---

## PARTE E — Verificação

### E.1 — Correr o reviewer

```bash
php artisan clinky:review --visual
```

Na secção visual, os screenshots mobile devem mostrar:
- Headings acima de 48px (font-size real)
- Sem overflow horizontal
- Texto visível com contraste ≥ 4.5:1

### E.2 — Teste manual do back-link

Abre `http://localhost:8000` → scroll até à secção do Bingo → clica "Experimentar" → no `/bingo` clica `← clinky.cc` → deve regressar a `/#bingo` com scroll directo à secção.

Repete para 3 sites diferentes (um grelha, um focal, um com form).

### E.3 — Teste em viewport real

Chrome DevTools:
- 375×812 (iPhone SE)
- 768×1024 (iPad)
- 1440×900 (desktop médio)

Em cada, verificar Horóscopo, Decisão, Bingo e Lista.

---

## Checklist final

- [ ] Todas as secções da homepage têm `id="{slug}"`
- [ ] `scroll-behavior: smooth` aplicado globalmente
- [ ] Back-link volta a `/#{slug}` com anchor
- [ ] Inter importada e `font-black` aplicada nos headings
- [ ] Contraste actualizado (sem `zinc-500` em fundos escuros)
- [ ] `<x-hero>` a ser usado em todos os mini-sites
- [ ] Layout adapta-se ao desktop (max-w-5xl em sites de grelha)
- [ ] Horóscopo, Decisão, Bingo, Lista aplicam os fixes específicos da PARTE D
- [ ] `php artisan clinky:review --visual` passa ou só tem avisos

Confirma cada passo com ✅ antes de avançar. Se algum site estiver tão diferente do padrão que os snippets não se aplicam directamente, mostra-me o view actual antes de editar.
