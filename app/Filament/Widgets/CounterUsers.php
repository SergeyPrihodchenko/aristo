<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CounterUsers extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    // вывод статистики по пользователям, которые заходили в приложение и бронировали места за текущий день
    protected static ?string $pollingInterval = '5s';
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => [
                        \App\Models\ActionStat::whereDate('created_at', now()->toDateString())->sum('entrances'),
                        \App\Models\ActionStat::whereDate('created_at', now()->toDateString())->sum('bookings'),
                    ],
                ],
            ],
            'labels' => ['Entrances', 'Bookings'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
