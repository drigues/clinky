# Clinky.cc — Skill para Claude Code

## O que é o Clinky.cc
Clinky.cc é um hub de mini-sites que exploram padrões cognitivos virais — compulsão, closure, variable reward, efeito Barnum, completionism. Cada mini-site é desenhado para ser difícil de largar e fácil de partilhar. É construído em Laravel 12 com Tailwind CSS e Alpine.js.

**Antes de qualquer tarefa, lê os ficheiros de referência relevantes:**
- `REFERENCES/ARCHITECTURE.md` — estrutura do projeto, routing, deploy
- `REFERENCES/SEO.md` — SEO, Open Graph, viral mechanics
- `REFERENCES/PRIVACY.md` — regras de privacidade e dados
- `REFERENCES/COMPONENTS.md` — componentes partilhados e padrões UI

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 + PHP 8.3 |
| Frontend | Tailwind CSS + Alpine.js |
| Admin | Filament 3 |
| IA | Claude API (claude-sonnet-4-20250514) |
| Deploy | GitHub → Laravel Forge → Hetzner |
| DNS/SSL | Wildcard `*.clinky.cc` via Forge |
| Analytics | Fathom Analytics (privacy-first) |

---

## Estrutura do repositório

```
clinky.cc/
├── app/
│   ├── Http/Controllers/
│   │   ├── Hub/HomeController.php          ← homepage clinky.cc
│   │   └── Sites/
│   │       ├── DesculpometroController.php
│   │       ├── BotaoController.php
│   │       └── ...cada mini-site
│   ├── Models/
│   │   ├── SiteVisit.php                   ← analytics agregado
│   │   └── ButtonPress.php                 ← ex: contador do botão
│   └── Services/
│       └── ClaudeService.php               ← wrapper Claude API
├── resources/views/
│   ├── layouts/
│   │   ├── hub.blade.php                   ← layout homepage
│   │   └── minisite.blade.php              ← layout mini-sites
│   ├── hub/
│   │   └── home.blade.php
│   └── sites/
│       ├── desculpometro/
│       ├── botao/
│       └── ...
├── routes/
│   ├── web.php                             ← clinky.cc routes
│   └── subdomains.php                      ← incluído em web.php
├── SKILL.md
├── REFERENCES/
└── PROMPTS/
```

---

## Regras absolutas (nunca violar)

1. **Mobile-first sempre.** Tudo é desenhado para ecrã de 375px primeiro.
2. **Zero dados pessoais.** Lê `REFERENCES/PRIVACY.md` antes de qualquer form ou input.
3. **SEO completo em cada mini-site.** Lê `REFERENCES/SEO.md`.
4. **Um mini-site por prompt.** Cada prompt em `PROMPTS/` gera um mini-site completo e funcional.
5. **Share nativo.** Cada mini-site tem botão de partilha WhatsApp + Web Share API.
6. **Dark mode.** Todos os mini-sites suportam dark mode via `prefers-color-scheme` ou classe `dark`.
7. **Performance.** Sem JavaScript pesado. Alpine.js para interactividade leve. Sem React/Vue.
8. **Português PT** como língua padrão. Variantes BR aceites nos mini-sites BR↔PT.

---

## Como criar um novo mini-site

1. Lê o ficheiro de prompt correspondente em `PROMPTS/`
2. Lê `REFERENCES/ARCHITECTURE.md` para estrutura de routing
3. Lê `REFERENCES/SEO.md` para meta tags e OG
4. Lê `REFERENCES/PRIVACY.md` para regras de dados
5. Lê `REFERENCES/COMPONENTS.md` para componentes reutilizáveis
6. Executa as tasks descritas no prompt do mini-site

Para criar um novo mini-site não listado, usa `PROMPTS/TEMPLATE-new-minisite.md`.

---

## Filament Admin

O painel admin em `clinky.cc/admin` gere:
- Lista de mini-sites (activo/inactivo)
- Stats por mini-site (visitas, partilhas)
- Gerir conteúdo editável (frases, categorias)
- Logs de chamadas à Claude API

---

## Notas de deploy

- Cada subdomínio é configurado no Forge como "subdomain" do site principal
- SSL wildcard `*.clinky.cc` cobre todos os subdomínios automaticamente
- Zero-downtime deploy com `php artisan down --render=503` + `--secret`
- `.env` tem: `APP_URL`, `CLAUDE_API_KEY`, `FATHOM_SITE_ID`
