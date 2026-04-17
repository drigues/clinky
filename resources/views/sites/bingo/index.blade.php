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
  "name": "Bingo do Imigrante",
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
<div class="min-h-screen px-4 pb-32" x-data="bingo()">

    <x-hero
        emoji="🎯"
        title="Bingo do Imigrante"
        tagline="Quantas situações já viveste em Portugal?"
        accent="#fbbf24"
        eyebrow="PT / BR · 06" />

    <div class="text-center mb-6">
        <span class="inline-flex items-center gap-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-sm font-medium px-4 py-2 rounded-full">
            <span x-text="total"></span>/25 situações vividas
        </span>
    </div>

    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-3 md:grid-cols-5 gap-2 md:gap-3">
            @foreach($quadrados as $i => $texto)
            <button @click="toggle({{ $i }})"
                    :class="marcados.includes({{ $i }})
                        ? 'bg-yellow-400 border-yellow-300 text-black'
                        : 'bg-zinc-900 border-zinc-700 text-white hover:border-yellow-500/60'"
                    class="aspect-square p-2 md:p-4 rounded-xl md:rounded-2xl
                           border-2 transition-all duration-200
                           flex items-center justify-center text-center
                           text-[11px] md:text-sm font-bold leading-tight
                           hover:scale-[1.03] active:scale-95"
                    aria-label="{{ $texto }}">
                <span x-show="!marcados.includes({{ $i }})">{{ $texto }}</span>
                <span x-show="marcados.includes({{ $i }})" x-cloak class="text-2xl md:text-4xl">✓</span>
            </button>
            @endforeach
        </div>
    </div>

    <div x-show="temBingo" x-cloak x-transition class="max-w-md mx-auto mt-6">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-2xl p-5 text-center">
            <p class="text-2xl mb-1">🎉</p>
            <p class="text-lg font-bold text-yellow-800 dark:text-yellow-200">BINGO!</p>
            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Completaste uma linha, coluna ou diagonal. Estás oficialmente integrado.</p>
        </div>
    </div>

    <div x-show="total >= 20 && !temBingo" x-cloak x-transition class="max-w-md mx-auto mt-6">
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-5 text-center">
            <p class="text-2xl mb-1">🇵🇹</p>
            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Já és meio português</p>
        </div>
    </div>

    <div class="max-w-md mx-auto mt-6 text-center">
        <button @click="limpar()" x-show="total > 0" x-cloak
                class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
            Limpar tudo
        </button>
    </div>

</div>

<div x-data="{
        get shareText() {
            const t = Alpine.store('bingo').total;
            if (t >= 20) return 'Fiz ' + t + '/25 no Bingo do Imigrante! Já sou meio português 🇵🇹';
            if (t >= 10) return 'Fiz ' + t + '/25 no Bingo do Imigrante! Ainda estou a integrar 😅';
            return 'Fiz ' + t + '/25 no Bingo do Imigrante! Acabei de chegar 🇧🇷';
        },
        shareUrl: '{{ route('bingo.index') }}',
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
     x-show="$store.bingo.total > 0" x-cloak
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
    Alpine.store('bingo', { total: 0 })

    Alpine.data('bingo', () => ({
        marcados: JSON.parse(localStorage.getItem('bingo_marks') || '[]'),
        temBingo: false,
        init() {
            Alpine.store('bingo').total = this.total;
            this.verificarBingo();
        },
        get total() { return this.marcados.length; },
        toggle(idx) {
            if (this.marcados.includes(idx)) {
                this.marcados = this.marcados.filter(i => i !== idx);
            } else {
                this.marcados.push(idx);
            }
            localStorage.setItem('bingo_marks', JSON.stringify(this.marcados));
            Alpine.store('bingo').total = this.total;
            this.verificarBingo();
        },
        limpar() {
            this.marcados = [];
            localStorage.removeItem('bingo_marks');
            Alpine.store('bingo').total = 0;
            this.temBingo = false;
        },
        verificarBingo() {
            const m = this.marcados;
            // Linhas
            for (let r = 0; r < 5; r++) {
                if ([0,1,2,3,4].every(c => m.includes(r * 5 + c))) { this.temBingo = true; return; }
            }
            // Colunas
            for (let c = 0; c < 5; c++) {
                if ([0,1,2,3,4].every(r => m.includes(r * 5 + c))) { this.temBingo = true; return; }
            }
            // Diagonal principal
            if ([0,6,12,18,24].every(i => m.includes(i))) { this.temBingo = true; return; }
            // Diagonal secundária
            if ([4,8,12,16,20].every(i => m.includes(i))) { this.temBingo = true; return; }
            this.temBingo = false;
        }
    }))
})
</script>
@endpush
