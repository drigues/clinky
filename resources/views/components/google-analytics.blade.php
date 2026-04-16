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
