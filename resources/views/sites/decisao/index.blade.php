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
  "name": "A Decisão Impossível",
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
<div class="min-h-screen px-4 pb-32" x-data="decisao()">

    <x-hero
        emoji="🤯"
        title="A Decisão Impossível"
        tagline="Duas opções. Nenhuma é boa."
        accent="#4F46E5"
        eyebrow="PARALISIA · 14" />

    {{-- Dilema — ecrã dividido --}}
    <div x-show="!analise && !loading" class="relative mx-auto max-w-4xl">

        <p class="text-center text-xs md:text-sm font-bold uppercase tracking-[0.4em] text-indigo-400/60 mb-10">
            Escolhe um lado
        </p>

        <div class="relative grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 min-h-[320px] md:min-h-[420px]">

            {{-- Opção A --}}
            <button @click="escolher('a')"
                    class="group relative flex items-center justify-center p-8 md:p-12 rounded-3xl
                           bg-gradient-to-br from-red-950/40 via-zinc-900 to-zinc-950
                           border-2 border-red-900/40
                           hover:border-red-500 hover:from-red-900/60
                           active:scale-[0.98] transition-all duration-300
                           text-center overflow-hidden">
                <div class="absolute inset-0 bg-red-500/0 group-hover:bg-red-500/5 transition-colors rounded-3xl"></div>
                <span class="relative text-2xl md:text-4xl font-black text-white leading-tight tracking-tight"
                      x-text="dilema.a"></span>
            </button>

            {{-- Opção B --}}
            <button @click="escolher('b')"
                    class="group relative flex items-center justify-center p-8 md:p-12 rounded-3xl
                           bg-gradient-to-br from-blue-950/40 via-zinc-900 to-zinc-950
                           border-2 border-blue-900/40
                           hover:border-blue-500 hover:from-blue-900/60
                           active:scale-[0.98] transition-all duration-300
                           text-center overflow-hidden">
                <div class="absolute inset-0 bg-blue-500/0 group-hover:bg-blue-500/5 transition-colors rounded-3xl"></div>
                <span class="relative text-2xl md:text-4xl font-black text-white leading-tight tracking-tight"
                      x-text="dilema.b"></span>
            </button>

            {{-- VS central --}}
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                        w-14 h-14 md:w-20 md:h-20 rounded-full
                        bg-black border-2 border-white/20
                        flex items-center justify-center
                        text-white font-black text-sm md:text-lg tracking-wider
                        shadow-[0_0_40px_rgba(168,85,247,0.4)]
                        z-10 pointer-events-none">
                VS
            </div>

        </div>

        <p class="mt-10 text-center text-white/40 text-sm italic">
            Não há resposta certa. Mas há uma análise.
        </p>
    </div>

    {{-- Loading --}}
    <div x-show="loading" x-cloak class="max-w-sm mx-auto mt-8">
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6">
            <div class="flex items-center gap-3 text-zinc-500">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
                <span class="text-sm">A analisar a tua escolha...</span>
            </div>
        </div>
    </div>

    {{-- Erro --}}
    <div x-show="erro" x-cloak class="max-w-sm mx-auto mt-4">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-300" x-text="erro"></div>
    </div>

    {{-- Resultado --}}
    <div x-show="analise" x-cloak class="max-w-sm mx-auto mt-8 space-y-4">

        {{-- Escolha feita --}}
        <div class="text-center">
            <span class="inline-block text-xs font-medium text-indigo-500 dark:text-indigo-400 uppercase tracking-wider mb-2">Escolheste</span>
            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100 leading-snug" x-text="escolhaTexto"></p>
        </div>

        {{-- Análise --}}
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6">
            <div class="text-3xl mb-3">🧠</div>
            <p class="text-lg font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed" x-text="analise"></p>
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xs text-zinc-400 hover:text-zinc-500">clinky.cc</a>
                <span class="text-xs text-zinc-400 dark:text-white/60">
                    <span x-text="percentagem"></span>% escolheu o mesmo
                </span>
            </div>
        </div>

        {{-- Próximo dilema --}}
        <button @click="proximo()"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95">
            Próxima decisão impossível
        </button>
    </div>

</div>

{{-- Share bar --}}
<div x-data="{
        get shareText() { return Alpine.store('decisao').shareText },
        shareUrl: '{{ route('decisao.index') }}',
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
     x-show="$store.decisao.analise"
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
    Alpine.store('decisao', { analise: '', shareText: '' })

    Alpine.data('decisao', () => ({
        dilemas: {!! $dilemas_json !!},
        dilema: @json($dilema),
        analise: '',
        escolhaTexto: '',
        percentagem: 0,
        loading: false,
        erro: '',
        async escolher(opcao) {
            const texto = opcao === 'a' ? this.dilema.a : this.dilema.b
            this.escolhaTexto = texto
            this.loading = true
            this.analise = ''
            this.erro = ''
            try {
                const res = await fetch('{{ route('decisao.escolher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ escolha: texto, opcao })
                })
                if (res.status === 429) {
                    this.erro = 'Demasiadas tentativas. Aguarda um momento e tenta novamente.'
                    return
                }
                const data = await res.json()
                this.analise = data.analise
                this.percentagem = data.percentagem
                Alpine.store('decisao').analise = data.analise
                Alpine.store('decisao').shareText = 'Escolhi "' + texto + '" e a análise disse: "' + data.analise.substring(0, 80) + '..."\n\n🤯 E tu, o que escolhias?'
            } catch(e) {
                this.erro = 'Não foi possível analisar. Verifica a tua ligação e tenta novamente.'
            } finally {
                this.loading = false
            }
        },
        proximo() {
            let novo
            do {
                novo = this.dilemas[Math.floor(Math.random() * this.dilemas.length)]
            } while (novo.id === this.dilema.id && this.dilemas.length > 1)
            this.dilema = novo
            this.analise = ''
            this.escolhaTexto = ''
            this.percentagem = 0
            this.erro = ''
            Alpine.store('decisao').analise = ''
            Alpine.store('decisao').shareText = ''
        }
    }))
})
</script>
@endpush
