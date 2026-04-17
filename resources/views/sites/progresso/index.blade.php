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
  "name": "Barra de Progresso da Vida",
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
<div class="min-h-screen flex flex-col items-center px-4 pb-32 pt-16"
     x-data="progresso()" x-on:before-remove.window="destruir()">

    {{-- ESTADO INICIAL — Formulário --}}
    <div x-show="!calculado" x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="flex flex-col items-center w-full max-w-sm">

        <header class="text-center mb-10">
            <div class="text-6xl mb-4">⏳</div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                Barra de Progresso da Vida
            </h1>
            <p class="mt-2 text-zinc-500 dark:text-zinc-400 text-sm max-w-xs mx-auto">
                Quanto da tua vida já passou? Não é motivacional.
            </p>
        </header>

        <form @submit.prevent="calcular()" class="w-full space-y-5">
            <fieldset>
                <legend class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">
                    Quando nasceste?
                </legend>

                <div class="flex gap-3">
                    <div class="flex-1">
                        <label for="mes" class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Mês</label>
                        <select id="mes" x-model="mesNascimento" required
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
                            <option value="" disabled selected>—</option>
                            <option value="1">Janeiro</option>
                            <option value="2">Fevereiro</option>
                            <option value="3">Março</option>
                            <option value="4">Abril</option>
                            <option value="5">Maio</option>
                            <option value="6">Junho</option>
                            <option value="7">Julho</option>
                            <option value="8">Agosto</option>
                            <option value="9">Setembro</option>
                            <option value="10">Outubro</option>
                            <option value="11">Novembro</option>
                            <option value="12">Dezembro</option>
                        </select>
                    </div>

                    <div class="flex-1">
                        <label for="ano" class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Ano</label>
                        <input id="ano" type="number" x-model="anoNascimento" required
                               min="1900" :max="new Date().getFullYear()"
                               placeholder="1990"
                               class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
                    </div>
                </div>
            </fieldset>

            <button type="submit"
                    class="w-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-4 rounded-xl transition-transform active:scale-95">
                Ver o progresso
            </button>
        </form>

        <p class="mt-6 text-xs text-zinc-400 dark:text-zinc-500 text-center max-w-xs">
            A tua data de nascimento é calculada localmente no browser. Não é enviada a nenhum servidor.
        </p>
    </div>

    {{-- ESTADO RESULTADO --}}
    <div x-show="calculado" x-cloak
         x-transition:enter="transition ease-out duration-500 delay-200"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="flex flex-col items-center w-full max-w-md">

        <header class="text-center mb-8">
            <div class="text-5xl mb-3">⏳</div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 uppercase tracking-widest font-medium">
                A tua vida está
            </p>
        </header>

        {{-- Número grande --}}
        <p class="text-5xl sm:text-6xl font-black tracking-tight tabular-nums text-zinc-900 dark:text-zinc-100"
           x-text="percentagemDisplay + '%'">
        </p>

        {{-- Barra de progresso --}}
        <div class="w-full mt-8 mb-2">
            <div class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-full h-3 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-[2500ms] ease-out"
                     :style="`width: ${barraWidth}%; background: linear-gradient(90deg, #c8f135, #ff6b00)`">
                </div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-zinc-400 dark:text-zinc-500 tabular-nums">
                <span>0%</span>
                <span>100%</span>
            </div>
        </div>

        {{-- Detalhes --}}
        <div class="text-center mt-6 space-y-3">
            <p class="text-sm text-zinc-500 dark:text-zinc-400"
               x-text="tempoRestante">
            </p>

            <p class="text-sm italic text-zinc-600 dark:text-zinc-300 max-w-xs mx-auto"
               x-text="fraseContextual">
            </p>
        </div>

        {{-- Recalcular --}}
        <button @click="reset()"
                class="mt-8 text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors underline underline-offset-4">
            Tentar outra data
        </button>

        {{-- Nota de rodapé --}}
        <p class="mt-10 text-[10px] text-zinc-400 dark:text-zinc-500 text-center max-w-xs">
            * Baseado em esperança de vida média de 79 anos. O teu resultado pode variar.
        </p>
    </div>

</div>

