<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalVisitsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = SiteVisit::where('date', today())->sum('count');
        $week = SiteVisit::where('date', '>=', now()->subWeek()->toDateString())->sum('count');
        $month = SiteVisit::where('date', '>=', now()->subMonth()->toDateString())->sum('count');

        return [
            Stat::make('Visitas hoje', number_format($today, 0, ',', '.')),
            Stat::make('Visitas esta semana', number_format($week, 0, ',', '.')),
            Stat::make('Visitas este mês', number_format($month, 0, ',', '.')),
        ];
    }
}
