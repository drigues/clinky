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
  "name": "O Oráculo",
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
<div class="min-h-screen px-4 pb-32" x-data="oraculo()">

    <x-hero
        emoji="👁️"
        title="O Oráculo"
        tagline="Pergunta. A resposta já existe."
        accent="#581C87"
        eyebrow="BARNUM · 15" />

    {{-- Formulário --}}
    <div x-show="!resposta && !loading" class="max-w-sm mx-auto">
        <form @submit.prevent="consultar" class="space-y-4">
            <div>
                <label for="pergunta" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Faz a tua pergunta ao Oráculo
                </label>
                <textarea id="pergunta"
                          x-model="pergunta"
                          rows="3"
                          maxlength="200"
                          placeholder="O que queres saber?"
                          class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none"></textarea>
                <p class="text-right text-xs text-zinc-400 mt-1"><span x-text="pergunta.length"></span>/200</p>
            </div>

            <button type="submit"
                    :disabled="!pergunta.trim() || loading"
                    class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95 disabled:opacity-50">
                Consultar o Oráculo
            </button>
        </form>
    </div>

    {{-- Loading dramático --}}
    <div x-show="loading" x-cloak class="max-w-sm mx-auto mt-8">
        <div class="flex flex-col items-center gap-6 py-12">
            <div class="relative">
                <div class="w-20 h-20 rounded-full bg-purple-500/20 animate-ping absolute inset-0"></div>
                <div class="w-20 h-20 rounded-full bg-purple-500/10 flex items-center justify-center relative">
                    <span class="text-4xl animate-pulse">👁️</span>
                </div>
            </div>
            <p class="text-sm text-zinc-500 dark:text-white/70 animate-pulse">O Oráculo está a ver...</p>
        </div>
    </div>

    {{-- Erro --}}
    <div x-show="erro" x-cloak class="max-w-sm mx-auto mt-4">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-300" x-text="erro"></div>
    </div>

    {{-- Resultado --}}
    <div x-show="resposta" x-cloak class="max-w-sm mx-auto mt-8 space-y-4">

        {{-- Pergunta feita --}}
        <div class="text-center">
            <span class="inline-block text-xs font-medium text-purple-500 dark:text-purple-400 uppercase tracking-wider mb-2">Perguntaste</span>
            <p class="text-sm text-zinc-500 dark:text-white/70 italic" x-text="'\"' + perguntaFeita + '\"'"></p>
        </div>

        {{-- Resposta do Oráculo --}}
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6">
            <div class="text-3xl mb-3">👁️</div>
            <p class="text-lg font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed italic" x-text="resposta"></p>
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xs text-zinc-400 hover:text-zinc-500">clinky.cc</a>
                <span class="text-xs text-zinc-400 dark:text-white/60">O Oráculo falou.</span>
            </div>
        </div>

        {{-- Nova pergunta --}}
        <button @click="nova()"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95">
            Fazer outra pergunta
        </button>
    </div>

</div>

{{-- Share bar --}}
<div x-data="{
        get shareText() { return Alpine.store('oraculo').shareText },
        shareUrl: '{{ route('oraculo.index') }}',
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
     x-show="$store.oraculo.resposta"
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
    Alpine.store('oraculo', { resposta: '', shareText: '' })

    Alpine.data('oraculo', () => ({
        pergunta: '',
        perguntaFeita: '',
        resposta: '',
        loading: false,
        erro: '',
        async consultar() {
            if (!this.pergunta.trim()) return
            this.perguntaFeita = this.pergunta.trim()
            this.loading = true
            this.resposta = ''
            this.erro = ''
            try {
                const res = await fetch('{{ route('oraculo.consultar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ pergunta: this.perguntaFeita })
                })
                if (res.status === 429) {
                    this.erro = 'O Oráculo precisa de descansar. Tenta novamente em breve.'
                    return
                }
                const data = await res.json()
                this.resposta = data.resposta
                Alpine.store('oraculo').resposta = data.resposta
                Alpine.store('oraculo').shareText = 'Perguntei ao Oráculo: "' + this.perguntaFeita + '"\n\nRespondeu: "' + data.resposta + '"\n\n👁️ Pergunta ao Oráculo:'
            } catch(e) {
                this.erro = 'O Oráculo está em silêncio. Verifica a tua ligação e tenta novamente.'
            } finally {
                this.loading = false
            }
        },
        nova() {
            this.pergunta = ''
            this.perguntaFeita = ''
            this.resposta = ''
            this.erro = ''
            Alpine.store('oraculo').resposta = ''
            Alpine.store('oraculo').shareText = ''
        }
    }))
})
</script>
@endpush
