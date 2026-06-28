<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BookingWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    // вывод статистики по пользователям, которые бронировали места за все время, с разбивкой по дням
    protected static ?string $pollingInterval = '5s';
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => \App\Models\ActionStat::selectRaw('DATE(created_at) as date, SUM(bookings) as bookings')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->pluck('bookings', 'date')
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
