<?php

namespace App\Filament\Widgets;

use App\Models\Game;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Players extends BaseWidget
{
    protected static ?string $heading = 'Игроки за столами';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Game::query()
                    ->with(['tgUser', 'table'])
                    ->latest()
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                Tables\Columns\ImageColumn::make('tgUser.photo_url')
                    ->disk('public')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(
                        url('/images/default-avatar.png')
                    ),

                Tables\Columns\TextColumn::make('tgUser.first_name')
                    ->label('Игрок')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tgUser.username')
                    ->label('Telegram')
                    ->formatStateUsing(
                        fn ($state) => $state ? "@{$state}" : '-'
                    )
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('table.name')
                    ->label('Стол')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('seat_number')
                    ->label('Место')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Присоединился')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
            ])
            ->emptyStateHeading('Игроков пока нет')
            ->emptyStateDescription('Когда игроки появятся, они будут отображаться здесь.');
    }
}