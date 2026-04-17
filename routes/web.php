<?php

use App\Http\Controllers\Hub\HomeController;
use App\Http\Controllers\PrivacidadeController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\Sites\AnalisadorNomeController;
use App\Http\Controllers\Sites\BingoController;
use App\Http\Controllers\Sites\BotaoController;
use App\Http\Controllers\Sites\ConversorController;
use App\Http\Controllers\Sites\CorporativoController;
use App\Http\Controllers\Sites\DecisaoController;
use App\Http\Controllers\Sites\DesculpometroController;
use App\Http\Controllers\Sites\ListaController;
use App\Http\Controllers\Sites\OraculoController;
use App\Http\Controllers\Sites\PanicoController;
use App\Http\Controllers\Sites\HoroscopoController;
use App\Http\Controllers\Sites\NomeadorController;
use App\Http\Controllers\Sites\BolhasController;
use App\Http\Controllers\Sites\NadaController;
use App\Http\Controllers\Sites\ProgressoController;
use App\Http\Controllers\Sites\ProibidoController;
use App\Http\Controllers\Sites\ConquistasController;
use App\Http\Controllers\Sites\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::get('/privacidade', [PrivacidadeController::class, 'index'])->name('privacidade');
Route::post('/api/track', [TrackController::class, 'store'])->name('track');

// Desculpómetro
Route::prefix('desculpometro')->name('desculpometro.')->group(function () {
    Route::get('/', [DesculpometroController::class, 'index'])->name('index');
    Route::post('/gerar', [DesculpometroController::class, 'gerar'])->name('gerar')->middleware('throttle:10,1');
});

// Aperta o Botão
Route::prefix('botao')->name('botao.')->group(function () {
    Route::get('/', [BotaoController::class, 'index'])->name('index');
    Route::post('/pressionar', [BotaoController::class, 'pressionar'])->name('pressionar')->middleware('throttle:60,1');
    Route::get('/total', [BotaoController::class, 'total'])->name('total');
});

// Nomeador de Grupos
Route::prefix('nomeador')->name('nomeador.')->group(function () {
    Route::get('/', [NomeadorController::class, 'index'])->name('index');
    Route::post('/gerar', [NomeadorController::class, 'gerar'])->name('gerar');
});

// Horóscopo Inútil
Route::prefix('horoscopo')->name('horoscopo.')->group(function () {
    Route::get('/', [HoroscopoController::class, 'index'])->name('index');
    Route::get('/{signo}', [HoroscopoController::class, 'signo'])->name('signo')->where('signo', '[a-z]+');
});

// Analisador de Nome
Route::prefix('nome')->name('nome.')->group(function () {
    Route::get('/', [AnalisadorNomeController::class, 'index'])->name('index');
    Route::post('/analisar', [AnalisadorNomeController::class, 'analisar'])->name('analisar')->middleware('throttle:10,1');
});

// Bingo do Imigrante
Route::prefix('bingo')->name('bingo.')->group(function () {
    Route::get('/', [BingoController::class, 'index'])->name('index');
});

// Conversor PT ↔ BR
Route::prefix('conversor')->name('conversor.')->group(function () {
    Route::get('/', [ConversorController::class, 'index'])->name('index');
});

// Quiz BR ou PT
Route::prefix('quiz')->name('quiz.')->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('index');
});

// Tradutor Corporativo
Route::prefix('corporativo')->name('corporativo.')->group(function () {
    Route::get('/', [CorporativoController::class, 'index'])->name('index');
    Route::post('/traduzir', [CorporativoController::class, 'traduzir'])->name('traduzir')->middleware('throttle:10,1');
});

// Rebenta as Bolhas
Route::prefix('bolhas')->name('bolhas.')->group(function () {
    Route::get('/', [BolhasController::class, 'index'])->name('index');
});

// Barra de Progresso da Vida
Route::prefix('progresso')->name('progresso.')->group(function () {
    Route::get('/', [ProgressoController::class, 'index'])->name('index');
});

// Nada
Route::prefix('nada')->name('nada.')->group(function () {
    Route::get('/', [NadaController::class, 'index'])->name('index');
    Route::get('/viewers', [NadaController::class, 'viewers'])->name('viewers');
});

// Botão Proibido
Route::prefix('proibido')->name('proibido.')->group(function () {
    Route::get('/', [ProibidoController::class, 'index'])->name('index');
    Route::post('/carregar', [ProibidoController::class, 'carregar'])->name('carregar')->middleware('throttle:60,1');
});

// A Decisão Impossível
Route::prefix('decisao')->name('decisao.')->group(function () {
    Route::get('/', [DecisaoController::class, 'index'])->name('index');
    Route::post('/escolher', [DecisaoController::class, 'escolher'])->name('escolher')->middleware('throttle:10,1');
});

// O Oráculo
Route::prefix('oraculo')->name('oraculo.')->group(function () {
    Route::get('/', [OraculoController::class, 'index'])->name('index');
    Route::post('/consultar', [OraculoController::class, 'consultar'])->name('consultar')->middleware('throttle:10,1');
});

// Coisas Que Nunca Vais Fazer
Route::prefix('lista')->name('lista.')->group(function () {
    Route::get('/', [ListaController::class, 'index'])->name('index');
});

// Modo Pânico
Route::prefix('panico')->name('panico.')->group(function () {
    Route::get('/', [PanicoController::class, 'index'])->name('index');
    Route::post('/activar', [PanicoController::class, 'activar'])->name('activar')->middleware('throttle:10,1');
});

// Conquistas do Nada
Route::prefix('conquistas')->name('conquistas.')->group(function () {
    Route::get('/', [ConquistasController::class, 'index'])->name('index');
});
