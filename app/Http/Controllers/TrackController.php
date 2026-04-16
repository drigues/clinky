<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $event = $request->input('event', 'unknown');
        $allowed = ['share_whatsapp', 'share_native', 'share_copy', 'generate', 'press'];

        if (in_array($event, $allowed)) {
            $host = $request->getHost();
            $site = explode('.', $host)[0] ?? 'hub';
            AnalyticsService::event($site, $event);
        }

        return response()->json(['ok' => true]);
    }
}
