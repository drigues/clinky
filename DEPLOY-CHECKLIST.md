# DEPLOY-CHECKLIST.md — Clinky.cc

## Antes do deploy

### Variáveis de ambiente
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://clinky.cc`
- [ ] `BASE_DOMAIN=clinky.cc`
- [ ] `CLAUDE_API_KEY` configurada
- [ ] `FATHOM_SITE_ID` configurado
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_SAME_SITE=lax`
- [ ] `SESSION_EXPIRE_ON_CLOSE=true`
- [ ] `DB_*` configuradas (SQLite ou PostgreSQL)

### DNS (Cloudflare ou registar)
- [ ] A record `clinky.cc` → IP do servidor
- [ ] CNAME `*.clinky.cc` → `clinky.cc`
- [ ] SPF, DKIM, DMARC para email (ver SEC-1)

### SSL
- [ ] Certificado wildcard `*.clinky.cc` via Let's Encrypt (Forge)
- [ ] Verificar HTTPS em todos os subdomínios

### Base de dados
- [ ] `php artisan migrate` executado
- [ ] Seed do ButtonPress: `php artisan tinker` → `\App\Models\ButtonPress::create(['total' => 0])`

### Assets
- [ ] `npm run build` executado
- [ ] Verificar que Tailwind CSS e Alpine.js carregam

### Admin
- [ ] Criar utilizador admin: `php artisan tinker` → criar User com Filament access
- [ ] Verificar acesso a `clinky.cc/admin`

---

## Verificação pós-deploy

### Cada mini-site
- [ ] `desculpometro.clinky.cc` — gera desculpa (Claude API)
- [ ] `botao.clinky.cc` — botão funciona, contador incrementa
- [ ] `nomeador.clinky.cc` — gera 3 nomes por categoria
- [ ] `horoscopo.clinky.cc` — mostra 12 signos, cada um com previsão
- [ ] `nome.clinky.cc` — analisa nome (Claude API)
- [ ] `bingo.clinky.cc` — grelha 5×5 funciona, localStorage persiste
- [ ] `conversor.clinky.cc` — pesquisa e toggle PT/BR funcionam
- [ ] `quiz.clinky.cc` — 5 perguntas, resultado com percentagem
- [ ] `corporativo.clinky.cc` — traduz jargão (dicionário + Claude API)

### Em cada mini-site verificar
- [ ] SEO: title, description, OG tags, JSON-LD
- [ ] Share: botão WhatsApp com texto dinâmico
- [ ] Dark mode funcional
- [ ] Mobile (375px) sem overflow
- [ ] Link "← clinky.cc" funciona

### Hub
- [ ] `clinky.cc` — homepage mostra todos os 9 mini-sites como live
- [ ] Links para subdomínios funcionam
- [ ] `clinky.cc/sitemap.xml` retorna XML válido
- [ ] `clinky.cc/robots.txt` bloqueia /admin, /storage, etc.
- [ ] `clinky.cc/privacidade` carrega

### Segurança
- [ ] `APP_DEBUG=false` confirmado
- [ ] Rate limiting activo nas rotas POST
- [ ] CSRF token em todos os formulários
- [ ] Security headers (X-Frame-Options, X-Content-Type-Options, etc.)

### Performance
- [ ] Fathom Analytics a registar visitas
- [ ] Claude API a responder (testar desculpometro + nome + corporativo)

---

## OG Images pendentes

Criar imagens 1200×630px para:
- [ ] `public/images/og/default.png`
- [ ] `public/images/og/bingo.png`
- [ ] `public/images/og/conversor.png`
- [ ] `public/images/og/quiz.png`
- [ ] `public/images/og/corporativo.png`
- [ ] (verificar se desculpometro, botao, nomeador, horoscopo, nome já existem)
