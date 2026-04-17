@props([
    'emoji'    => null,
    'title',
    'tagline'  => null,
    'accent'   => '#c8f135',
    'eyebrow'  => null,
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