{{-- Share bar --}}
<div x-data="{
        shareUrl: '{{ route('progresso.index') }}',
        get shareText() { return 'A minha vida está ' + ($store.progresso.pct || '0') + '% completa ⏳\n\nE a tua?' },
        canShare: typeof navigator.share !== 'undefined',
        copied: false,
        track(platform) {
            const t = document.querySelector('meta[name=csrf-token]');
            if (!t) return;
            fetch('/api/track', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t.content }, body: JSON.stringify({ event: 'share_' + platform }) }).catch(() => {});
            if (typeof gtagEvent !== 'undefined') gtagEvent('share', { method: platform, site: '/progresso' });
        },
        async nativeShare() { try { await navigator.share({ text: this.shareText, url: this.shareUrl, title: document.title }); this.track('native') } catch(e) {} },
        copy() { navigator.clipboard.writeText(this.shareText + '\n\nTenta tu: ' + this.shareUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000) }); this.track('copy') },
        get whatsappUrl() { return 'https://wa.me/?text=' + encodeURIComponent(this.shareText + '\n\nTenta tu: ' + this.shareUrl) }
     }"
     x-show="$store.progresso.calculado" x-cloak
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
    Alpine.store('progresso', { calculado: false, pct: '0.0' })

    Alpine.data('progresso', () => ({
        mesNascimento: '',
        anoNascimento: '',
        calculado: false,
        percentagem: 0,
        percentagemDisplay: '0.000000',
        barraWidth: 0,
        intervalo: null,
        esperancaVida: 79,

        nascimento: null,
        totalMs: 0,

        calcular() {
            if (!this.mesNascimento || !this.anoNascimento) return;

            const ano = parseInt(this.anoNascimento);
            const mes = parseInt(this.mesNascimento);
            const agora = new Date();

            if (ano < 1900 || ano > agora.getFullYear()) return;

            this.nascimento = new Date(ano, mes - 1, 15);
            const morte = new Date(this.nascimento);
            morte.setFullYear(morte.getFullYear() + this.esperancaVida);

            this.totalMs = morte - this.nascimento;
            const passadoMs = agora - this.nascimento;
            this.percentagem = Math.min((passadoMs / this.totalMs) * 100, 100);

            this.calculado = true;
            Alpine.store('progresso').calculado = true;

            // Animar barra com delay para a transição CSS funcionar
            setTimeout(() => {
                this.barraWidth = this.percentagem;
                this.percentagemDisplay = this.percentagem.toFixed(6);
            }, 100);

            // Actualizar decimais em tempo real
            this.intervalo = setInterval(() => {
                const agora2 = new Date();
                const passado2 = agora2 - this.nascimento;
                const p = Math.min((passado2 / this.totalMs) * 100, 100);
                this.percentagemDisplay = p.toFixed(6);
                Alpine.store('progresso').pct = p.toFixed(1);
            }, 100);
        },

        get tempoRestante() {
            if (!this.calculado || !this.nascimento) return '';
            const morte = new Date(this.nascimento);
            morte.setFullYear(morte.getFullYear() + this.esperancaVida);
            const agora = new Date();
            const diffMs = morte - agora;

            if (diffMs <= 0) return 'Estatisticamente, já deverias ter partido. Parabéns.';

            const dias = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            const anos = Math.floor(dias / 365.25);
            const meses = Math.floor((dias % 365.25) / 30.44);
            const diasRestantes = Math.floor(dias - (anos * 365.25) - (meses * 30.44));

            const partes = [];
            if (anos > 0) partes.push(anos + (anos === 1 ? ' ano' : ' anos'));
            if (meses > 0) partes.push(meses + (meses === 1 ? ' mês' : ' meses'));
            if (diasRestantes > 0) partes.push(diasRestantes + (diasRestantes === 1 ? ' dia' : ' dias'));

            return 'Aproximadamente ' + partes.join(', ') + ' restantes.';
        },

        get fraseContextual() {
            const p = this.percentagem;
            if (p < 25) return 'Ainda tens muito por descobrir. Ou talvez não.';
            if (p < 40) return 'Os melhores anos? Dependendo de quem perguntas.';
            if (p < 50) return 'Ainda não chegaste a meio. Respira.';
            if (p < 60) return 'Passaste do meio. Sem alarme necessário.';
            if (p < 75) return 'Estatisticamente, já viveste mais do que o que falta.';
            if (p < 90) return 'O tempo é uma ilusão. A barra não.';
            return 'Parabéns por chegares até aqui. A sério.';
        },

        get shareText() {
            return 'A minha vida está ' + parseFloat(this.percentagemDisplay).toFixed(1) + '% completa ⏳\n\nE a tua?';
        },

        reset() {
            this.destruir();
            this.calculado = false;
            Alpine.store('progresso').calculado = false;
            Alpine.store('progresso').pct = '0.0';
            this.percentagem = 0;
            this.percentagemDisplay = '0.000000';
            this.barraWidth = 0;
            this.mesNascimento = '';
            this.anoNascimento = '';
        },

        destruir() {
            if (this.intervalo) {
                clearInterval(this.intervalo);
                this.intervalo = null;
            }
        }
    }))
})
</script>
@endpush
