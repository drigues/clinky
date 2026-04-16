@extends('layouts.hub')

@section('title', 'Clinky.cc — Mini-sites virais, inúteis e partilháveis')
@section('description', 'Uma colecção de mini-sites absurdos, divertidos e feitos para partilhar no WhatsApp. Grátis, sem registo, sem dados.')

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Clinky.cc",
  "description": "Hub de mini-sites virais, inúteis e partilháveis",
  "url": "{{ route('home') }}",
  "inLanguage": "pt-PT"
}
</script>
@endpush

@section('content')
<div class="w-full max-w-[560px] mx-auto px-6 py-16">

    <header class="text-center mb-10">
        <h1 class="text-5xl font-black tracking-tight logo-shimmer">
            <span aria-hidden="true">&#10022;</span> Clinky.cc
        </h1>
        <p class="mt-3 text-[#71717a] text-sm leading-relaxed max-w-xs mx-auto">
            Mini-sites virais, inúteis e partilháveis.<br>
            Sem registo, sem dados, sem sentido.
        </p>
    </header>

    <div class="flex flex-col gap-3">

        {{-- Desculpómetro — TOP --}}
        <a href="{{ route('desculpometro.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-[#c8f135]/60 hover:shadow-[0_0_20px_-6px_rgba(200,241,53,0.15)] hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="absolute top-2.5 right-3 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-[#c8f135]/15 text-[#c8f135]">TOP</span>
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">😅</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Desculpómetro</p>
                <p class="text-[#71717a] text-sm mt-0.5">Gera a desculpa perfeita</p>
            </div>
        </a>

        {{-- Aperta o Botão — EM ALTA --}}
        <a href="{{ route('botao.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-orange-500/60 hover:shadow-[0_0_20px_-6px_rgba(249,115,22,0.15)] hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="absolute top-2.5 right-3 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-orange-500/15 text-orange-400">EM ALTA</span>
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">🔴</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Aperta o Botão</p>
                <p class="text-[#71717a] text-sm mt-0.5">Um botão. Sem explicação.</p>
            </div>
        </a>

        {{-- Nomeador de Grupos --}}
        <a href="{{ route('nomeador.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-zinc-600/60 hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">💬</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Nomeador de Grupos</p>
                <p class="text-[#71717a] text-sm mt-0.5">Nomes épicos para o teu WhatsApp</p>
            </div>
        </a>

        {{-- Horóscopo Inútil --}}
        <a href="{{ route('horoscopo.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-zinc-600/60 hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">🔮</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Horóscopo Inútil</p>
                <p class="text-[#71717a] text-sm mt-0.5">Previsões 100% inventadas</p>
            </div>
        </a>

        {{-- Analisador de Nome --}}
        <a href="{{ route('nome.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-zinc-600/60 hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">🧬</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Analisador de Nome</p>
                <p class="text-[#71717a] text-sm mt-0.5">Descobre o que o teu nome diz</p>
            </div>
        </a>

        {{-- Bingo do Imigrante — PT/BR --}}
        <a href="{{ route('bingo.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-purple-500/60 hover:shadow-[0_0_20px_-6px_rgba(168,85,247,0.15)] hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="absolute top-2.5 right-3 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-purple-500/15 text-purple-400">PT/BR</span>
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">🎯</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Bingo do Imigrante</p>
                <p class="text-[#71717a] text-sm mt-0.5">Reconheces a tua vida em Portugal?</p>
            </div>
        </a>

        {{-- Conversor PT ↔ BR — PT/BR --}}
        <a href="{{ route('conversor.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-purple-500/60 hover:shadow-[0_0_20px_-6px_rgba(168,85,247,0.15)] hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="absolute top-2.5 right-3 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-purple-500/15 text-purple-400">PT/BR</span>
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">🇵🇹🇧🇷</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Conversor PT ↔ BR</p>
                <p class="text-[#71717a] text-sm mt-0.5">Traduz entre português e brasileiro</p>
            </div>
        </a>

        {{-- Sou mais BR ou PT? — PT/BR --}}
        <a href="{{ route('quiz.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-purple-500/60 hover:shadow-[0_0_20px_-6px_rgba(168,85,247,0.15)] hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="absolute top-2.5 right-3 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full bg-purple-500/15 text-purple-400">PT/BR</span>
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">🤔</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Sou mais BR ou PT?</p>
                <p class="text-[#71717a] text-sm mt-0.5">Descobre o teu nível de sotaque</p>
            </div>
        </a>

        {{-- Tradutor Corporativo --}}
        <a href="{{ route('corporativo.index') }}"
           class="group flex items-center gap-4 w-full p-4 rounded-2xl border border-[#1e1e1e] bg-[#141414] hover:border-zinc-600/60 hover:bg-[#1a1a1a] hover:-translate-y-0.5 transition-all duration-200 relative">
            <span class="text-4xl shrink-0 transition-transform duration-200 group-hover:scale-110">💼</span>
            <div class="text-left">
                <p class="text-[#f4f4f5] font-bold text-lg leading-tight tracking-tight">Tradutor Corporativo</p>
                <p class="text-[#71717a] text-sm mt-0.5">O que realmente querem dizer</p>
            </div>
        </a>

    </div>

</div>
@endsection
