<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CounterUsers extends ChartWidget
{
    protected static ?string $heading = 'Посещения';

    // вывод статистики по пользователям, которые заходили в приложение и бронировали места за все время, с разбивкой по дням
    protected static ?string $pollingInterval = '15s';
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Пользователи',
                    'data' => \App\Models\ActionStat::selectRaw('DATE(created_at) as date, SUM(entrances) as entrances')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->pluck('entrances', 'date')
                        ->toArray(),
                ],
            ],
            'labels' => \App\Models\ActionStat::selectRaw('DATE(created_at) as date')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('date')
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
