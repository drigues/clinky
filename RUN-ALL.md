# RUN-ALL.md — Execução em Stack Completa

> Cola este bloco inteiro no Claude Code para executar todos os prompts em sequência.
> O Claude Code executa um, confirma, e só avança quando o anterior está completo.

---

## Prompt de execução

```
Vais construir o projecto Clinky.cc completo, executando os prompts em sequência.

REGRAS DE EXECUÇÃO:
- Lê CLAUDE.md antes de começar
- Executa um prompt de cada vez, pela ordem indicada
- Após concluir cada prompt, escreve "✅ PROMPT XX CONCLUÍDO" e lista os ficheiros criados
- Só avança para o próximo depois de confirmar que o anterior está funcional
- Se encontrares um erro, resolve-o antes de avançar — nunca saltes um prompt
- Não inventes código fora do que está nos REFERENCES/
- Se tiveres dúvida em qualquer passo, para e pergunta

ORDEM DE EXECUÇÃO:

─── FASE 1 ───────────────────────────────────────────
PROMPT 00 → Lê PROMPTS/00-foundation.md e executa todas as tasks.
Quando terminares, escreve "✅ PROMPT 00 CONCLUÍDO" e aguarda.

─── FASE 2 ───────────────────────────────────────────
PROMPT 01 → Lê PROMPTS/01-desculpometro.md e executa todas as tasks.
Quando terminares, escreve "✅ PROMPT 01 CONCLUÍDO" e aguarda.

─── FASE 3 ───────────────────────────────────────────
PROMPT 02 → Lê PROMPTS/02-05-mini-sites.md, executa APENAS a secção "PROMPT 02 — Aperta o Botão".
Quando terminares, escreve "✅ PROMPT 02 CONCLUÍDO" e aguarda.

PROMPT 03 → No mesmo ficheiro 02-05-mini-sites.md, executa APENAS a secção "PROMPT 03 — Nomeador de Grupos".
Quando terminares, escreve "✅ PROMPT 03 CONCLUÍDO" e aguarda.

PROMPT 04 → No mesmo ficheiro, executa APENAS a secção "PROMPT 04 — Horóscopo Inútil".
Quando terminares, escreve "✅ PROMPT 04 CONCLUÍDO" e aguarda.

PROMPT 05 → No mesmo ficheiro, executa APENAS a secção "PROMPT 05 — Analisador de Nome".
Quando terminares, escreve "✅ PROMPT 05 CONCLUÍDO" e aguarda.

─── FASE 4 ───────────────────────────────────────────
PROMPT 06 → Lê PROMPTS/06-09-BR-PT-sites.md, executa APENAS a secção "PROMPT 06 — Bingo do Imigrante".
Quando terminares, escreve "✅ PROMPT 06 CONCLUÍDO" e aguarda.

PROMPT 07 → No mesmo ficheiro, executa APENAS a secção "PROMPT 07 — Conversor PT ↔ BR".
Quando terminares, escreve "✅ PROMPT 07 CONCLUÍDO" e aguarda.

PROMPT 08 → No mesmo ficheiro, executa APENAS a secção "PROMPT 08 — Sou mais BR ou PT?".
Quando terminares, escreve "✅ PROMPT 08 CONCLUÍDO" e aguarda.

PROMPT 09 → No mesmo ficheiro, executa APENAS a secção "PROMPT 09 — Tradutor Corporativo".
Quando terminares, escreve "✅ PROMPT 09 CONCLUÍDO" e aguarda.

─── FINAL ────────────────────────────────────────────
Quando todos os prompts estiverem concluídos:
1. Actualiza a secção "Estado do projecto" no CLAUDE.md marcando todos como [x]
2. Cria um ficheiro DEPLOY-CHECKLIST.md com tudo o que falta para ir a live
3. Escreve "🚀 CLINKY.CC PRONTO PARA DEPLOY"

Começa agora com o PROMPT 00.
```

---

## Versão por fases (recomendada se o contexto ficar longo)

Se o Claude Code começar a perder contexto a meio, divide em 4 sessões separadas.
Inicia cada sessão nova com:

```
Lê CLAUDE.md. O projecto está no estado indicado em "Estado do projecto".
Continua a partir do próximo prompt não concluído.
Segue as mesmas regras: confirma cada prompt com ✅ antes de avançar.
```

---

## Sessão única — versão compacta

Se preferires uma versão mais curta para colar:

```
Lê CLAUDE.md e executa os PROMPTS/ pela ordem: 00 → 01 → 02 → 03 → 04 → 05 → 06 → 07 → 08 → 09.
Regras: um prompt de cada vez, confirma com ✅ PROMPT XX CONCLUÍDO antes de avançar, resolve erros antes de continuar, não inventes código fora dos REFERENCES/.
Começa pelo 00.
```
