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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">

    <div class="fixed top-4 left-4 z-50">
        <a href="{{ route('home') }}"
           class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 flex items-center gap-1 transition-colors">
            ← clinky.cc
        </a>
    </div>

    <main class="min-h-screen">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
