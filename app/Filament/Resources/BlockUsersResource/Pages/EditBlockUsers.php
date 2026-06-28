<?php

namespace App\Filament\Resources\BlockUsersResource\Pages;

use App\Filament\Resources\BlockUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockUsers extends EditRecord
{
    protected static string $resource = BlockUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
