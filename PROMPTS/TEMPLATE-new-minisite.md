# TEMPLATE — Novo Mini-Site Clinky.cc

## Como usar este template

Copia este ficheiro para `PROMPTS/XX-nome-do-site.md` e preenche cada secção.
Depois passa o ficheiro ao Claude Code como prompt de execução.

---

## Lê primeiro (obrigatório)
- `SKILL.md`
- `REFERENCES/ARCHITECTURE.md`
- `REFERENCES/SEO.md`
- `REFERENCES/PRIVACY.md`
- `REFERENCES/COMPONENTS.md`

---

## Metadados do mini-site

```
Nome do site:      [ex: Gerador de Nomes de Startup]
Subdomínio:        [ex: startup.clinky.cc]
Slug interno:      [ex: startup]
Cor de acento:     [lime | orange | pink | blue | yellow | purple | teal | red]
Emoji principal:   [ex: 🚀]
Usa Claude API:    [Sim / Não]
Usa DB própria:    [Sim / Não — se Sim, descreve o modelo]
Categoria:         [Humor / BR↔PT / Quiz / Gerador / Contador / Outro]
```

---

## SEO

Preenche os valores:

```php
$seo = [
    'title'          => '[Máximo 60 chars] [Emoji] Nome — Promessa em 8 palavras',
    'description'    => '[120-155 chars] Descrição que explica a brincadeira.',
    'og_title'       => '[Mais emocional, até 70 chars] Para partilha social',
    'og_description' => '[Curto, para WhatsApp preview]',
    'og_image'       => asset('images/og/[slug].png'),
    'canonical'      => 'https://[slug].clinky.cc',
    'keywords'       => '[palavra1, palavra2, palavra3]',
];
```

**OG Image:** Descreve o design da imagem 1200×630:
- Fundo: [cor]
- Elementos: [emoji, texto, etc.]

---

## Descrição da funcionalidade

[Descreve em prosa o que o site faz, do ponto de vista do utilizador.
Ex: "O utilizador escreve o nome da sua startup. O site gera 3 nomes
alternativos ridículos com justificação pseudo-científica."]

---

## UX — fluxo do utilizador

1. [Passo 1: o que vê ao entrar]
2. [Passo 2: acção principal]
3. [Passo 3: resultado]
4. [Passo 4: partilha]

---

## Prompt Claude API (se aplicável)

```
System: [Descrição do papel da IA + regras de output]
User: [Formato do input do utilizador]
Max tokens: [100-500 dependendo do output]
Temperatura: [não aplicável na API v1, mas descreve o tom esperado]
```

---

## Tasks para Claude Code

### 1. Route
```php
Route::domain('[slug].' . config('app.base_domain'))->group(function () {
    Route::get('/', [[Slug em PascalCase]Controller::class, 'index']);
    // Adicionar routes POST se necessário
});
```

### 2. Controller
`app/Http/Controllers/Sites/[Slug em PascalCase]Controller.php`

Métodos necessários:
- `index()` — [descreve]
- `[ação]()` — [descreve]

Dados guardados em DB (se aplicável):
- [campo]: [tipo] — [propósito] — [contém PII? Se sim, reconsiderar]

### 3. View
`resources/views/sites/[slug]/index.blade.php`

Componentes a usar:
- `<x-site-header>` com emoji, title, tagline, accentColor
- `<x-result-card>` para mostrar resultado
- `<x-share-bar>` com texto de partilha
- `<x-counter-badge>` se tiver contador

Alpine.js component `[slug]()`:
- Estado inicial: [descreve]
- Acções: [descreve]

### 4. Texto de partilha WhatsApp
```
[Template do texto — inclui resultado dinâmico e URL]

Experimenta em: https://[slug].clinky.cc
```

### 5. JSON-LD
```json
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "[Nome]",
  "description": "[Description]",
  "url": "https://[slug].clinky.cc",
  "applicationCategory": "EntertainmentApplication",
  "offers": { "@type": "Offer", "price": "0" },
  "inLanguage": "pt-PT"
}
```

### 6. Adicionar ao hub
No `HomeController`, adicionar à lista de sites com `live: true`.

### 7. Adicionar ao sitemap
Adicionar URL ao `SitemapController`.

---

## Checklist antes de publicar

- [ ] Route configurado em `subdomains.php`
- [ ] Controller criado e funcional
- [ ] View mobile-first testada em 375px
- [ ] Dark mode funciona
- [ ] SEO meta tags completas
- [ ] OG image 1200×630 em `public/images/og/[slug].png`
- [ ] JSON-LD no `<head>`
- [ ] Share bar funciona e gera texto correcto
- [ ] Inputs NÃO são guardados em DB (PRIVACY.md)
- [ ] Analytics `event()` chamado nas acções principais
- [ ] Site adicionado ao hub com `live: true`
- [ ] URL adicionada ao sitemap
- [ ] Testado no mobile (iOS Safari + Chrome Android)
- [ ] PageSpeed Insights > 90 (mobile)
- [ ] Subdomínio configurado no Forge com SSL

---

## Notas adicionais

[Qualquer detalhe específico deste mini-site que não cabe nas secções acima]

---

## Exemplo de prompt completo para Claude Code

Quando passares este ficheiro ao Claude Code, usa este formato:

```
Implementa o mini-site descrito em PROMPTS/XX-nome.md para o projecto Clinky.cc.
Lê primeiro SKILL.md e todos os REFERENCES/ indicados.
Executa todas as tasks em ordem.
Confirma cada ficheiro criado.
No final, lista o que falta para ir a live (ex: OG image, .env vars).
```
