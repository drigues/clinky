# CLAUDE.md — Clinky.cc

> Este ficheiro é lido automaticamente pelo Claude Code em cada sessão.
> Mantém-no actualizado após cada tarefa concluída.

---

## O projecto

Clinky.cc é um hub de mini-sites que exploram padrões cognitivos virais — compulsão, closure, variable reward, efeito Barnum, completionism. Cada mini-site é desenhado para ser difícil de largar e fácil de partilhar.

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
- [x] `PROMPTS/10-bolhas.md` — Rebenta as Bolhas (satisfação sensorial)
- [x] `PROMPTS/11-progresso.md` — Barra de Progresso da Vida (closure compulsion)
- [x] `PROMPTS/12-nada.md` — Nada (curiosidade pura)
- [x] `PROMPTS/13-proibido.md` — O Botão Proibido (variable reward)
- [x] `PROMPTS/14-decisao.md` — A Decisão Impossível (paralisia de análise)
- [ ] `PROMPTS/15-20-restantes.md` — Oráculo, Pânico, Tempo, Lista, Conquistas, Ouviste

---

## Regras absolutas — nunca violar

1. **Mobile-first.** Tudo desenhado para 375px primeiro.
2. **Zero dados pessoais.** Inputs processados em memória e descartados. Ver `REFERENCES/PRIVACY.md`.
3. **SEO completo** em cada mini-site antes de marcar como concluído.
4. **Um mini-site por sessão.** Nunca encadear vários na mesma execução.
5. **Share funcional.** Todo mini-site tem botão WhatsApp com texto dinâmico.
6. **Dark mode.** Suporte via `prefers-color-scheme` em todos os mini-sites.
7. **Confirmar antes de criar.** Lista o plano e aguarda confirmação.
8. **O mecanismo psicológico É o produto.** A UI serve o gatilho — não decorar por decorar.

---

## Como executar um prompt

Padrão a usar no início de cada sessão:

```
Lê CLAUDE.md.
Depois lê PROMPTS/[XX-nome].md.
Antes de criar qualquer ficheiro, lista o que vais criar e aguarda confirmação.
Não inventes código fora do que está nos REFERENCES/.
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
├── CLAUDE.md
├── SKILL.md
├── config/clinky.php              ← SOURCE OF TRUTH para homepage
├── REFERENCES/
│   ├── ARCHITECTURE.md
│   ├── SEO.md
│   ├── PRIVACY.md
│   └── COMPONENTS.md
├── PROMPTS/
│   └── ...
├── app/
├── resources/views/
├── routes/
└── public/images/og/
```

## Como adicionar um novo mini-site à homepage

1. Criar o mini-site (controller, view, route) seguindo `PROMPTS/TEMPLATE-new-minisite.md`
2. Adicionar uma entrada em `config/clinky.php` respeitando o padrão de tamanhos (soma 12 por linha)
3. Marcar `'live' => true`
4. Correr `php artisan config:clear`
5. O site aparece automaticamente na homepage

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

| Slug | URL | Claude API | Status |
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

## Mini-sites psicológicos — referência rápida

| Slug | URL | Gatilho Psicológico | Claude API | Status |
|---|---|---|---|---|
| `bolhas` | clinky.cc/bolhas | Satisfação sensorial | Não | `[x]` |
| `progresso` | clinky.cc/progresso | Closure compulsion | Não | `[x]` |
| `nada` | clinky.cc/nada | Curiosidade pura | Não | `[x]` |
| `proibido` | clinky.cc/proibido | Variable reward | Não | `[x]` |
| `decisao` | clinky.cc/decisao | Paralisia de análise | Sim | `[x]` |
| `oraculo` | clinky.cc/oraculo | Efeito Barnum | Sim | `[x]` |
| `panico` | clinky.cc/panico | Urgência falsa | Sim | `[x]` |
| `tempo` | clinky.cc/tempo | Culpa produtiva | Não | `[ ]` |
| `lista` | clinky.cc/lista | Identificação + humor | Não | `[x]` |
| `conquistas` | clinky.cc/conquistas | Completionism | Não | `[x]` |
| `ouviste` | clinky.cc/ouviste | Curiosidade auditiva | Sim | `[ ]` |
