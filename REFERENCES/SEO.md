# SEO.md — Clinky.cc

## Filosofia SEO para mini-sites virais

O objetivo primário não é ranking orgânico — é **shareability**. Quando alguém partilha no WhatsApp, o link tem de:
1. Mostrar uma imagem apelativa (OG image)
2. Ter um título que dá vontade de clicar
3. Ter uma descrição que explica a brincadeira em 1 frase

O ranking orgânico é secundário mas real: mini-sites com conteúdo divertido recebem links espontâneos.

---

## Meta tags obrigatórias por mini-site

Cada mini-site define estas variáveis no seu controller e passa para a view:

```php
return view('sites.desculpometro.index', [
    'seo' => [
        'title'          => 'Desculpómetro — Gera a Desculpa Perfeita em 1 Segundo',
        'description'    => 'Gerador de desculpas absurdas, criativas e irresistíveis. Ideal para quando faltaste ao trabalho, ao ginásio, ou à vida.',
        'og_title'       => '😅 Desculpómetro — A culpa foi do gato filosófico',
        'og_description' => 'Gera a tua desculpa perfeita agora. Grátis, anónimo, partilhável.',
        'og_image'       => asset('images/og/desculpometro.png'),  // 1200×630px
        'canonical'      => 'https://desculpometro.clinky.cc',
        'keywords'       => 'desculpas engraçadas, gerador de desculpas, desculpa criativa, humor',
    ]
]);
```

---

## OG Images (imagens de partilha)

Cada mini-site tem uma OG image estática em `public/images/og/{site}.png`.

**Especificações:**
- Tamanho: **1200 × 630px** (proporção 1.91:1 — padrão Facebook/WhatsApp)
- Formato: PNG ou JPG (PNG preferido para texto nítido)
- Fundo: dark (#0A0A0A) com cor de acento do mini-site
- Conteúdo: emoji grande + nome do site + tagline curta
- Texto mínimo 48px para legibilidade em thumbnails

**Para gerar as OG images**, usar o script `scripts/generate-og.js` (Node.js + Canvas ou Puppeteer). Ver `REFERENCES/COMPONENTS.md`.

---

## Títulos — fórmulas que funcionam

```
{Emoji} {Nome} — {Promessa em menos de 8 palavras}

Exemplos:
😅 Desculpómetro — Gera a Desculpa Perfeita em 1 Segundo
🔴 Aperta o Botão — Já Apertaram 1.247.891 Vezes
💬 Nomeador de Grupos — Nomes Épicos para o Teu WhatsApp
🎯 Bingo do Imigrante — Reconheces a Tua Vida em Portugal?
```

**Regras:**
- Máximo 60 caracteres para o `<title>`
- Máximo 155 caracteres para `meta description`
- OG title pode ser mais curto e mais emocional (até 70 chars)
- Nunca usar "clica aqui" ou CTAs óbvios

---

## Structured Data (JSON-LD)

Cada mini-site inclui no `<head>`:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Desculpómetro",
  "description": "Gerador de desculpas absurdas e criativas",
  "url": "https://desculpometro.clinky.cc",
  "applicationCategory": "EntertainmentApplication",
  "operatingSystem": "Web",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "EUR"
  },
  "inLanguage": "pt-PT",
  "isPartOf": {
    "@type": "WebSite",
    "name": "Clinky.cc",
    "url": "https://clinky.cc"
  }
}
</script>
```

---

## Sitemap

`clinky.cc/sitemap.xml` lista todas as URLs:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://clinky.cc</loc>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://desculpometro.clinky.cc</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <!-- ... -->
</urlset>
```

Gerado dinamicamente via `SitemapController` com lista dos sites activos.

---

## robots.txt

```
User-agent: *
Allow: /
Sitemap: https://clinky.cc/sitemap.xml

# Bloquear admin
Disallow: /admin
Disallow: /horizon
```

---

## WhatsApp Share — mecânica viral

O botão de partilha WhatsApp deve incluir o texto + URL:

```blade
{{-- resources/views/components/share-bar.blade.php --}}
@php
$whatsappText = urlencode($text . "\n\n" . $url);
$whatsappUrl = "https://wa.me/?text={$whatsappText}";
@endphp

<a href="{{ $whatsappUrl }}"
   target="_blank"
   rel="noopener"
   @click="$dispatch('share-click', { platform: 'whatsapp' })"
   class="...">
    Partilhar no WhatsApp
</a>
```

**Texto de partilha** — cada mini-site define o seu. Fórmula:
```
{Resultado gerado ou tagline engraçada}

Descobre o teu em: {URL}
```

Exemplo para Desculpómetro:
```
"Não fui porque o meu gato estava a ter uma crise existencial e precisava de apoio emocional."

Gera a tua desculpa em: https://desculpometro.clinky.cc
```

---

## Web Share API (mobile nativo)

```javascript
// Alpine.js component para share
function shareBar() {
    return {
        canShare: typeof navigator.share !== 'undefined',
        async share(text, url) {
            if (this.canShare) {
                try {
                    await navigator.share({ text, url, title: document.title });
                    // Track share event
                    fetch('/api/track-share', { method: 'POST' });
                } catch(e) {
                    // User cancelled — não é erro
                }
            } else {
                // Fallback: copia para clipboard
                navigator.clipboard.writeText(text + '\n\n' + url);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        },
        copied: false,
    }
}
```

---

## Performance SEO

**Core Web Vitals metas:**
- LCP < 2.5s
- FID < 100ms
- CLS < 0.1

**Regras:**
- Zero render-blocking scripts (tudo `defer` ou no final do `<body>`)
- Imagens com `loading="lazy"` e `width`/`height` definidos
- Tailwind CSS purged (Vite faz isto automaticamente)
- Sem Google Fonts em produção — usar system font stack ou fonte auto-hospedada
- Fathom Analytics é assíncrono e leve (~2KB)

**Font stack recomendado:**
```css
font-family: 'Syne', 'Georgia', sans-serif; /* headings - auto-hosted */
font-family: system-ui, -apple-system, sans-serif; /* body */
```

---

## Hreflang (PT/BR)

Para mini-sites com conteúdo dual PT/BR:

```html
<link rel="alternate" hreflang="pt-PT" href="https://conversor.clinky.cc">
<link rel="alternate" hreflang="pt-BR" href="https://conversor.clinky.cc?lang=br">
<link rel="alternate" hreflang="x-default" href="https://conversor.clinky.cc">
```

---

## Checklist SEO por mini-site

Antes de publicar, verificar:

- [ ] `<title>` único, menos de 60 chars
- [ ] `meta description` entre 120-155 chars
- [ ] OG image 1200×630px existe em `public/images/og/`
- [ ] `og:image` aponta para URL absoluta com HTTPS
- [ ] `canonical` definido
- [ ] JSON-LD `WebApplication` no `<head>`
- [ ] robots.txt não bloqueia o mini-site
- [ ] URL adicionada ao sitemap
- [ ] Share button funciona e gera texto apelativo
- [ ] Página carrega em menos de 2s no mobile (testar com PageSpeed Insights)
