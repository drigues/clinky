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
  "name": "Nomeador de Grupos",
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
<div class="min-h-screen px-4 pb-32" x-data="nomeador()">

    <x-site-header
        emoji="💬"
        title="Nomeador de Grupos"
        tagline="Nomes épicos para o teu WhatsApp"
        accentColor="pink"
    />

    <div class="max-w-sm mx-auto space-y-4">
        <div>
            <label for="categoria" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Tipo de grupo
            </label>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="cat in categorias" :key="cat.id">
                    <button type="button"
                            @click="categoria = cat.id"
                            :class="categoria === cat.id
                                ? 'bg-pink-500 text-white border-pink-500'
                                : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700'"
                            class="text-xs py-2.5 px-2 rounded-lg font-medium transition-colors border text-center"
                            x-text="cat.label">
                    </button>
                </template>
            </div>
        </div>

        <button @click="gerar"
                :disabled="loading"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95 disabled:opacity-50">
            <span x-show="!nomes.length && !loading">Gerar nomes</span>
            <span x-show="nomes.length && !loading">Mais 3 nomes</span>
            <span x-show="loading" x-cloak>A inventar...</span>
        </button>
    </div>

    <div x-show="nomes.length" x-cloak class="max-w-sm mx-auto mt-6 space-y-3">
        <template x-for="(nome, i) in nomes" :key="i">
            <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="nome"></span>
                <button @click="copiar(i)"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg transition-colors"
                        :class="copiado === i
                            ? 'bg-pink-500 text-white'
                            : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600'">
                    <span x-show="copiado !== i">Copiar</span>
                    <span x-show="copiado === i">Copiado</span>
                </button>
            </div>
        </template>
    </div>

</div>

<div x-data="{
        shareUrl: 'https://nomeador.{{ config('app.base_domain') }}',
        shareText: 'Encontrei o nome perfeito para o grupo!\n\n💬 Descobre o teu em:',
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            const t = document.querySelector('meta[name=csrf-token]')
            if (!t) return
            fetch('/api/track', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t.content }, body: JSON.stringify({ event: 'share_' + platform }) }).catch(() => {})
        },
        async nativeShare() { try { await navigator.share({ text: this.shareText, url: this.shareUrl, title: document.title }); this.track('native') } catch(e) {} },
        copy() { navigator.clipboard.writeText(this.shareText + '\n' + this.shareUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) }); this.track('copy') },
        get whatsappUrl() { return 'https://wa.me/?text=' + encodeURIComponent(this.shareText + '\n' + this.shareUrl) }
     }"
     x-show="$store.nomeador.gerado" x-cloak
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
    Alpine.store('nomeador', { gerado: false })

    Alpine.data('nomeador', () => ({
        categoria: 'familia',
        nomes: [],
        loading: false,
        copiado: null,
        categorias: [
            { id: 'familia', label: 'Família' },
            { id: 'trabalho', label: 'Trabalho' },
            { id: 'amigos', label: 'Amigos' },
            { id: 'casal', label: 'Casal' },
            { id: 'vizinhos', label: 'Vizinhos' },
            { id: 'escola', label: 'Escola' },
        ],
        async gerar() {
            this.loading = true
            this.copiado = null
            try {
                const res = await fetch('/gerar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ categoria: this.categoria })
                })
                const data = await res.json()
                this.nomes = data.nomes
                Alpine.store('nomeador').gerado = true
            } catch(e) {}
            this.loading = false
        },
        copiar(index) {
            navigator.clipboard.writeText(this.nomes[index]).then(() => {
                this.copiado = index
                setTimeout(() => this.copiado = null, 2000)
            })
        }
    }))
})
</script>
@endpush
