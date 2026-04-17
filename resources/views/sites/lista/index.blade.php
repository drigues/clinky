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
  "name": "Coisas Que Nunca Vais Fazer",
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
<div class="min-h-screen px-4 pb-32" x-data="lista()">

    <x-hero
        emoji="✅"
        title="Coisas Que Nunca Vais Fazer"
        tagline="A lista honesta"
        accent="#DB2777"
        eyebrow="IDENTIFICAÇÃO · 18" />

    {{-- Contador --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-baseline gap-2">
            <span x-text="marcados" class="text-6xl md:text-8xl font-black text-orange-500 tabular-nums"></span>
            <span class="text-2xl md:text-3xl text-white/40 font-bold">/ {{ $total }}</span>
        </div>
        <p class="mt-2 text-white/60 font-medium" x-text="mensagem"></p>
    </div>

    {{-- Lista --}}
    <ul class="mx-auto max-w-2xl space-y-2">
        <template x-for="(item, i) in itens" :key="i">
            <li>
                <label class="group flex items-center gap-4 p-4 md:p-5 rounded-xl
                              bg-zinc-900/50 hover:bg-zinc-900
                              border border-zinc-800 hover:border-orange-500/40
                              cursor-pointer transition-all select-none">
                    <input type="checkbox"
                           :checked="checked[i]"
                           @change="toggle(i)"
                           class="w-5 h-5 rounded accent-orange-500 cursor-pointer">
                    <span :class="checked[i] ? 'line-through text-white/30' : 'text-white'"
                          class="text-base md:text-lg font-semibold transition-colors"
                          x-text="item"></span>
                </label>
            </li>
        </template>
    </ul>

</div>

{{-- Share bar --}}
<div x-data="{
        get shareText() { return Alpine.store('lista').shareText },
        shareUrl: '{{ route('lista.index') }}',
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            const t = document.querySelector('meta[name=csrf-token]');
            if (!t) return;
            fetch('/api/track', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t.content }, body: JSON.stringify({ event: 'share_' + platform }) }).catch(() => {});
        },
        async nativeShare() { try { await navigator.share({ text: this.shareText + '\n' + this.shareUrl, url: this.shareUrl, title: document.title }); this.track('native') } catch(e) {} },
        copy() { navigator.clipboard.writeText(this.shareText + '\n' + this.shareUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) }); this.track('copy') },
        get whatsappUrl() { return 'https://wa.me/?text=' + encodeURIComponent(this.shareText + '\n→ ' + this.shareUrl) }
     }"
     x-show="$store.lista.marcados > 0"
     x-cloak
     class="fixed bottom-0 left-0 right-0 p-4 bg-white/90 dark:bg-zinc-950/90 backdrop-blur border-t border-zinc-200 dark:border-zinc-800">
    <div class="flex items-center gap-2 max-w-sm mx-auto">
        <a :href="whatsappUrl" target="_blank" rel="noopener" @click="track('whatsapp')"
           class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] text-white font-medium text-sm py-3 px-4 rounded-xl transition-transform active:scale-95">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.122 1.523 5.857L.057 23.492a.5.5 0 0 0 .604.634l5.822-1.527A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.833 9.833 0 0 1-5.028-1.377l-.36-.214-3.733.979 1.002-3.644-.235-.374A9.818 9.818 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
            Partilhar
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
    Alpine.store('lista', { marcados: 0, shareText: '' })

    Alpine.data('lista', () => ({
        itens: {!! $itens_json !!},
        checked: {},
        get marcados() {
            return Object.values(this.checked).filter(Boolean).length
        },
        get mensagem() {
            const n = this.marcados
            if (n === 0) return 'Vai, sê honesto.'
            if (n < 5) return 'Ainda tens esperança.'
            if (n < 10) return 'Hm. Interessante.'
            if (n < 15) return 'Está a ficar pessoal.'
            if (n < 20) return 'Agora estamos a falar.'
            if (n < 25) return 'Isto já é terapia.'
            if (n < 30) return 'A honestidade é brutal.'
            return 'Parabéns. A tua honestidade é refrescante.'
        },
        init() {
            const saved = localStorage.getItem('clinky_lista')
            if (saved) {
                try { this.checked = JSON.parse(saved) } catch(e) {}
            }
            this.$watch('marcados', (n) => {
                Alpine.store('lista').marcados = n
                Alpine.store('lista').shareText = '😅 Marquei ' + n + '/{{ $total }} coisas que nunca vou fazer.\n\n' + this.mensagem + '\n\nE tu, quantas marcas?'
            })
            // Trigger initial store update
            Alpine.store('lista').marcados = this.marcados
        },
        toggle(i) {
            this.checked[i] = !this.checked[i]
            localStorage.setItem('clinky_lista', JSON.stringify(this.checked))
        }
    }))
})
</script>
@endpush
