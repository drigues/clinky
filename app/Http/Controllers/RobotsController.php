<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /horizon',
            'Disallow: /telescope',
            'Disallow: /storage/',
            'Disallow: /*.env',
            'Disallow: /*.log',
            '',
            'Sitemap: ' . route('sitemap'),
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
