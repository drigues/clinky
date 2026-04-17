<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $sites    = collect(config('clinky.sites'))->where('live', true)->values();
        $featured = $sites->firstWhere('featured', true);
        $others   = $sites->reject(fn ($s) => ($s['featured'] ?? false) === true)->values();

        return view('hub.home', compact('featured', 'others'));
    }
}
