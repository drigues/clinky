<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Clinky.cc — Mini-sites virais, inúteis e partilháveis')</title>
    <meta name="description" content="@yield('description', 'Uma colecção de mini-sites absurdos, divertidos e feitos para partilhar no WhatsApp. Grátis, sem registo, sem dados.')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ request()->url() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'Clinky.cc — Mini-sites virais e inúteis')">
    <meta property="og:description" content="@yield('og_description', 'Descobre ferramentas absurdas, divertidas e 100% partilháveis.')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og/default.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Clinky.cc">
    <meta property="og:locale" content="pt_PT">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Clinky.cc — Mini-sites virais e inúteis')">
    <meta name="twitter:description" content="@yield('og_description', 'Descobre ferramentas absurdas, divertidas e 100% partilháveis.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og/default.png'))">

    {{-- Inter font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
<body class="bg-[#0a0a0a] text-zinc-100 antialiased">

    <main>
        @yield('content')
    </main>

    @stack('scripts')
    <x-cookie-banner />
</body>
</html>
