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
  "name": "Sou mais BR ou PT?",
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
<div class="min-h-screen px-4 pb-32" x-data="quiz()">

    {{-- Splash --}}
    <div x-show="estado === 'splash'" class="flex flex-col items-center justify-center min-h-[80vh]">
        <x-site-header
            emoji="🤔"
            title="Sou mais BR ou PT?"
            tagline="5 perguntas para descobrir o teu nível de sotaque"
            accentColor="orange"
        />
        <button @click="estado = 'pergunta'"
                class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 px-8 rounded-xl transition-transform active:scale-95">
            Começar quiz
        </button>
    </div>

    {{-- Perguntas --}}
    <div x-show="estado === 'pergunta'" x-cloak class="max-w-md mx-auto pt-12">
        <div class="mb-6">
            <div class="flex items-center justify-between text-xs text-zinc-400 mb-2">
                <span x-text="(actual + 1) + ' de ' + perguntas.length"></span>
                <span x-text="Math.round(((actual + 1) / perguntas.length) * 100) + '%'"></span>
            </div>
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2">
                <div class="bg-orange-500 h-2 rounded-full transition-all duration-300"
                     :style="'width: ' + ((actual + 1) / perguntas.length * 100) + '%'"></div>
            </div>
        </div>

        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-6" x-text="perguntas[actual].pergunta"></h2>

        <div class="space-y-3">
            <template x-for="(opcao, idx) in perguntas[actual].opcoes" :key="idx">
                <button @click="responder(opcao)"
                        class="w-full text-left bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 text-sm font-medium text-zinc-700 dark:text-zinc-300 transition-colors hover:border-orange-400 dark:hover:border-orange-500 active:scale-[0.98]"
                        x-text="opcao.texto">
                </button>
            </template>
        </div>
    </div>

    {{-- Resultado --}}
    <div x-show="estado === 'resultado'" x-cloak class="max-w-md mx-auto pt-12 text-center">
        <div class="text-6xl mb-4" x-text="resultadoEmoji"></div>
        <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2" x-text="resultadoTitulo"></h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6" x-text="resultadoDescricao"></p>

        <div class="flex items-center gap-4 justify-center mb-8">
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-500" x-text="percPT + '%'"></div>
                <div class="text-xs text-zinc-400 mt-1">🇵🇹 Português</div>
            </div>
            <div class="w-px h-12 bg-zinc-200 dark:bg-zinc-700"></div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-500" x-text="percBR + '%'"></div>
                <div class="text-xs text-zinc-400 mt-1">🇧🇷 Brasileiro</div>
            </div>
        </div>

        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-4 mb-8 overflow-hidden flex">
            <div class="bg-orange-500 h-4 transition-all duration-500" :style="'width: ' + percPT + '%'"></div>
            <div class="bg-green-500 h-4 transition-all duration-500" :style="'width: ' + percBR + '%'"></div>
        </div>

        <button @click="recomecar()"
                class="text-sm text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
            Tentar novamente
        </button>
    </div>

</div>

<div x-data="{
        get shareText() {
            const s = Alpine.store('quiz');
            if (!s.resultado) return '';
            return 'Fiz o teste e sou ' + s.percPT + '% português e ' + s.percBR + '% brasileiro! 🇵🇹🇧🇷';
        },
        shareUrl: 'https://quiz.{{ config('app.base_domain') }}',
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
     x-show="$store.quiz.resultado" x-cloak
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
    Alpine.store('quiz', { resultado: false, percPT: 0, percBR: 0 })

    Alpine.data('quiz', () => ({
        perguntas: @json($perguntas),
        estado: 'splash',
        actual: 0,
        totalPT: 0,
        totalBR: 0,
        get percPT() {
            const total = this.totalPT + this.totalBR;
            if (!total) return 50;
            return Math.round((this.totalPT / total) * 100);
        },
        get percBR() { return 100 - this.percPT; },
        get resultadoEmoji() {
            if (this.percPT >= 80) return '🇵🇹';
            if (this.percBR >= 80) return '🇧🇷';
            return '🎉';
        },
        get resultadoTitulo() {
            if (this.percPT >= 80) return 'És praticamente tuga';
            if (this.percBR >= 80) return 'O fado ainda não te convenceu';
            return 'Imigrante integrado perfeito';
        },
        get resultadoDescricao() {
            if (this.percPT >= 80) return 'Já pedes café sem açúcar e achas normal. Parabéns, és dos nossos.';
            if (this.percBR >= 80) return 'Continuas a mandar áudio de 3 minutos e a chegar tarde. Nunca mudes.';
            return 'Nem cá nem lá — absorveste o melhor dos dois mundos.';
        },
        responder(opcao) {
            this.totalPT += opcao.pt;
            this.totalBR += opcao.br;
            if (this.actual < this.perguntas.length - 1) {
                this.actual++;
            } else {
                this.estado = 'resultado';
                Alpine.store('quiz').resultado = true;
                Alpine.store('quiz').percPT = this.percPT;
                Alpine.store('quiz').percBR = this.percBR;
            }
        },
        recomecar() {
            this.actual = 0;
            this.totalPT = 0;
            this.totalBR = 0;
            this.estado = 'splash';
            Alpine.store('quiz').resultado = false;
        }
    }))
})
</script>
@endpush
