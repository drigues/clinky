# SKILL — Google Analytics 4 + Banner de Consentimento (Clinky.cc)

## Objectivo
Adicionar Google Analytics 4 ao Clinky.cc com **Google Consent Mode v2** e um **banner de cookies minimalista em Alpine.js**, em conformidade com RGPD/UE.

**Measurement ID:** `G-J4FCK7KZ9W`

## Lê primeiro
- `CLAUDE.md`
- `REFERENCES/PRIVACY.md` (regras de privacidade do projecto)
- `REFERENCES/COMPONENTS.md` (padrão de componentes Blade)

---

## Princípios

1. **Consent Mode v2 obrigatório.** O GA4 carrega sempre, mas em modo `denied` por defeito. Só passa a `granted` após consentimento explícito.
2. **Sem tracking em desenvolvimento.** O script só dispara em `app()->environment('production')`.
3. **Sem cookies antes do consentimento.** Zero cookies de marketing/analytics são definidos até o utilizador aceitar.
4. **Banner mobile-first.** Flutuante em baixo, dispensável com 1 toque, dois botões claros: **Aceitar** / **Rejeitar**.
5. **Persistência local.** A escolha guarda em `localStorage` (não em cookie de servidor — coerente com `PRIVACY.md`).
6. **Mantém Fathom.** Se o Fathom já está instalado, não o remove — funciona em paralelo (Fathom é cookieless e não precisa de consentimento).

---

## Tasks

### 1. Variável de ambiente

Em `.env`:
```env
GA_MEASUREMENT_ID=G-J4FCK7KZ9W
```

Em `.env.example`:
```env
GA_MEASUREMENT_ID=
```

### 2. Config

Em `config/services.php`, adicionar:
```php
'google_analytics' => [
    'measurement_id' => env('GA_MEASUREMENT_ID'),
],
```

### 3. Componente Blade — Google Analytics

Criar `resources/views/components/google-analytics.blade.php`:

```blade
@php($gaId = config('services.google_analytics.measurement_id'))

@if($gaId && app()->environment('production'))
{{-- Google Consent Mode v2: definir defaults ANTES de carregar gtag --}}
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}

    // Defaults: tudo negado até consentimento explícito
    gtag('consent', 'default', {
        'ad_storage': 'denied',
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'analytics_storage': 'denied',
        'functionality_storage': 'granted',
        'security_storage': 'granted',
        'wait_for_update': 500
    });

    // Aplicar consentimento previamente guardado
    try {
        const saved = localStorage.getItem('clinky_consent');
        if (saved === 'granted') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    } catch (e) {}

    gtag('js', new Date());
    gtag('config', '{{ $gaId }}', {
        anonymize_ip: true,
        cookie_flags: 'SameSite=Lax;Secure'
    });
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
@endif
```

### 4. Componente Blade — Banner de Consentimento

Criar `resources/views/components/cookie-banner.blade.php`:

```blade
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
                    // Pequeno delay para não interromper o primeiro paint
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
            // Mantém defaults (denied) — não é necessário update
            this.visible = false;
        }
    }
}
</script>

<style>[x-cloak] { display: none !important; }</style>
@endif
```

### 5. Incluir nos layouts

**`resources/views/layouts/hub.blade.php`** — dentro do `<head>`, antes do `</head>`:
```blade
<x-google-analytics />
```

E dentro do `<body>`, antes de `</body>`:
```blade
<x-cookie-banner />
```

**`resources/views/layouts/minisite.blade.php`** — exactamente o mesmo:
```blade
{{-- no <head> --}}
<x-google-analytics />

{{-- antes de </body> --}}
<x-cookie-banner />
```

### 6. Página de privacidade

Em `resources/views/pages/privacidade.blade.php` (ou onde estiver), adicionar secção sobre GA:

```blade
<section class="mt-8">
    <h2 class="text-xl font-bold mb-3">Cookies e Analytics</h2>
    <p class="mb-2">
        Usamos <strong>Google Analytics 4</strong> para perceber quais mini-sites
        funcionam melhor. O GA só carrega após aceitares no banner.
    </p>
    <p class="mb-2">
        Recolhemos: páginas visitadas, tempo na página, país aproximado, tipo de dispositivo.
    </p>
    <p class="mb-2">
        <strong>Não recolhemos:</strong> nome, email, endereço IP completo (anonimizado),
        nem qualquer input que escrevas nos mini-sites.
    </p>
    <p>
        Para revogar o consentimento, limpa os dados deste site no teu browser
        (o banner aparecerá novamente).
    </p>
</section>
```

### 7. Eventos custom (opcional, mas recomendado)

Em cada mini-site, dispara eventos para análise. Exemplos:

**Desculpómetro** (após gerar):
```javascript
if (typeof gtag !== 'undefined') {
    gtag('event', 'desculpa_gerada', {
        situacao: this.situacao,
        nivel: this.absurdo
    });
}
```

**Aperta o Botão** (após pressionar):
```javascript
if (typeof gtag !== 'undefined') {
    gtag('event', 'botao_pressionado', {
        total_global: data.total
    });
}
```

**Share bar** (em `share-bar.blade.php`, após partilha):
```javascript
if (typeof gtag !== 'undefined') {
    gtag('event', 'share', {
        method: 'whatsapp',  // ou 'native', 'copy'
        site: '{{ request()->path() }}'
    });
}
```

### 8. Deploy no Forge

1. Fazer commit + push
2. No painel Forge → Site → **Environment** → adicionar `GA_MEASUREMENT_ID=G-J4FCK7KZ9W`
3. **Deploy** (o post-deploy script já corre `php artisan config:cache`)

### 9. Verificação pós-deploy

- [ ] Abrir `clinky.cc` em janela anónima → banner aparece após ~800ms
- [ ] Clicar **Rejeitar** → banner fecha, GA não dispara `analytics_storage`
- [ ] Limpar localStorage, recarregar, clicar **Aceitar** → no GA4 DebugView aparece evento
- [ ] Abrir DevTools → Application → Cookies: confirmar que `_ga*` só aparece após aceitar
- [ ] Em modo dev (`APP_ENV=local`) → nenhum script GA é carregado

---

## Checklist final

- [ ] `GA_MEASUREMENT_ID` em `.env` e `.env.example`
- [ ] `config/services.php` actualizado
- [ ] `components/google-analytics.blade.php` criado
- [ ] `components/cookie-banner.blade.php` criado
- [ ] `<x-google-analytics />` em ambos os layouts
- [ ] `<x-cookie-banner />` em ambos os layouts
- [ ] Página de privacidade actualizada
- [ ] (Opcional) Eventos custom nos mini-sites principais
- [ ] `GA_MEASUREMENT_ID` configurado no Forge
- [ ] Banner testado em mobile (375px)
- [ ] Verificado no GA4 DebugView

---

## Regras absolutas — nunca violar

1. **Nunca carregar GA antes do Consent Mode `default`.** A ordem importa: `gtag('consent', 'default', {...})` SEMPRE antes do script `gtag/js`.
2. **Nunca activar `ad_storage` ou `ad_personalization`.** Clinky.cc não usa publicidade.
3. **Nunca guardar input do utilizador no GA.** Os mini-sites recebem texto livre (Desculpómetro, Nome, Corporativo) — esse texto **nunca** vai como parâmetro de evento.
4. **Banner não tem botão "X" sozinho.** Tem que ter Aceitar e Rejeitar visíveis (RGPD: rejeitar tem que ser tão fácil como aceitar).
5. **`anonymize_ip: true` sempre.** Mesmo após consentimento.
