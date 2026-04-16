<?php

namespace App\Filament\Resources;

use App\Models\SiteVisit;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteVisitResource extends Resource
{
    protected static ?string $model = SiteVisit::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Analytics';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('count')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('site')
                    ->options(fn () => SiteVisit::distinct()->pluck('site', 'site')->toArray()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SiteVisitResource\Pages\ListSiteVisits::route('/'),
        ];
    }
}
