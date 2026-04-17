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
  "name": "Modo Pânico",
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
<div class="min-h-screen px-4 pb-32" x-data="panico()">

    <x-hero
        emoji="🚨"
        title="Modo Pânico"
        tagline="Activa a crise para qualquer situação"
        accent="#DC2626"
        eyebrow="URGÊNCIA · 16" />

    {{-- Formulário --}}
    <div x-show="!resultado && !loading" class="max-w-sm mx-auto">
        <form @submit.prevent="activar" class="space-y-4">
            <div>
                <label for="situacao" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Descreve a situação
                </label>
                <input id="situacao"
                       type="text"
                       x-model="situacao"
                       maxlength="200"
                       placeholder="Tenho uma reunião amanhã"
                       class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-500/50">
            </div>

            <button type="submit"
                    :disabled="!situacao.trim() || loading"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition-all active:scale-95 disabled:opacity-50 uppercase tracking-wider text-sm">
                Activar modo pânico
            </button>
        </form>
    </div>

    {{-- Loading com flash vermelho --}}
    <div x-show="loading" x-cloak class="max-w-sm mx-auto mt-8">
        <div class="flex flex-col items-center gap-4 py-12">
            <div class="relative">
                <div class="w-16 h-16 rounded-full bg-red-500/30 animate-ping absolute inset-0"></div>
                <div class="w-16 h-16 rounded-full bg-red-500/20 flex items-center justify-center relative">
                    <span class="text-3xl animate-pulse">🚨</span>
                </div>
            </div>
            <p class="text-sm text-red-400 font-mono uppercase tracking-wider animate-pulse">A analisar ameaça...</p>
        </div>
    </div>

    {{-- Erro --}}
    <div x-show="erro" x-cloak class="max-w-sm mx-auto mt-4">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-300" x-text="erro"></div>
    </div>

    {{-- Resultado estilo terminal --}}
    <div x-show="resultado" x-cloak class="max-w-sm mx-auto mt-8 space-y-4">

        {{-- Situação --}}
        <div class="text-center">
            <span class="inline-block text-xs font-mono font-bold text-red-500 uppercase tracking-wider mb-1">Situação reportada</span>
            <p class="text-sm text-zinc-500 dark:text-white/70" x-text="'\"' + situacaoFeita + '\"'"></p>
        </div>

        {{-- Card terminal --}}
        <div class="bg-zinc-950 border border-red-900/50 rounded-2xl p-5 font-mono text-sm space-y-4 shadow-lg shadow-red-500/5">

            {{-- Nível --}}
            <div class="flex items-center justify-between">
                <span class="text-red-400 text-xs uppercase tracking-wider">Nível de crise</span>
                <span class="text-2xl font-black text-red-500" x-text="nivel"></span>
            </div>

            {{-- Barra de nível --}}
            <div class="h-2 bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-red-600 to-red-400 rounded-full transition-all duration-1000"
                     :style="'width: ' + (nivelNum * 10) + '%'"></div>
            </div>

            {{-- Ameaça --}}
            <div>
                <span class="text-red-400/70 text-xs uppercase tracking-wider block mb-1">Análise da ameaça</span>
                <p class="text-zinc-100 leading-relaxed" x-text="ameaca"></p>
            </div>

            {{-- Plano de acção --}}
            <div>
                <span class="text-red-400/70 text-xs uppercase tracking-wider block mb-2">Plano de acção</span>
                <div class="space-y-2">
                    <template x-for="(acc, i) in accoes" :key="i">
                        <div class="flex items-start gap-2">
                            <span class="text-red-500 font-bold shrink-0" x-text="(i+1) + '.'"></span>
                            <span class="text-zinc-300" x-text="acc"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Mensagem final --}}
            <div class="pt-3 border-t border-zinc-800">
                <span class="text-red-400/70 text-xs uppercase tracking-wider block mb-1">Mensagem de força</span>
                <p class="text-zinc-100 italic" x-text="forca"></p>
            </div>

            <div class="pt-2 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xs text-zinc-600 hover:text-zinc-400">clinky.cc</a>
                <span class="text-xs text-zinc-700">MODO PÂNICO v1.0</span>
            </div>
        </div>

        {{-- Nova situação --}}
        <button @click="nova()"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95">
            Nova situação
        </button>
    </div>

</div>

{{-- Share bar --}}
<div x-data="{
        get shareText() { return Alpine.store('panico').shareText },
        shareUrl: '{{ route('panico.index') }}',
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
     x-show="$store.panico.resultado"
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
    Alpine.store('panico', { resultado: false, shareText: '' })

    Alpine.data('panico', () => ({
        situacao: '',
        situacaoFeita: '',
        resultado: false,
        loading: false,
        erro: '',
        nivel: '',
        nivelNum: 0,
        ameaca: '',
        accoes: [],
        forca: '',
        parse(text) {
            const lines = text.split('\n').map(l => l.trim()).filter(Boolean)
            for (const line of lines) {
                if (line.startsWith('NÍVEL:')) {
                    this.nivel = line.replace('NÍVEL:', '').trim()
                    const match = this.nivel.match(/(\d+)/)
                    this.nivelNum = match ? parseInt(match[1]) : 9
                } else if (line.startsWith('AMEAÇA:')) {
                    this.ameaca = line.replace('AMEAÇA:', '').trim()
                } else if (line.match(/^ACÇÃO \d:/)) {
                    this.accoes.push(line.replace(/^ACÇÃO \d:/, '').trim())
                } else if (line.startsWith('FORÇA:')) {
                    this.forca = line.replace('FORÇA:', '').trim()
                }
            }
            if (!this.nivel) this.nivel = '9/10'
            if (!this.ameaca) this.ameaca = text
        },
        async activar() {
            if (!this.situacao.trim()) return
            this.situacaoFeita = this.situacao.trim()
            this.loading = true
            this.resultado = false
            this.erro = ''
            this.nivel = ''
            this.nivelNum = 0
            this.ameaca = ''
            this.accoes = []
            this.forca = ''
            try {
                const res = await fetch('{{ route('panico.activar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ situacao: this.situacaoFeita })
                })
                if (res.status === 429) {
                    this.erro = 'Demasiados alertas. O sistema precisa de arrefecer.'
                    return
                }
                const data = await res.json()
                this.parse(data.resposta)
                this.resultado = true
                Alpine.store('panico').resultado = true
                Alpine.store('panico').shareText = '🚨 Activei o Modo Pânico para "' + this.situacaoFeita + '" e o nível de crise é ' + this.nivel + '\n\nActiva o teu:'
            } catch(e) {
                this.erro = 'O sistema de pânico entrou em pânico. Tenta novamente.'
            } finally {
                this.loading = false
            }
        },
        nova() {
            this.situacao = ''
            this.situacaoFeita = ''
            this.resultado = false
            this.erro = ''
            this.nivel = ''
            this.nivelNum = 0
            this.ameaca = ''
            this.accoes = []
            this.forca = ''
            Alpine.store('panico').resultado = false
            Alpine.store('panico').shareText = ''
        }
    }))
})
</script>
@endpush
