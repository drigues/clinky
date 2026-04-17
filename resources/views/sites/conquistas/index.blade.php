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
  "name": "Conquistas do Nada",
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
<div class="min-h-screen px-4 pb-32" x-data="conquistas()" @click="registerClick()">

    <x-hero
        emoji="🏆"
        title="Conquistas do Nada"
        tagline="Medalhas por zero esforço"
        accent="#CA8A04"
        eyebrow="COMPLETIONISM · 19" />

    {{-- Progresso --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-sm font-medium px-4 py-2 rounded-full">
            <span x-text="desbloqueadas"></span>/<span>{{ $total }}</span> desbloqueadas
        </div>
        <p class="mt-2 text-sm font-medium transition-all duration-300"
           :class="desbloqueadas >= 8 ? 'text-amber-500' : desbloqueadas >= 5 ? 'text-yellow-500' : 'text-zinc-400 dark:text-white/60'"
           x-text="mensagem"></p>
    </div>

    {{-- Grid de conquistas --}}
    <div class="max-w-3xl mx-auto grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
        <template x-for="c in lista" :key="c.id">
            <div class="relative rounded-2xl border p-4 text-center transition-all duration-500"
                 :class="unlocked[c.id]
                     ? 'bg-amber-50 dark:bg-amber-950/20 border-amber-300 dark:border-amber-700'
                     : 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800'">

                {{-- Emoji / Cadeado --}}
                <div class="text-4xl mb-2 transition-transform duration-500"
                     :class="unlocked[c.id] ? 'scale-100' : 'grayscale opacity-30'">
                    <span x-show="unlocked[c.id] || !c.secret" x-text="unlocked[c.id] ? c.emoji : '🔒'"></span>
                    <span x-show="!unlocked[c.id] && c.secret">❓</span>
                </div>

                {{-- Titulo --}}
                <p class="text-sm font-semibold leading-tight transition-colors duration-300"
                   :class="unlocked[c.id]
                       ? 'text-zinc-900 dark:text-zinc-100'
                       : 'text-zinc-400 dark:text-white/50'">
                    <span x-show="unlocked[c.id] || !c.secret" x-text="unlocked[c.id] ? c.titulo : c.titulo"></span>
                    <span x-show="!unlocked[c.id] && c.secret">???</span>
                </p>

                {{-- Descrição --}}
                <p class="text-xs mt-1 leading-snug transition-colors duration-300"
                   :class="unlocked[c.id]
                       ? 'text-zinc-500 dark:text-white/70'
                       : 'text-zinc-300 dark:text-zinc-700'">
                    <span x-show="unlocked[c.id]" x-text="c.desc"></span>
                    <span x-show="!unlocked[c.id] && !c.secret">Ainda não desbloqueada</span>
                    <span x-show="!unlocked[c.id] && c.secret">Secreta</span>
                </p>

                {{-- Brilho ao desbloquear --}}
                <div x-show="justUnlocked === c.id"
                     x-transition:enter="transition duration-700"
                     x-transition:enter-start="opacity-0 scale-150"
                     x-transition:enter-end="opacity-0 scale-100"
                     class="absolute inset-0 rounded-2xl bg-amber-400/20 pointer-events-none"></div>
            </div>
        </template>
    </div>

    {{-- Toast de conquista --}}
    <div x-show="toast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-4 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-4 opacity-0"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-amber-500 text-white font-semibold text-sm px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
        <span x-text="toastEmoji" class="text-lg"></span>
        <span x-text="toastText"></span>
    </div>

</div>

{{-- Share bar --}}
<div x-data="{
        get shareText() { return Alpine.store('conquistas').shareText },
        shareUrl: '{{ route('conquistas.index') }}',
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
     x-show="$store.conquistas.desbloqueadas > 0"
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
    Alpine.store('conquistas', { desbloqueadas: 0, shareText: '' })

    Alpine.data('conquistas', () => ({
        lista: {!! $conquistas_json !!},
        unlocked: {},
        clicks: 0,
        seconds: 0,
        toast: false,
        toastEmoji: '',
        toastText: '',
        justUnlocked: null,
        timerInterval: null,

        get desbloqueadas() {
            return Object.values(this.unlocked).filter(Boolean).length
        },

        get mensagem() {
            const n = this.desbloqueadas
            if (n === 0) return 'Ainda nada. Continua a existir.'
            if (n < 3) return 'O teu potencial de nada é promissor.'
            if (n < 5) return 'Tens talento para o inútil.'
            if (n < 7) return 'Profissional do nada.'
            if (n < 9) return 'Lenda do vazio.'
            return 'Completaste o nada. Parabéns por tudo o que não fizeste.'
        },

        init() {
            // Restore from localStorage
            const saved = localStorage.getItem('clinky_conquistas')
            if (saved) {
                try { this.unlocked = JSON.parse(saved) } catch(e) {}
            }

            // Track visits
            let visits = parseInt(localStorage.getItem('clinky_conquistas_visits') || '0')
            visits++
            localStorage.setItem('clinky_conquistas_visits', visits.toString())

            // Trigger: first visit
            this.tryUnlock('primeiro_passo')

            // Trigger: return visit
            if (visits > 1) this.tryUnlock('recidivista')

            // Trigger: 5+ visits
            if (visits >= 5) this.tryUnlock('fiel')

            // Trigger: late night (00h-05h)
            const hour = new Date().getHours()
            if (hour >= 0 && hour < 5) this.tryUnlock('nocturno')

            // Timer for time-based triggers
            this.timerInterval = setInterval(() => {
                this.seconds++
                if (this.seconds >= 30) this.tryUnlock('turista')
                if (this.seconds >= 120) this.tryUnlock('residente')
                if (this.seconds >= 300) {
                    this.tryUnlock('filosofo')
                    clearInterval(this.timerInterval)
                }
            }, 1000)

            // Scroll detection
            const scrollHandler = () => {
                const scrollTop = window.scrollY || document.documentElement.scrollTop
                const scrollHeight = document.documentElement.scrollHeight
                const clientHeight = document.documentElement.clientHeight
                if (scrollTop + clientHeight >= scrollHeight - 20) {
                    this.tryUnlock('explorador')
                    window.removeEventListener('scroll', scrollHandler)
                }
            }
            window.addEventListener('scroll', scrollHandler)

            // Sync store
            this.$watch('desbloqueadas', (n) => {
                Alpine.store('conquistas').desbloqueadas = n
                Alpine.store('conquistas').shareText = '🏆 Desbloqueei ' + n + '/{{ $total }} conquistas do nada.\n\n' + this.mensagem + '\n\nConsegues superar?'
            })
            Alpine.store('conquistas').desbloqueadas = this.desbloqueadas
        },

        registerClick() {
            this.clicks++
            if (this.clicks >= 10) this.tryUnlock('clicker')
            if (this.clicks >= 50) this.tryUnlock('obsessivo')
        },

        tryUnlock(id) {
            if (this.unlocked[id]) return
            this.unlocked[id] = true
            this.save()
            this.showToast(id)
        },

        save() {
            localStorage.setItem('clinky_conquistas', JSON.stringify(this.unlocked))
        },

        showToast(id) {
            const c = this.lista.find(x => x.id === id)
            if (!c) return
            this.justUnlocked = id
            this.toastEmoji = c.emoji
            this.toastText = c.titulo + ' desbloqueada'
            this.toast = true
            setTimeout(() => {
                this.toast = false
                this.justUnlocked = null
            }, 3000)
        }
    }))
})
</script>
@endpush
