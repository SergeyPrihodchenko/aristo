<?php

namespace App\Filament\Resources\TelegramPostResource\Pages;

use App\Filament\Resources\TelegramPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTelegramPosts extends ListRecords
{
    protected static string $resource = TelegramPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
