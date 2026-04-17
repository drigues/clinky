<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') — Clinky.cc</title>
    <meta name="description" content="@yield('description')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ request()->url() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title'))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('description'))">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og/default.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Clinky.cc">
    <meta property="og:locale" content="pt_PT">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', View::yieldContent('title'))">
    <meta name="twitter:description" content="@yield('og_description', View::yieldContent('description'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og/default.png'))">

    @if(config('services.fathom.site_id'))
    <script src="https://cdn.usefathom.com/script.js"
            data-site="{{ config('services.fathom.site_id') }}"
            data-canonical="{{ request()->url() }}"
            defer></script>
    @endif

    @stack('structured_data')

    <x-google-analytics />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">

    @php
        $slug = $slug ?? request()->segment(1) ?? '';
        $backUrl = $slug ? route('home') . '#' . $slug : route('home');
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

    <main class="min-h-screen">
        <div class="container mx-auto px-5 py-8 md:px-10 md:py-16 lg:px-16">
            <div class="mx-auto w-full max-w-3xl lg:max-w-5xl xl:max-w-6xl">
                @yield('content')
            </div>
        </div>
    </main>

    @stack('scripts')
    <x-cookie-banner />
</body>
</html>
