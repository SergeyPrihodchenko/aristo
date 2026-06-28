<?php

namespace App\Filament\Resources\BlockUsersResource\Pages;

use App\Filament\Resources\BlockUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlockUsers extends ListRecords
{
    protected static string $resource = BlockUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
