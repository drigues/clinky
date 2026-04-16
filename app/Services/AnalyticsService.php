<?php

namespace App\Services;

use App\Models\SiteVisit;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    public static function pageView(string $site): void
    {
        $key = "visits:{$site}:" . now()->format('Y-m-d');
        Cache::increment($key);
    }

    public static function event(string $site, string $event): void
    {
        $key = "events:{$site}:{$event}:" . now()->format('Y-m-d');
        Cache::increment($key);
    }

    public static function getCount(string $site, string $event = 'view', int $days = 30): int
    {
        return SiteVisit::where('site', $site)
            ->where('event', $event)
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->sum('count');
    }
}
