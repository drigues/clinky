@extends('layouts.minisite')

@section('title', $seo['title'])
@section('description', $seo['description'])
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_image', $seo['og_image'])

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebApplication",
  "name": "Horóscopo Inútil",
  "description": "{{ $seo['description'] }}",
  "url": "{{ $seo['canonical'] }}",
  "applicationCategory": "EntertainmentApplication",
  "operatingSystem": "Web",
  "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "EUR" },
  "inLanguage": "pt-PT",
  "isPartOf": { "@@type": "WebSite", "name": "Clinky.cc", "url": "https://{{ config('app.base_domain') }}" }
}
</script>
@endpush

@section('content')
<div class="min-h-screen px-4 pb-12">

    <x-site-header
        emoji="🔮"
        title="Horóscopo Inútil"
        tagline="Previsões 100% inventadas"
        accentColor="purple"
    />

    <div class="max-w-sm mx-auto">
        <p class="text-center text-xs text-zinc-400 dark:text-zinc-500 mb-6">Escolhe o teu signo</p>

        <div class="grid grid-cols-3 gap-3">
            @foreach($signos as $slug => $info)
            <a href="{{ url($slug) }}"
               class="flex flex-col items-center p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-md transition-all duration-200 group">
                <span class="text-3xl mb-1 transition-transform duration-200 group-hover:scale-110">{{ $info[0] }}</span>
                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">{{ $info[1] }}</span>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $info[2] }}</span>
            </a>
            @endforeach
        </div>
    </div>

</div>
@endsection
