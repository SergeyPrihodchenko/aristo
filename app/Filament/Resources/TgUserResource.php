<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TgUserResource\Pages;
use App\Filament\Resources\TgUserResource\RelationManagers;
use App\Models\TgUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TgUserResource extends Resource
{
    protected static ?string $model = TgUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('telegram_id')->label('Telegram ID')->sortable(),
                Tables\Columns\TextColumn::make('username')->label('Полное имя')->sortable(),
                Tables\Columns\TextColumn::make('first_name')->label('Имя')->sortable(),
                Tables\Columns\TextColumn::make('last_name')->label('Фамилия')->sortable(),
                Tables\Columns\ImageColumn::make('photo_url')
                    ->label('Фото')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(
                        url('/images/default-avatar.png')
                    ),
                Tables\Columns\TextColumn::make('created_at')->label('Дата создания')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTgUsers::route('/'),
            'create' => Pages\CreateTgUser::route('/create'),
            'edit' => Pages\EditTgUser::route('/{record}/edit'),
        ];
    }
}
