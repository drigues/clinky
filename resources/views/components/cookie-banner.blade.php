@if(config('services.google_analytics.measurement_id') && app()->environment('production'))
<div x-data="cookieBanner()"
     x-show="visible"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-8"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-8"
     x-cloak
     class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:bottom-6 md:max-w-sm z-[9999]"
     role="dialog"
     aria-labelledby="cookie-banner-title"
     aria-describedby="cookie-banner-desc">

    <div class="bg-zinc-900 text-white rounded-2xl shadow-2xl border border-white/10 p-5">
        <p id="cookie-banner-title" class="font-bold text-base mb-1.5 flex items-center gap-2">
            🍪 Cookies
        </p>
        <p id="cookie-banner-desc" class="text-sm text-white/70 leading-relaxed mb-4">
            Usamos cookies de analytics para perceber o que diverte mais.
            Sem isto, nada é guardado.
            <a href="/privacidade" class="underline hover:text-white">Saber mais</a>.
        </p>
        <div class="flex gap-2">
            <button @click="reject()"
                    class="flex-1 px-4 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-sm font-semibold transition-colors">
                Rejeitar
            </button>
            <button @click="accept()"
                    class="flex-1 px-4 py-2.5 rounded-full bg-[#c8f135] hover:bg-[#d4ff4d] text-black text-sm font-bold transition-colors">
                Aceitar
            </button>
        </div>
    </div>
</div>

<script>
function cookieBanner() {
    return {
        visible: false,
        init() {
            try {
                const saved = localStorage.getItem('clinky_consent');
                if (!saved) {
                    setTimeout(() => { this.visible = true; }, 800);
                }
            } catch (e) {
                this.visible = true;
            }
        },
        accept() {
            try { localStorage.setItem('clinky_consent', 'granted'); } catch (e) {}
            if (typeof gtag !== 'undefined') {
                gtag('consent', 'update', {
                    'analytics_storage': 'granted'
                });
            }
            this.visible = false;
        },
        reject() {
            try { localStorage.setItem('clinky_consent', 'denied'); } catch (e) {}
            this.visible = false;
        }
    }
}
</script>

<style>[x-cloak] { display: none !important; }</style>
@endif
