# REFACTOR-to-prefix.md

Lê CLAUDE.md e REFERENCES/ARCHITECTURE.md.

O projecto vai migrar de subdomínios para directorias. Executa estas alterações em ordem:

1. Apaga `routes/subdomains.php`

2. Em `routes/web.php`, remove o `require __DIR__.'/subdomains.php'` e substitui todos os `Route::domain()` por `Route::prefix()->name()->group()` para cada mini-site existente

3. Em `.env` e `config/app.php`, remove todas as referências a `BASE_DOMAIN`

4. Em cada controller em `app/Http/Controllers/Sites/`, substitui os canonicals e URLs hardcoded por `route('nome.index')`

5. Em `resources/views/components/share-bar.blade.php` e em todas as views de mini-sites, substitui URLs de subdomínio por `route()`

6. Em `app/Http/Controllers/Hub/HomeController.php`, substitui todos os `url` dos sites por `route()`

7. Apaga a entrada `BASE_DOMAIN` do `.env` e de `config/app.php`

8. Corre `php artisan route:list` e confirma que todas as rotas aparecem com o prefixo correcto (ex: `clinky.cc/desculpometro`)

9. Corre `php artisan route:cache`

10. Actualiza CLAUDE.md: na tabela de mini-sites, muda a coluna URL de `slug.clinky.cc` para `clinky.cc/slug`

Confirma cada passo com ✅ antes de avançar. Se encontrares um URL hardcoded que não sabes substituir, mostra-o e pergunta antes de continuar.
