@extends('layouts.minisite')

@section('title', $seo['title'])
@section('description', $seo['description'])
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_image', $seo['og_image'])

@section('content')
<div class="min-h-screen px-4 pb-32">

    <header class="text-center py-12">
        <div class="text-6xl mb-4">{{ $emoji }}</div>
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ $nome }}
        </h1>
        <p class="mt-1 text-zinc-500 dark:text-zinc-400 text-xs">{{ $datas }}</p>
        <p class="mt-2 text-purple-500 dark:text-purple-400 text-sm font-medium">
            Previsão de {{ now()->locale('pt')->isoFormat('D [de] MMMM') }}
        </p>
    </header>

    <div class="max-w-sm mx-auto">
        <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6">
            <p class="text-lg leading-relaxed text-zinc-900 dark:text-zinc-100">
                {{ $previsao }}
            </p>
            <p class="mt-4 text-[10px] text-zinc-400 dark:text-zinc-500 italic">
                * Previsão 100% inventada. As estrelas não se responsabilizam.
            </p>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ url('/') }}"
               class="text-sm text-purple-500 hover:text-purple-600 dark:text-purple-400 dark:hover:text-purple-300 font-medium transition-colors">
                ← Ver todos os signos
            </a>
        </div>
    </div>

</div>

<div x-data="{
        shareUrl: '{{ $seo['canonical'] }}',
        shareText: 'O meu horóscopo de hoje diz: \'{{ addslashes($previsao) }}\'\n\n🔮 Descobre o teu em:',
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
