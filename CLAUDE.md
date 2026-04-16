# CLAUDE.md — Clinky.cc

> Este ficheiro é lido automaticamente pelo Claude Code em cada sessão.
> Mantém-no actualizado após cada tarefa concluída.

---

## O projecto

**Clinky.cc** é um hub de mini-sites virais, inúteis e partilháveis via WhatsApp e redes sociais.
Cada mini-site vive num directório próprio (`clinky.cc/desculpometro`, `clinky.cc/botao`, etc.).

**Repositório:** `clinky-cc` no GitHub
**Servidor:** Hetzner via Laravel Forge
**URL produção:** `https://clinky.cc`

---

## Lê sempre antes de qualquer tarefa

```
SKILL.md
REFERENCES/ARCHITECTURE.md
REFERENCES/SEO.md
REFERENCES/PRIVACY.md
REFERENCES/COMPONENTS.md
```

Não escrevas código sem ter lido os ficheiros acima. Se algo não estiver claro, pergunta antes de agir.

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 + PHP 8.3 |
| Frontend | Tailwind CSS + Alpine.js |
| Admin | Filament 3 (`clinky.cc/admin`) |
| IA | Claude API — modelo `claude-sonnet-4-20250514` |
| Deploy | GitHub → Laravel Forge → Hetzner |
| SSL | Let's Encrypt via Forge |
| Analytics | Fathom Analytics (cookieless, GDPR-native) |

---

## Estado do projecto

Actualiza esta secção após cada sessão concluída.

- [x] `PROMPTS/00-foundation.md` — Fundação Laravel, homepage, admin, deploy
- [x] `PROMPTS/01-desculpometro.md` — `desculpometro.clinky.cc` (Claude API)
- [x] `PROMPTS/02-05-mini-sites.md` — Botão, Nomeador, Horóscopo, Analisador de Nome
- [x] `PROMPTS/06-09-BR-PT-sites.md` — Bingo, Conversor PT/BR, Quiz, Corporativo

---

## Regras absolutas — nunca violar

1. **Mobile-first.** Tudo desenhado para 375px primeiro. Testar sempre em viewport estreito.
2. **Zero dados pessoais.** Inputs de utilizador são processados em memória e descartados. Ver `REFERENCES/PRIVACY.md`.
3. **SEO completo** em cada mini-site antes de marcar como concluído. Ver `REFERENCES/SEO.md`.
4. **Um mini-site por sessão.** Não encadear vários mini-sites na mesma execução.
5. **Share funcional.** Todo mini-site tem botão WhatsApp com texto gerado dinamicamente.
6. **Dark mode.** Suporte via `prefers-color-scheme` em todos os mini-sites.
7. **Sem Claude API desnecessária.** Se o mini-site pode funcionar com lista curada, não usar API.
8. **Confirmar antes de criar.** Antes de escrever ficheiros, lista o plano e aguarda confirmação.

---

## Como executar um prompt

Padrão a usar no início de cada sessão:

```
Lê CLAUDE.md.
Depois lê PROMPTS/[XX-nome].md.
Antes de criar qualquer ficheiro, lista o que vais criar e aguarda confirmação.
Não inventes código fora do que está nos REFERENCES/ — se tiveres dúvida, pergunta.
Após cada ficheiro criado, confirma: "✓ [caminho/ficheiro] criado".
No final, lista o que falta para este mini-site ir a live.
```

---

## Como criar um novo mini-site (futuro)

1. Duplicar `PROMPTS/TEMPLATE-new-minisite.md`
2. Renomear para `PROMPTS/XX-nome-do-site.md`
3. Preencher todas as secções do template
4. Iniciar sessão Claude Code com o padrão acima
5. Após concluído, marcar como `[x]` no Estado do projecto

---

## Estrutura de ficheiros esperada

```
clinky.cc/
├── CLAUDE.md                          ← este ficheiro
├── SKILL.md
├── REFERENCES/
│   ├── ARCHITECTURE.md
│   ├── SEO.md
│   ├── PRIVACY.md
│   └── COMPONENTS.md
├── PROMPTS/
│   ├── 00-foundation.md
│   ├── 01-desculpometro.md
│   ├── 02-05-mini-sites.md
│   ├── 06-09-BR-PT-sites.md
│   └── TEMPLATE-new-minisite.md
├── app/
│   ├── Http/Controllers/
│   │   ├── Hub/HomeController.php
│   │   └── Sites/
│   ├── Models/
│   ├── Services/
│   │   ├── ClaudeService.php
│   │   └── AnalyticsService.php
│   └── Filament/
├── resources/views/
│   ├── layouts/
│   │   ├── hub.blade.php
│   │   └── minisite.blade.php
│   ├── hub/
│   │   └── home.blade.php
│   ├── sites/
│   │   ├── desculpometro/
│   │   ├── botao/
│   │   └── ...
│   └── components/
│       ├── share-bar.blade.php
│       ├── result-card.blade.php
│       ├── site-header.blade.php
│       └── counter-badge.blade.php
├── routes/
│   └── web.php
└── public/
    └── images/
        └── og/                        ← OG images 1200×630px por mini-site
```

---

## Variáveis de ambiente necessárias

```env
APP_NAME="Clinky.cc"
APP_URL=https://clinky.cc

CLAUDE_API_KEY=
FATHOM_SITE_ID=

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=true
```

---

## Mini-sites — referência rápida

| Slug | Subdomínio | Claude API | Status |
|---|---|---|---|
| `desculpometro` | clinky.cc/desculpometro | Sim | `[x]` |
| `botao` | clinky.cc/botao | Não | `[x]` |
| `nomeador` | clinky.cc/nomeador | Não | `[x]` |
| `horoscopo` | clinky.cc/horoscopo | Não | `[x]` |
| `nome` | clinky.cc/nome | Sim | `[x]` |
| `bingo` | clinky.cc/bingo | Não | `[x]` |
| `conversor` | clinky.cc/conversor | Não | `[x]` |
| `quiz` | clinky.cc/quiz | Não | `[x]` |
| `corporativo` | clinky.cc/corporativo | Sim | `[x]` |
