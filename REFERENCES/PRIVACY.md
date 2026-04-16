# PRIVACY.md — Clinky.cc

## Princípio fundamental

**Não colectamos dados dos utilizadores.**

O Clinky.cc opera num modelo de privacidade máxima:
- Sem contas de utilizador
- Sem cookies de tracking
- Sem dados pessoais armazenados
- Analytics agregado e anónimo (Fathom)

---

## O que é permitido guardar

### Analytics (Fathom — sem cookies, GDPR by design)
- Páginas visitadas (URL, não utilizador)
- País de origem (apenas país, sem IP completo)
- Referrer (de onde vieram — WhatsApp, Google, etc.)
- Duração de sessão (agregada)

O Fathom Analytics **não usa cookies**, não precisa de banner de GDPR, e é compliant com GDPR, CCPA e PECR por design.

### Contadores agregados (em base de dados própria)
- Número total de gerações (ex: total de desculpas geradas)
- Número total de partilhas
- Número de cliques no botão (Aperta o Botão)
- Contadores **sem qualquer ligação a um utilizador**

```php
// CORRECTO — só o contador, sem identificador
DB::table('button_presses')->increment('total');

// INCORRECTO — nunca guardar IP, session_id, etc.
// DB::insert(['ip' => $request->ip(), 'pressed_at' => now()]);
```

---

## Inputs de texto (nomes, situações)

Alguns mini-sites pedem input de texto ao utilizador (ex: nome para análise, situação para desculpa).

**Regras:**

1. **Nunca guardar em base de dados.** O input é processado e descartado.
2. **Nunca enviar para terceiros** (excepto Claude API — ver abaixo).
3. **Nunca logar** o input em ficheiros de log.
4. Se o input for enviado para Claude API, usar a flag `do-not-train: true` no header (Anthropic honra este header).

```php
// CORRECTO — processa e descarta
public function analisar(Request $request)
{
    $nome = $request->input('nome'); // usado só nesta request
    $resultado = $this->claude->generate($systemPrompt, $nome);
    // $nome NÃO é guardado
    return response()->json(['resultado' => $resultado]);
}

// INCORRECTO — nunca fazer isto
// UserInput::create(['nome' => $nome, 'ip' => $request->ip()]);
```

---

## Claude API — headers de privacidade

Em todas as chamadas à Claude API, incluir:

```php
$response = Http::withHeaders([
    'x-api-key'              => $this->apiKey,
    'anthropic-version'      => '2023-06-01',
    'content-type'           => 'application/json',
    'anthropic-beta'         => 'do-not-train',  // Não usar para treino
])->post($this->baseUrl, [...]);
```

---

## Cookies

O Clinky.cc usa **zero cookies de tracking**.

O único cookie permitido é o cookie de sessão Laravel (`laravel_session`) que:
- É necessário para CSRF protection
- Não contém dados pessoais
- Expira com a sessão do browser

```php
// config/session.php — configuração segura
'secure' => true,        // Apenas HTTPS
'http_only' => true,     // Não acessível por JavaScript
'same_site' => 'lax',    // Protecção CSRF
'expire_on_close' => true, // Expira ao fechar o browser
```

**Sem banner de cookies necessário** — como não usamos cookies de tracking ou publicidade, não precisamos de banner de consentimento segundo a directiva ePrivacy.

---

## Local Storage / Session Storage

**Permitido** para experiência do utilizador (sem PII):

```javascript
// CORRECTO — guardar preferência de tema
localStorage.setItem('clinky_theme', 'dark');

// CORRECTO — resultado gerado para mostrar na página (não persiste no server)
sessionStorage.setItem('last_result', resultado);

// INCORRECTO — nunca guardar dados identificáveis
// localStorage.setItem('user_name', nome);
// localStorage.setItem('user_ip', ip);
```

---

## Política de Privacidade

Página obrigatória em `clinky.cc/privacidade` com:

1. **Quem somos** — Clinky.cc, projecto de entretenimento
2. **Dados que colectamos** — nenhum dado pessoal; apenas analytics agregado via Fathom
3. **Cookies** — apenas cookie de sessão técnico (não de tracking)
4. **Inputs de texto** — processados em memória, nunca guardados
5. **Claude API** — inputs processados pela Anthropic com flag de não-treino
6. **Direitos** — não temos dados teus para apagar, mas podes contactar-nos
7. **Contacto** — email de contacto

---

## GDPR Checklist

- [ ] Sem cookies de tracking → sem banner de consentimento
- [ ] Analytics com Fathom (cookieless, GDPR-native)
- [ ] Inputs de utilizador não persistidos
- [ ] Claude API com header `do-not-train`
- [ ] Política de privacidade publicada
- [ ] Cookie de sessão configurado como `secure`, `http_only`, `same_site`
- [ ] Sem dados pessoais em logs de aplicação
- [ ] Sem dados pessoais em logs de servidor (Nginx — configurar para não logar IPs completos ou anonimizar)

---

## Anonimização de logs Nginx

No Forge, configurar o Nginx para anonimizar IPs nos logs:

```nginx
# Em /etc/nginx/nginx.conf — anonimizar último octeto do IP
map $remote_addr $remote_addr_anon {
    ~(?P<ip>\d+\.\d+\.\d+)\.    $ip.0;
    ~(?P<ip>[^:]+:[^:]+):        $ip::;
    default                       0.0.0.0;
}

log_format main '$remote_addr_anon - ...';
```
