@extends('layouts.hub')

@section('title', 'Clinky.cc — Mini-sites virais, inúteis e partilháveis')
@section('description', 'Uma colecção de mini-sites absurdos, divertidos e feitos para partilhar no WhatsApp. Grátis, sem registo, sem dados.')

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Clinky.cc",
  "description": "Hub de mini-sites virais, inúteis e partilháveis",
  "url": "{{ route('home') }}",
  "inLanguage": "pt-PT"
}
</script>
@endpush

@push('head')
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
@endpush

@section('content')

{{-- ==================== HERO ==================== --}}
<section class="relative min-h-screen flex flex-col items-center justify-center bg-[#0a0a0a] overflow-hidden">

    <div class="absolute inset-0 opacity-10"
         style="background-image: radial-gradient(circle, #c8f135 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="absolute inset-0"
         style="background: radial-gradient(ellipse at center, #c8f13515 0%, transparent 70%)"></div>

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

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/20 animate-bounce">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12l7 7 7-7"/>
        </svg>
    </div>

</section>

{{-- ==================== SECÇÕES ==================== --}}
@foreach($sites as $site)
<section class="relative min-h-screen flex items-center overflow-hidden"
         style="background-color: {{ $site['bg'] }}">

    @if($site['has_image'])
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/og/' . $site['slug'] . '.png') }}"
             alt=""
             loading="lazy"
             width="1200"
             height="630"
             class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
    </div>
    @endif

    <div class="absolute inset-0 z-0"
         style="background: linear-gradient(135deg, {{ $site['bg'] }}ee 0%, {{ $site['bg'] }}88 50%, transparent 100%)"></div>

    <div class="relative z-10 max-w-2xl mx-auto px-6 py-24 md:px-12">

        <p class="text-xs font-bold uppercase tracking-[0.3em] text-white/40 mb-4 reveal">
            {{ str_pad($site['n'], 2, '0', STR_PAD_LEFT) }} — {{ $site['cat'] }}
        </p>

        <div class="text-8xl mb-6 reveal reveal-delay-1">{{ $site['emoji'] }}</div>

        <h2 class="text-5xl md:text-7xl font-black text-white leading-[0.9] tracking-tight mb-4 reveal reveal-delay-2">
            {{ $site['title'] }}
        </h2>

        <p class="text-xl text-white/70 max-w-md leading-relaxed mb-10 reveal reveal-delay-3">
            {{ $site['desc'] }}
        </p>

        <div class="flex items-center flex-wrap gap-4 reveal reveal-delay-4">
            <a href="/{{ $site['slug'] }}"
               class="inline-flex items-center gap-3 bg-white text-black font-bold text-lg px-8 py-4 rounded-full hover:scale-105 active:scale-95 transition-transform">
                Experimentar agora
                <span class="text-2xl">→</span>
            </a>

            @if($site['tag'])
            <span class="inline-block text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full"
                  style="background: {{ $site['tag_bg'] }}; color: {{ $site['tag_text'] }}">
                {{ $site['tag'] }}
            </span>
            @endif
        </div>

    </div>

    <div class="absolute right-0 top-1/2 -translate-y-1/2 text-[20rem] font-black text-white/5 select-none leading-none pr-4 hidden md:block">
        {{ str_pad($site['n'], 2, '0', STR_PAD_LEFT) }}
    </div>

</section>
@endforeach

{{-- ==================== FOOTER ==================== --}}
<footer class="bg-[#0a0a0a] border-t border-white/10 py-12 text-center">
    <p class="text-3xl font-black text-white mb-2">Clinky<span style="color:#c8f135">.cc</span></p>
    <p class="text-white/30 text-sm mb-6">Mini-sites virais, inúteis e partilháveis.</p>
    <div class="flex justify-center gap-6 text-white/30 text-xs">
        <a href="{{ route('privacidade') }}" class="hover:text-white/60 transition-colors">Privacidade</a>
        <span>·</span>
        <span>&copy; {{ date('Y') }} Clinky.cc</span>
        <span>·</span>
        <a href="https://thr33.xyz" target="_blank" rel="noopener noreferrer" class="hover:text-white/60 transition-colors">thr33.xyz</a>
    </div>
</footer>

@endsection

@push('scripts')
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
@endpush
