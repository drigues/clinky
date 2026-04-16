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
  "name": "Conversor PT ↔ BR",
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
<div class="min-h-screen px-4 pb-32" x-data="conversor()">

    <x-site-header
        emoji="🇵🇹🇧🇷"
        title="Conversor PT ↔ BR"
        tagline="As palavras que nos separam (e unem)"
        accentColor="blue"
    />

    <div class="max-w-md mx-auto mb-6">
        <div class="relative">
            <input type="search"
                   x-model="pesquisa"
                   placeholder="Pesquisa uma palavra..."
                   autocomplete="off"
                   autocorrect="off"
                   class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
    </div>

    <div class="max-w-md mx-auto mb-4 flex items-center justify-between">
        <button @click="modo = modo === 'pt' ? 'br' : 'pt'"
                class="flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
            <span x-show="modo === 'pt'">🇵🇹 PT → BR 🇧🇷</span>
            <span x-show="modo === 'br'" x-cloak>🇧🇷 BR → PT 🇵🇹</span>
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
        </button>
        <span class="text-xs text-zinc-400" x-text="filtrados.length + ' palavras'"></span>
    </div>

    <div class="max-w-md mx-auto mb-6 text-center" x-show="palavraDoDia" x-cloak>
        <div class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-full px-4 py-2 text-sm">
            <span>💡</span>
            <span class="text-blue-700 dark:text-blue-300">
                Palavra do dia: <strong x-text="palavraDoDia.pt"></strong> 🇵🇹 = <strong x-text="palavraDoDia.br"></strong> 🇧🇷
            </span>
        </div>
    </div>

    <div class="max-w-md mx-auto space-y-3">
        <template x-for="(item, idx) in filtrados" :key="idx">
            <button @click="seleccionado = seleccionado === idx ? null : idx"
                    class="w-full text-left bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 transition-colors hover:border-blue-300 dark:hover:border-blue-600">
                <div class="flex items-center gap-3">
                    <span class="text-2xl" x-text="item.emoji"></span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100" x-text="modo === 'pt' ? item.pt : item.br"></span>
                            <span class="text-zinc-400">→</span>
                            <span class="text-blue-600 dark:text-blue-400 font-medium" x-text="modo === 'pt' ? item.br : item.pt"></span>
                        </div>
                    </div>
                </div>
                <div x-show="seleccionado === idx" x-cloak class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-500 dark:text-zinc-400 space-y-1">
                    <template x-if="item.exemplo_pt">
                        <p>🇵🇹 <span x-text="item.exemplo_pt"></span></p>
                    </template>
                    <template x-if="item.exemplo_br">
                        <p>🇧🇷 <span x-text="item.exemplo_br"></span></p>
                    </template>
                    <template x-if="item.nota">
                        <p>💡 <span x-text="item.nota"></span></p>
                    </template>
                </div>
            </button>
        </template>
    </div>

    <div x-show="filtrados.length === 0" x-cloak class="max-w-md mx-auto mt-8 text-center">
        <p class="text-zinc-400 text-sm">Sem resultados para "<span x-text="pesquisa"></span>"</p>
        <button @click="pesquisa = ''" class="mt-2 text-blue-500 text-sm hover:underline">Limpar pesquisa</button>
    </div>

</div>

<div x-data="{
        get shareText() {
            const s = Alpine.store('conversor');
            if (s.palavra) return 'Sabias que «' + s.palavra.pt + '» em PT = «' + s.palavra.br + '» em BR? 🇵🇹🇧🇷';
            return 'Descobre as diferenças entre português de Portugal e do Brasil 🇵🇹🇧🇷';
        },
        shareUrl: '{{ route('conversor.index') }}',
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
     class="fixed bottom-0 left-0 right-0 p-4 bg-white/90 dark:bg-zinc-950/90 backdrop-blur border-t border-zinc-200 dark:border-zinc-800">
    <div class="flex items-center gap-2 max-w-sm mx-auto">
        <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer" @click="track('whatsapp')"
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
    Alpine.store('conversor', { palavra: null })

    Alpine.data('conversor', () => ({
        dicionario: @json($dicionario),
        pesquisa: '',
        modo: 'pt',
        seleccionado: null,
        get palavraDoDia() {
            const day = new Date().getDate();
            return this.dicionario[day % this.dicionario.length];
        },
        get filtrados() {
            if (!this.pesquisa) return this.dicionario;
            const q = this.pesquisa.toLowerCase();
            return this.dicionario.filter(e =>
                e.pt.toLowerCase().includes(q) || e.br.toLowerCase().includes(q)
            );
        },
        init() {
            this.$watch('seleccionado', (val) => {
                if (val !== null) {
                    Alpine.store('conversor').palavra = this.filtrados[val];
                }
            });
        }
    }))
})
</script>
@endpush
