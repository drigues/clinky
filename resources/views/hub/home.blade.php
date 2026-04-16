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
  "url": "https://{{ config('app.base_domain') }}",
  "inLanguage": "pt-PT"
}
</script>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <header class="text-center mb-12">
        <h1 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
            Clinky.cc
        </h1>
        <p class="mt-3 text-zinc-500 dark:text-zinc-400 text-sm max-w-sm mx-auto">
            Mini-sites virais, inúteis e partilháveis. Sem registo, sem dados, sem sentido.
        </p>
    </header>

    <div class="grid grid-cols-2 gap-3">
        @foreach($sites as $site)
        @php
            $isLive = $site['live'];
            $cardClasses = $isLive
                ? 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-lg hover:-translate-y-0.5 cursor-pointer'
                : 'border-zinc-100 dark:border-zinc-800/50 opacity-60 cursor-default';
            $badgeText = $isLive ? ($site['tag'] ?? null) : 'Em breve';
        @endphp
        <a href="{{ $isLive ? $site['url'] : '#' }}"
           {!! $isLive ? '' : 'aria-disabled="true"' !!}
           class="group relative flex flex-col items-center text-center p-6 rounded-2xl border transition-all duration-200 {{ $cardClasses }}">

            @if($badgeText)
            <span class="absolute top-2 right-2 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400">
                {{ $badgeText }}
            </span>
            @endif

            <span class="text-4xl mb-3 transition-transform duration-200 group-hover:scale-110">
                {{ $site['emoji'] }}
            </span>

            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $site['title'] }}
            </span>

            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $site['tagline'] }}
            </span>
        </a>
        @endforeach
    </div>

</div>
@endsection
