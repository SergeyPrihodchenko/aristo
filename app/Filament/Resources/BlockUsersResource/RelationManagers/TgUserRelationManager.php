<?php

namespace App\Filament\Resources\BlockUsersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TgUserRelationManager extends RelationManager
{
    protected static string $relationship = 'TgUser';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('username')
            ->columns([
                Tables\Columns\TextColumn::make('username')->label('Никнейм игрока'),
                Tables\Columns\TextColumn::make('first_name')->label('Имя игрока'),
                Tables\Columns\TextColumn::make('last_name')->label('Фамилия игрока'),
                Tables\Columns\ImageColumn::make('photo_url')
                    ->label('Фото игрока')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(
                        url('/images/default-avatar.png')
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
