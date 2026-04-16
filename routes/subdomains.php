<?php

use App\Http\Controllers\Sites\AnalisadorNomeController;
use App\Http\Controllers\Sites\BingoController;
use App\Http\Controllers\Sites\BotaoController;
use App\Http\Controllers\Sites\ConversorController;
use App\Http\Controllers\Sites\CorporativoController;
use App\Http\Controllers\Sites\DesculpometroController;
use App\Http\Controllers\Sites\HoroscopoController;
use App\Http\Controllers\Sites\NomeadorController;
use App\Http\Controllers\Sites\QuizController;
use Illuminate\Support\Facades\Route;

// Desculpómetro
Route::domain('desculpometro.' . config('app.base_domain'))->group(function () {
    Route::get('/', [DesculpometroController::class, 'index'])->name('desculpometro.index');
    Route::post('/gerar', [DesculpometroController::class, 'gerar'])->name('desculpometro.gerar')->middleware('throttle:10,1');
});

// Aperta o Botão
Route::domain('botao.' . config('app.base_domain'))->group(function () {
    Route::get('/', [BotaoController::class, 'index'])->name('botao.index');
    Route::post('/pressionar', [BotaoController::class, 'pressionar'])->name('botao.pressionar')->middleware('throttle:60,1');
    Route::get('/total', [BotaoController::class, 'total'])->name('botao.total');
});

// Nomeador de Grupos
Route::domain('nomeador.' . config('app.base_domain'))->group(function () {
    Route::get('/', [NomeadorController::class, 'index'])->name('nomeador.index');
    Route::post('/gerar', [NomeadorController::class, 'gerar'])->name('nomeador.gerar');
});

// Horóscopo Inútil
Route::domain('horoscopo.' . config('app.base_domain'))->group(function () {
    Route::get('/', [HoroscopoController::class, 'index'])->name('horoscopo.index');
    Route::get('/{signo}', [HoroscopoController::class, 'signo'])->name('horoscopo.signo')->where('signo', '[a-z]+');
});

// Analisador de Nome
Route::domain('nome.' . config('app.base_domain'))->group(function () {
    Route::get('/', [AnalisadorNomeController::class, 'index'])->name('nome.index');
    Route::post('/analisar', [AnalisadorNomeController::class, 'analisar'])->name('nome.analisar')->middleware('throttle:10,1');
});

// Bingo do Imigrante
Route::domain('bingo.' . config('app.base_domain'))->group(function () {
    Route::get('/', [BingoController::class, 'index'])->name('bingo.index');
});

// Conversor PT ↔ BR
Route::domain('conversor.' . config('app.base_domain'))->group(function () {
    Route::get('/', [ConversorController::class, 'index'])->name('conversor.index');
});

// Quiz BR ou PT
Route::domain('quiz.' . config('app.base_domain'))->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('quiz.index');
});

// Tradutor Corporativo
Route::domain('corporativo.' . config('app.base_domain'))->group(function () {
    Route::get('/', [CorporativoController::class, 'index'])->name('corporativo.index');
    Route::post('/traduzir', [CorporativoController::class, 'traduzir'])->name('corporativo.traduzir')->middleware('throttle:10,1');
});
