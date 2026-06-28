<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockUsersResource\Pages;
use App\Filament\Resources\BlockUsersResource\RelationManagers;
use App\Models\BlockUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlockUsersResource extends Resource
{
    protected static ?string $model = BlockUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Настройки';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Заблокированные пользователи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('tgUser.username')->label('Никнейм игрока'),
                Tables\Columns\ImageColumn::make('tgUser.photo_url')
                    ->label('Фото')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(
                        url('/images/default-avatar.png')
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            BlockUsersResource\RelationManagers\TgUserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlockUsers::route('/'),
            'create' => Pages\CreateBlockUsers::route('/create'),
            'edit' => Pages\EditBlockUsers::route('/{record}/edit'),
        ];
    }
}
