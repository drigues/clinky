# COMPONENTS.md — Clinky.cc

## Componentes Blade partilhados

Todos os mini-sites reutilizam estes componentes. Nunca duplicar código.

---

## 1. Share Bar (`components/share-bar.blade.php`)

Barra fixa no fundo com botões de partilha. **Obrigatório em todos os mini-sites.**

```blade
{{-- resources/views/components/share-bar.blade.php --}}
@props(['text', 'url', 'label' => 'Partilhar resultado'])

@php
$whatsappText = urlencode($text . "\n\n" . $url);
$whatsappUrl  = "https://wa.me/?text={$whatsappText}";
$twitterUrl   = "https://twitter.com/intent/tweet?text=" . urlencode($text) . "&url=" . urlencode($url);
@endphp

<div x-data="shareBar('{{ $text }}', '{{ $url }}')"
     class="flex items-center gap-2 max-w-sm mx-auto">

    {{-- WhatsApp (principal) --}}
    <a href="{{ $whatsappUrl }}"
       target="_blank" rel="noopener"
       @click="track('whatsapp')"
       class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] text-white font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.122 1.523 5.857L.057 23.492a.5.5 0 0 0 .604.634l5.822-1.527A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.833 9.833 0 0 1-5.028-1.377l-.36-.214-3.733.979 1.002-3.644-.235-.374A9.818 9.818 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
        </svg>
        WhatsApp
    </a>

    {{-- Copiar link --}}
    <button @click="copy()"
            class="flex items-center justify-center gap-2 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
        <span x-show="!copied">Copiar</span>
        <span x-show="copied" x-cloak>✓ Copiado</span>
    </button>

    {{-- Web Share API (mobile nativo) --}}
    <button x-show="canShare" x-cloak
            @click="nativeShare()"
            class="flex items-center justify-center border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 py-3 px-3 rounded-xl transition-transform active:scale-95">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
        </svg>
    </button>
</div>

<script>
function shareBar(text, url) {
    return {
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            fetch('/api/track', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ event: 'share_' + platform })
            }).catch(() => {});
        },
        async nativeShare() {
            try {
                await navigator.share({ text, url, title: document.title });
                this.track('native');
            } catch(e) {}
        },
        copy() {
            navigator.clipboard.writeText(url).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
            this.track('copy');
        }
    }
}
</script>
```

---

## 2. Result Card (`components/result-card.blade.php`)

Card para mostrar resultado gerado (por IA ou algoritmo).

```blade
{{-- resources/views/components/result-card.blade.php --}}
@props(['result', 'emoji' => '✨', 'loading' => false])

<div class="relative bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 my-6">

    {{-- Loading state --}}
    @if($loading)
    <div class="flex items-center gap-3 text-zinc-500">
        <div class="flex gap-1">
            <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
            <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
            <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
        </div>
        <span class="text-sm">A gerar...</span>
    </div>
    @else
    <div class="text-3xl mb-3">{{ $emoji }}</div>
    <p class="text-lg font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed">
        {{ $result }}
    </p>
    @endif

    {{-- Marca Clinky subtil --}}
    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
        <a href="https://clinky.cc" class="text-xs text-zinc-400 hover:text-zinc-500">clinky.cc</a>
    </div>
</div>
```

---

## 3. Site Header (`components/site-header.blade.php`)

Header padrão de mini-site com emoji, título e tagline.

```blade
{{-- resources/views/components/site-header.blade.php --}}
@props(['emoji', 'title', 'tagline', 'accentColor' => 'lime'])

@php
$colors = [
    'lime'   => 'text-lime-500 dark:text-lime-400',
    'orange' => 'text-orange-500 dark:text-orange-400',
    'pink'   => 'text-pink-500 dark:text-pink-400',
    'blue'   => 'text-blue-500 dark:text-blue-400',
    'purple' => 'text-purple-500 dark:text-purple-400',
    'teal'   => 'text-teal-500 dark:text-teal-400',
    'yellow' => 'text-yellow-500 dark:text-yellow-400',
    'red'    => 'text-red-500 dark:text-red-400',
];
$colorClass = $colors[$accentColor] ?? $colors['lime'];
@endphp

<header class="text-center py-12 px-4">
    <div class="text-6xl mb-4">{{ $emoji }}</div>
    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
        {{ $title }}
    </h1>
    <p class="mt-2 text-zinc-500 dark:text-zinc-400 text-sm max-w-xs mx-auto">
        {{ $tagline }}
    </p>
</header>
```

---

## 4. Counter Badge (`components/counter-badge.blade.php`)

