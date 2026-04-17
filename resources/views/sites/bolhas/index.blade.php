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
  "name": "Rebenta as Bolhas",
  "description": "{{ $seo['description'] }}",
  "url": "{{ $seo['canonical'] }}",
  "applicationCategory": "EntertainmentApplication",
  "operatingSystem": "Web",
  "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "EUR" },
  "inLanguage": "pt-PT",
  "isPartOf": { "@@type": "WebSite", "name": "Clinky.cc", "url": "{{ route('home') }}" }
}
</script>
@endpush

@section('content')
<div class="min-h-screen flex flex-col items-center px-4 pb-32 pt-16" x-data="bolhas()">

    {{-- Header --}}
    <header class="text-center mb-8">
        <div class="text-6xl mb-4">🫧</div>
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
            Rebenta as Bolhas
        </h1>
        <p class="mt-2 text-zinc-500 dark:text-white/70 text-sm max-w-xs mx-auto">
            Sem propósito. Sem fim. Completamente viciante.
        </p>
    </header>

    {{-- Contador --}}
    <div class="mb-6 text-center">
        <p class="text-sm text-zinc-500 dark:text-white/70">Rebentaste</p>
        <p class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 tabular-nums"
           x-text="contador.toLocaleString('pt-PT')">0</p>
        <p class="text-sm text-zinc-500 dark:text-white/70">bolhas</p>
    </div>

    {{-- Mensagem progressiva --}}
    <div class="h-8 mb-6 text-center">
        <p x-show="mensagem" x-cloak
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           class="text-sm text-cyan-500 dark:text-cyan-400 italic"
           x-text="mensagem">
        </p>
    </div>

    {{-- Grelha de bolhas --}}
    <div class="grid gap-2 w-full max-w-sm mx-auto"
         style="grid-template-columns: repeat(8, 1fr)">

        <template x-for="i in total" :key="gridKey + '-' + i">
            <button @click="rebentar(i)"
                    :class="rebentadas.includes(i)
                        ? 'opacity-10 scale-0 pointer-events-none'
                        : 'scale-100 hover:scale-110 active:scale-75'"
                    class="aspect-square rounded-full transition-all duration-200 ease-out
                           bg-gradient-to-br from-cyan-200/40 to-blue-400/20
                           dark:from-cyan-400/30 dark:to-blue-500/15
                           border border-cyan-300/30 dark:border-cyan-500/20
                           shadow-inner cursor-pointer
                           focus:outline-none focus:ring-2 focus:ring-cyan-400/40"
                    aria-label="Rebentar bolha">
            </button>
        </template>
    </div>

</div>

{{-- Share bar --}}
<div x-data="{
        shareUrl: '{{ route('bolhas.index') }}',
        get shareText() { return 'Já rebentei ' + (document.querySelector('[x-data=bolhas\\(\\)]') ? '' : '0') + ' bolhas neste site completamente inútil e não consigo parar 🫧' },
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            const t = document.querySelector('meta[name=csrf-token]')
            if (!t) return
            fetch('/api/track', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t.content }, body: JSON.stringify({ event: 'share_' + platform }) }).catch(() => {})
            if (typeof gtagEvent !== 'undefined') gtagEvent('share', { method: platform, site: '/bolhas' })
        },
        async nativeShare() { try { await navigator.share({ text: this.shareText, url: this.shareUrl, title: document.title }); this.track('native') } catch(e) {} },
        copy() { navigator.clipboard.writeText(this.shareText + '\n\n' + this.shareUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) }); this.track('copy') },
        get whatsappUrl() { return 'https://wa.me/?text=' + encodeURIComponent(this.shareText + '\n\nTenta tu: ' + this.shareUrl) }
     }"
     x-show="$store.bolhas.mostrarShare" x-cloak
     class="fixed bottom-0 left-0 right-0 p-4 bg-white/90 dark:bg-zinc-950/90 backdrop-blur border-t border-zinc-200 dark:border-zinc-800">
    <div class="flex items-center gap-2 max-w-sm mx-auto">
        <a :href="whatsappUrl" target="_blank" rel="noopener" @click="track('whatsapp')"
           class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] text-white font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.122 1.523 5.857L.057 23.492a.5.5 0 0 0 .604.634l5.822-1.527A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.833 9.833 0 0 1-5.028-1.377l-.36-.214-3.733.979 1.002-3.644-.235-.374A9.818 9.818 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
            WhatsApp
        </a>
        <button @click="copy()"
                class="flex items-center justify-center gap-2 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
            <span x-show="!copied">Copiar</span>
            <span x-show="copied" x-cloak>Copiado</span>
        </button>
        <button x-show="canShare" x-cloak @click="nativeShare()"
                class="flex items-center justify-center border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 py-3 px-3 rounded-xl transition-transform active:scale-95">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('bolhas', { mostrarShare: false })

    Alpine.data('bolhas', () => ({
        total: 96,
        rebentadas: [],
        contador: 0,
        mensagem: '',
        gridKey: 0,

        get todasRebentadas() {
            return this.rebentadas.length === this.total;
        },

        rebentar(idx) {
            if (this.rebentadas.includes(idx)) return;
            this.rebentadas.push(idx);
            this.contador++;

            this.actualizarMensagem();

            if (this.contador >= 10) {
                Alpine.store('bolhas').mostrarShare = true;
            }

            if (this.todasRebentadas) {
                setTimeout(() => {
                    this.rebentadas = [];
                    this.gridKey++;
                }, 600);
            }
        },

        actualizarMensagem() {
            const msgs = {
                10:  'Isto não vai a lado nenhum. E mesmo assim...',
                50:  'Nenhum problema teu foi resolvido.',
                100: 'Parabéns. Rebentaste 100 bolhas. A tua vida é igual.',
                200: 'Estás bem?',
                500: 'Procura ajuda.',
            };
            if (msgs[this.contador]) {
                this.mensagem = msgs[this.contador];
            }
        },

        get shareText() {
            return `Já rebentei ${this.contador} bolhas neste site completamente inútil e não consigo parar 🫧`;
        }
    }))
})
</script>
@endpush
