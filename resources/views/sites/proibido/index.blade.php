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
  "name": "O Botão Proibido",
  "description": "{{ $seo['description'] }}",
  "url": "{{ $seo['canonical'] }}",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT",
  "isPartOf": { "@@type": "WebSite", "name": "Clinky.cc", "url": "{{ route('home') }}" }
}
</script>
@endpush

@push('head')
<style>
    body { background: #1a0000 !important; }

    .btn-proibido {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle at 40% 35%, #4a0000, #2a0000 60%, #1a0000);
        border: 3px solid #5a0000;
        box-shadow: 0 0 30px rgba(139, 0, 0, 0.3), inset 0 -4px 8px rgba(0,0,0,0.4);
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        animation: pulseBtn 3s ease-in-out infinite;
        position: relative;
    }
    .btn-proibido:hover {
        box-shadow: 0 0 50px rgba(139, 0, 0, 0.5), inset 0 -4px 8px rgba(0,0,0,0.4);
    }
    .btn-proibido:active {
        transform: scale(0.92);
        box-shadow: 0 0 15px rgba(139, 0, 0, 0.2), inset 0 4px 8px rgba(0,0,0,0.5);
        animation: none;
    }
    .btn-proibido.loading {
        animation: none;
        opacity: 0.6;
        pointer-events: none;
    }

    @keyframes pulseBtn {
        0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(139, 0, 0, 0.3), inset 0 -4px 8px rgba(0,0,0,0.4); }
        50% { transform: scale(1.04); box-shadow: 0 0 45px rgba(139, 0, 0, 0.45), inset 0 -4px 8px rgba(0,0,0,0.4); }
    }

    .resultado-fade-enter {
        animation: fadeUp 0.5s ease-out forwards;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4 pb-32"
     x-data="proibido()">

    {{-- Aviso progressivo --}}
    <p class="text-zinc-500 text-sm tracking-widest uppercase mb-10 text-center transition-all duration-500"
       x-text="aviso">
    </p>

    {{-- Botão --}}
    <button @click="carregar()"
            :class="{ 'loading': loading }"
            :disabled="loading"
            class="btn-proibido flex items-center justify-center"
            aria-label="O botão proibido">
        <span class="text-3xl select-none" x-show="!loading">🚫</span>
        <span x-show="loading" x-cloak class="flex gap-1">
            <span class="w-2 h-2 bg-red-900 rounded-full animate-bounce" style="animation-delay:0ms"></span>
            <span class="w-2 h-2 bg-red-900 rounded-full animate-bounce" style="animation-delay:150ms"></span>
            <span class="w-2 h-2 bg-red-900 rounded-full animate-bounce" style="animation-delay:300ms"></span>
        </span>
    </button>

    {{-- Contador de cliques --}}
    <p x-show="cliques > 0" x-cloak
       class="text-zinc-700 text-xs mt-6 tabular-nums transition-all duration-300"
       x-text="cliques + '×'">
    </p>

    {{-- Resultado --}}
    <div class="h-24 mt-8 flex items-center justify-center max-w-md">
        <p x-show="resultado !== null && !loading" x-cloak
           class="resultado-fade-enter text-center text-lg md:text-xl font-medium leading-relaxed"
           :class="tipo === 'silencio' ? 'text-zinc-800' : tipo === 'raro' ? 'text-amber-500' : tipo === 'elogio' ? 'text-rose-400' : 'text-zinc-400'"
           x-text="resultado">
        </p>
    </div>

</div>

{{-- Share bar — aparece após 3 cliques --}}
<div x-data="{
        shareUrl: '{{ route('proibido.index') }}',
        shareText: 'Não carregues neste botão. Sério.\n\n→ ',
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            const t = document.querySelector('meta[name=csrf-token]');
            if (!t) return;
            fetch('/api/track', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t.content }, body: JSON.stringify({ event: 'share_' + platform }) }).catch(() => {});
        },
        async nativeShare() { try { await navigator.share({ text: this.shareText + this.shareUrl, url: this.shareUrl, title: document.title }); this.track('native') } catch(e) {} },
        copy() { navigator.clipboard.writeText(this.shareText + this.shareUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) }); this.track('copy') },
        get whatsappUrl() { return 'https://wa.me/?text=' + encodeURIComponent(this.shareText + this.shareUrl) }
     }"
     x-show="$store.proibido.mostrarShare" x-cloak
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="fixed bottom-0 left-0 right-0 p-4 bg-[#1a0000]/90 backdrop-blur border-t border-red-950">
    <div class="flex items-center gap-2 max-w-sm mx-auto">
        <a :href="whatsappUrl" target="_blank" rel="noopener" @click="track('whatsapp')"
           class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] text-white font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.122 1.523 5.857L.057 23.492a.5.5 0 0 0 .604.634l5.822-1.527A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.833 9.833 0 0 1-5.028-1.377l-.36-.214-3.733.979 1.002-3.644-.235-.374A9.818 9.818 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
            Partilhar aviso
        </a>
        <button @click="copy()"
                class="flex items-center justify-center gap-2 border border-red-900 text-zinc-400 font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
            <span x-show="!copied">Copiar</span>
            <span x-show="copied" x-cloak>Copiado</span>
        </button>
        <button x-show="canShare" x-cloak @click="nativeShare()"
                class="flex items-center justify-center border border-red-900 text-zinc-400 py-3 px-3 rounded-xl transition-transform active:scale-95">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('proibido', { mostrarShare: false })

    Alpine.data('proibido', () => ({
        cliques: 0,
        resultado: null,
        loading: false,
        tipo: null,

        get aviso() {
            if (this.cliques >= 10) return 'Sabíamos que ias fazer isto.';
            if (this.cliques >= 5)  return 'Já dissemos para não carregar.';
            if (this.cliques >= 1)  return 'Outra vez?';
            return 'NÃO CARREGUES NESTE BOTÃO.';
        },

        async carregar() {
            this.loading = true;
            this.resultado = null;
            this.cliques++;

            try {
                const res = await fetch('{{ route('proibido.carregar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();

                // Suspense de 800ms
                await new Promise(r => setTimeout(r, 800));

                this.resultado = data.texto;
                this.tipo = data.tipo;
            } catch(e) {
                this.resultado = 'Algo correu mal. Tenta novamente.';
                this.tipo = 'erro';
            } finally {
                this.loading = false;
            }

            // Mostrar share bar após 3 cliques
            if (this.cliques >= 3) {
                Alpine.store('proibido').mostrarShare = true;
            }
        }
    }))
})
</script>
@endpush