Mostra contador global (ex: total de gerações) com animação.

```blade
{{-- resources/views/components/counter-badge.blade.php --}}
@props(['count', 'label' => 'vezes'])

<div class="inline-flex items-center gap-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs font-medium px-3 py-1.5 rounded-full">
    <span class="w-1.5 h-1.5 bg-lime-500 rounded-full animate-pulse"></span>
    <span x-data="{ n: 0, target: {{ $count }} }"
          x-init="let s=setInterval(()=>{ n=Math.min(n+Math.ceil(target/40), target); if(n>=target) clearInterval(s); }, 40)">
        <span x-text="n.toLocaleString('pt-PT')"></span>
    </span>
    {{ $label }}
</div>
```

---

## 5. Layout mini-site completo

Padrão de view para um mini-site com IA:

```blade
{{-- resources/views/sites/desculpometro/index.blade.php --}}
@extends('layouts.minisite')

@section('title', $seo['title'])
@section('description', $seo['description'])
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_image', $seo['og_image'])

@push('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Desculpómetro",
  "description": "{{ $seo['description'] }}",
  "url": "https://desculpometro.clinky.cc",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT"
}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-white dark:bg-zinc-950 px-4 pb-32" x-data="desculpometro()">

    <x-site-header
        emoji="😅"
        title="Desculpómetro"
        tagline="Gera a desculpa perfeita em 1 segundo"
        accentColor="orange"
    />

    <x-counter-badge :count="$totalGeradas" label="desculpas geradas" />

    {{-- Form --}}
    <form @submit.prevent="gerar" class="max-w-sm mx-auto mt-8 space-y-4">
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Qual é a situação?
            </label>
            <select x-model="situacao"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm">
                <option value="trabalho">Faltar ao trabalho</option>
                <option value="ginasio">Faltar ao ginásio</option>
                <option value="familia">Evitar família</option>
                <option value="encontro">Cancelar encontro</option>
                <option value="reuniao">Sair da reunião</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Grau de absurdo
            </label>
            <div class="flex gap-2">
                @foreach(['Normal 😐', 'Criativo 😏', 'Épico 🤌', 'Absurdo 🤯'] as $i => $grau)
                <button type="button"
                        @click="absurdo = {{ $i }}"
                        :class="absurdo === {{ $i }} ? 'bg-orange-500 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300'"
                        class="flex-1 text-xs py-2 px-1 rounded-lg font-medium transition-colors">
                    {{ $grau }}
                </button>
                @endforeach
            </div>
        </div>

        <button type="submit"
                :disabled="loading"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95 disabled:opacity-50">
            <span x-show="!loading">Gerar Desculpa</span>
            <span x-show="loading" x-cloak>A criar magia...</span>
        </button>
    </form>

    {{-- Resultado --}}
    <div x-show="resultado || loading" x-cloak>
        <x-result-card
            x-bind:result="resultado"
            x-bind:loading="loading"
            emoji="😅"
        />
    </div>

</div>

{{-- Share bar fixa --}}
<div x-show="resultado" x-cloak
     class="fixed bottom-0 left-0 right-0 p-4 bg-white/90 dark:bg-zinc-950/90 backdrop-blur border-t border-zinc-200 dark:border-zinc-800">
    <x-share-bar
        x-bind:text="resultado + '\n\nGera a tua desculpa em:'"
        url="https://desculpometro.clinky.cc"
    />
</div>
@endsection

@push('scripts')
<script>
function desculpometro() {
    return {
        situacao: 'trabalho',
        absurdo: 1,
        resultado: '',
        loading: false,
        async gerar() {
            this.loading = true;
            this.resultado = '';
            try {
                const res = await fetch('/gerar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ situacao: this.situacao, absurdo: this.absurdo })
                });
                const data = await res.json();
                this.resultado = data.desculpa;
            } catch(e) {
                this.resultado = 'Ocorreu um erro. Tenta novamente.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
```

---

## Tailwind config base

```javascript
// tailwind.config.js
export default {
    content: ['./resources/**/*.blade.php', './resources/**/*.js'],
    darkMode: 'media',
    theme: {
        extend: {
            fontFamily: {
                display: ['Syne', 'sans-serif'],
                sans: ['system-ui', '-apple-system', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
```

---

## Alpine.js — directivas globais

Registar no `resources/js/app.js`:

```javascript
import Alpine from 'alpinejs'
import { shareBar, desculpometro, botao } from './sites'

Alpine.data('shareBar', shareBar)
Alpine.data('desculpometro', desculpometro)
Alpine.data('botao', botao)

Alpine.start()
```
