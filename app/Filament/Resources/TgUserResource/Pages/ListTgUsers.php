<?php

namespace App\Filament\Resources\TgUserResource\Pages;

use App\Filament\Resources\TgUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTgUsers extends ListRecords
{
    protected static string $resource = TgUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
