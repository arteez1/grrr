<?php

namespace App\Filament\Resources\VkMessageResource\Pages;

use App\Filament\Resources\VkMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVkMessages extends ListRecords
{
    protected static string $resource = VkMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
