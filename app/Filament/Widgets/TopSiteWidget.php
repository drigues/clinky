<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopSiteWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $topSite = SiteVisit::where('date', '>=', now()->subWeek()->toDateString())
            ->selectRaw('site, SUM(count) as total')
            ->groupBy('site')
            ->orderByDesc('total')
            ->first();

        $totalShares = SiteVisit::where('date', '>=', now()->subWeek()->toDateString())
            ->where('event', 'like', 'share_%')
            ->sum('count');

        return [
            Stat::make('Mini-site mais visitado', $topSite?->site ?? 'N/A')
                ->description($topSite ? number_format($topSite->total, 0, ',', '.') . ' visitas' : 'Sem dados'),
            Stat::make('Partilhas esta semana', number_format($totalShares, 0, ',', '.')),
        ];
    }
}
