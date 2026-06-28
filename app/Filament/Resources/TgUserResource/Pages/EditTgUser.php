<?php

namespace App\Filament\Resources\TgUserResource\Pages;

use App\Filament\Resources\TgUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTgUser extends EditRecord
{
    protected static string $resource = TgUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
