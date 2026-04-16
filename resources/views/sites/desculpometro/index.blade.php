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
  "name": "Desculpómetro",
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
<div class="min-h-screen px-4 pb-32" x-data="desculpometro()">

    <x-site-header
        emoji="😅"
        title="Desculpómetro"
        tagline="Gera a desculpa perfeita em 1 segundo"
        accentColor="orange"
    />

    <div class="text-center mb-8">
        <x-counter-badge :count="$totalGeradas" label="desculpas geradas" />
    </div>

    <form @submit.prevent="gerar" class="max-w-sm mx-auto space-y-4">
        <div>
            <label for="situacao" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Qual é a situação?
            </label>
            <select id="situacao" x-model="situacao"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50">
                <option value="trabalho">Faltar ao trabalho</option>
                <option value="ginasio">Faltar ao ginásio</option>
                <option value="familia">Evitar a família</option>
                <option value="encontro">Cancelar um encontro</option>
                <option value="reuniao">Sair de uma reunião</option>
                <option value="aula">Faltar à aula</option>
                <option value="consulta">Cancelar consulta</option>
                <option value="outro">Outra situação</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Grau de absurdo
            </label>
            <div class="grid grid-cols-4 gap-2">
                <template x-for="(grau, i) in graus" :key="i">
                    <button type="button"
                            @click="absurdo = i"
                            :class="absurdo === i
                                ? 'bg-orange-500 text-white border-orange-500'
                                : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700'"
                            class="text-xs py-2 px-1 rounded-lg font-medium transition-colors border"
                            x-text="grau">
                    </button>
                </template>
            </div>
        </div>

        <button type="submit"
                :disabled="loading"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95 disabled:opacity-50">
            <span x-show="!loading">Gerar desculpa</span>
            <span x-show="loading" x-cloak>A criar magia...</span>
        </button>
    </form>

    <div x-show="resultado" x-cloak class="max-w-sm mx-auto">
        <div class="relative bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 my-6">
            <div class="text-3xl mb-3">😅</div>
            <p class="text-lg font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed" x-text="resultado"></p>
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <a href="https://{{ config('app.base_domain') }}" class="text-xs text-zinc-400 hover:text-zinc-500">clinky.cc</a>
            </div>
        </div>
    </div>

    <div x-show="loading && !resultado" x-cloak class="max-w-sm mx-auto">
        <div class="relative bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 my-6">
            <div class="flex items-center gap-3 text-zinc-500">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
                <span class="text-sm">A gerar...</span>
            </div>
        </div>
    </div>

    <div x-show="erro" x-cloak class="max-w-sm mx-auto">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 my-4 text-sm text-red-700 dark:text-red-300" x-text="erro"></div>
    </div>

</div>

<div x-data="{
        get shareText() { return Alpine.store('desculpometro').resultado + '\n\n😅 Gera a tua em: https://desculpometro.{{ config('app.base_domain') }}' },
        shareUrl: 'https://desculpometro.{{ config('app.base_domain') }}',
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            const t = document.querySelector('meta[name=csrf-token]')
            if (!t) return
            fetch('/api/track', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t.content }, body: JSON.stringify({ event: 'share_' + platform }) }).catch(() => {})
        },
        async nativeShare() {
            try { await navigator.share({ text: this.shareText, url: this.shareUrl, title: document.title }); this.track('native') } catch(e) {}
        },
        copy() {
            navigator.clipboard.writeText(this.shareText + '\n' + this.shareUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) })
            this.track('copy')
        },
        get whatsappUrl() { return 'https://wa.me/?text=' + encodeURIComponent(this.shareText + '\n' + this.shareUrl) }
     }"
     x-show="$store.desculpometro.resultado" x-cloak
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
    Alpine.store('desculpometro', { resultado: '' })

    Alpine.data('desculpometro', () => ({
        situacao: 'trabalho',
        absurdo: 1,
        resultado: '',
        loading: false,
        erro: '',
        graus: ['Normal 😐', 'Criativo 😏', 'Épico 🤌', 'Absurdo 🤯'],
        async gerar() {
            this.loading = true
            this.resultado = ''
            this.erro = ''
            try {
                const res = await fetch('/gerar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ situacao: this.situacao, absurdo: this.absurdo })
                })
                if (res.status === 429) {
                    this.erro = 'Demasiadas tentativas. Aguarda um momento e tenta novamente.'
                    return
                }
                const data = await res.json()
                this.resultado = data.desculpa
                Alpine.store('desculpometro').resultado = data.desculpa
            } catch(e) {
                this.erro = 'Não foi possível gerar a desculpa. Verifica a tua ligação e tenta novamente.'
            } finally {
                this.loading = false
            }
        }
    }))

    Alpine.data('desculpometroShare', () => ({
        get shareText() {
            const r = Alpine.store('desculpometro').resultado
            return r ? r + '\n\n😅 Gera a tua em:' : ''
        }
    }))
})
</script>
@endpush
