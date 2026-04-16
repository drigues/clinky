<?php

use App\Http\Controllers\Hub\HomeController;
use App\Http\Controllers\PrivacidadeController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/subdomains.php';

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::get('/privacidade', [PrivacidadeController::class, 'index'])->name('privacidade');
Route::post('/api/track', [TrackController::class, 'store'])->name('track');
