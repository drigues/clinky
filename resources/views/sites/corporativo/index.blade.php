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
  "name": "Tradutor Corporativo",
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
<div class="min-h-screen px-4 pb-32" x-data="corporativo()">

    <x-site-header
        emoji="💼"
        title="Tradutor Corporativo"
        tagline="O que realmente querem dizer no escritório"
        accentColor="red"
    />

    <div class="max-w-sm mx-auto mb-6">
        <div class="flex flex-wrap gap-2 justify-center">
            @foreach(array_slice($termos, 0, 8) as $termo)
            <button @click="texto = '{{ $termo }}'; traduzir()"
                    class="text-xs bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 px-3 py-1.5 rounded-full hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                {{ $termo }}
            </button>
            @endforeach
        </div>
    </div>

    <form @submit.prevent="traduzir" class="max-w-sm mx-auto space-y-4">
        <div>
            <label for="texto" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Escreve o jargão aqui
            </label>
            <input type="text"
                   id="texto"
                   x-model="texto"
                   placeholder="vamos alinhar, synergy, deep dive..."
                   maxlength="300"
                   autocomplete="off"
                   autocorrect="off"
                   class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-500/50">
        </div>

        <button type="submit"
                :disabled="loading || !texto.trim()"
                class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95 disabled:opacity-50">
            <span x-show="!loading">Traduzir para português real</span>
            <span x-show="loading" x-cloak>A consultar o dicionário honesto...</span>
        </button>
    </form>

    <div x-show="traducao" x-cloak class="max-w-sm mx-auto mt-6">
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">💼</span>
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Tradução honesta</p>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500" x-text="'«' + textoTraduzido + '»'"></p>
                </div>
            </div>
            <p class="text-base leading-relaxed text-zinc-900 dark:text-zinc-100" x-text="traducao"></p>
            <p class="mt-4 text-[10px] text-zinc-400 dark:text-zinc-500 italic">
                * Tradução não oficial mas surpreendentemente precisa.
            </p>
        </div>
    </div>

    <div x-show="loading && !traducao" x-cloak class="max-w-sm mx-auto mt-6">
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6">
            <div class="flex items-center gap-3 text-zinc-500">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
                <span class="text-sm">A traduzir...</span>
            </div>
        </div>
    </div>

    <div x-show="erro" x-cloak class="max-w-sm mx-auto">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 my-4 text-sm text-red-700 dark:text-red-300" x-text="erro"></div>
    </div>

    <div class="max-w-sm mx-auto mt-10">
        <h2 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mb-4 text-center">Dicionário de sobrevivência</h2>
        <div class="space-y-2">
            @foreach($termos as $termo)
            <button @click="texto = '{{ $termo }}'; traduzir()"
                    class="w-full text-left bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 hover:border-red-300 dark:hover:border-red-600 transition-colors">
                {{ $termo }}
            </button>
            @endforeach
        </div>
    </div>

</div>

<div x-data="{
        get shareText() {
            const s = Alpine.store('corporativo');
            if (s.traducao) return '💼 «' + s.texto + '» = ' + s.traducao + '\n\nDescobre mais traduções em:';
            return '';
        },
        shareUrl: 'https://corporativo.{{ config('app.base_domain') }}',
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
     x-show="$store.corporativo.traducao" x-cloak
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
    Alpine.store('corporativo', { traducao: '', texto: '' })

    Alpine.data('corporativo', () => ({
        texto: '',
        traducao: '',
        textoTraduzido: '',
        loading: false,
        erro: '',
        async traduzir() {
            if (!this.texto.trim()) return;
            this.loading = true;
            this.traducao = '';
            this.erro = '';
            try {
                const res = await fetch('/traduzir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ texto: this.texto.trim() })
                });
                if (res.status === 429) {
                    this.erro = 'Demasiadas tentativas. Aguarda um momento e tenta novamente.';
                    return;
                }
                const data = await res.json();
                this.traducao = data.traducao;
                this.textoTraduzido = this.texto.trim();
                Alpine.store('corporativo').traducao = data.traducao;
                Alpine.store('corporativo').texto = this.texto.trim();
            } catch(e) {
                this.erro = 'Não foi possível traduzir. Verifica a tua ligação e tenta novamente.';
            } finally {
                this.loading = false;
            }
        }
    }))
})
</script>
@endpush
