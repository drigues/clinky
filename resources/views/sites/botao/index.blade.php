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
  "name": "Aperta o Botão",
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
<div class="min-h-screen flex flex-col items-center justify-center px-4 pb-32" x-data="botao()">

    <header class="text-center mb-8">
        <p class="text-sm text-zinc-500 dark:text-white/70 mb-2">Já apertaram</p>
        <div class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 tabular-nums"
             x-text="total.toLocaleString('pt-PT')">
            {{ number_format($total, 0, ',', '.') }}
        </div>
        <p class="text-sm text-zinc-500 dark:text-white/70 mt-1">vezes</p>
    </header>

    <button x-ref="botao"
            @click="pressionar"
            :disabled="loading"
            class="w-48 h-48 rounded-full bg-red-600 hover:bg-red-500 active:bg-red-700
                   shadow-[0_8px_0_0_#991b1b] active:shadow-[0_2px_0_0_#991b1b] active:translate-y-1.5
                   transition-all duration-100 cursor-pointer disabled:opacity-70
                   flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-red-500/30"
            aria-label="Apertar o botão">
        <span class="text-white text-lg font-bold select-none" x-show="!loading">APERTA</span>
        <span class="text-white text-lg font-bold select-none" x-show="loading" x-cloak>...</span>
    </button>

    <div class="mt-8 h-8 text-center">
        <p x-show="mensagem" x-cloak
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-2"
           x-transition:enter-end="opacity-100 translate-y-0"
           class="text-sm text-zinc-500 dark:text-white/70 italic"
           x-text="mensagem">
        </p>
    </div>

    <div x-show="pressionado" x-cloak class="mt-4">
        <p class="text-xs text-zinc-400 dark:text-white/60">
            Tu: <span x-text="meuTotal" class="font-semibold text-zinc-600 dark:text-zinc-300"></span> vezes
        </p>
    </div>

</div>

<div x-data="{
        shareUrl: '{{ route('botao.index') }}',
        shareText: 'Já apertei o botão. Quantas vezes vais aguentar sem apertar?\n\n🔴',
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
     x-show="$store.botao.pressionado" x-cloak
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
    Alpine.store('botao', { pressionado: false })

    Alpine.data('botao', () => ({
        total: {{ $total }},
        loading: false,
        pressionado: false,
        meuTotal: 0,
        mensagem: '',
        mensagens: [
            'Porquê? Só porque sim.',
            'Já não podes parar.',
            'O botão agradece.',
            'Mais uma vez?',
            'Definitivamente não era necessário.',
            'E agora? Vai lá apertar outra vez.',
            'Ninguém te obrigou.',
            'O botão sente-se amado.',
            'Outra vez? A sério?',
            'Não tens mais nada para fazer?',
        ],
        pollInterval: null,
        init() {
            this.pollInterval = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    fetch('{{ route('botao.total') }}').then(r => r.json()).then(d => { this.total = d.total }).catch(() => {})
                }
            }, 10000)
        },
        destroy() {
            if (this.pollInterval) clearInterval(this.pollInterval)
        },
        async pressionar() {
            this.loading = true
            try {
                const res = await fetch('{{ route('botao.pressionar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    }
                })
                if (res.ok) {
                    const data = await res.json()
                    this.total = data.total
                    this.meuTotal++
                    this.pressionado = true
                    Alpine.store('botao').pressionado = true
                    this.mensagem = this.mensagens[Math.floor(Math.random() * this.mensagens.length)]
                }
            } catch(e) {}
            this.loading = false
        }
    }))
})
</script>
@endpush
