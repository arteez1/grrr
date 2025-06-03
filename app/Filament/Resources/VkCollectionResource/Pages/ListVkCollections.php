<?php

namespace App\Filament\Resources\VkCollectionResource\Pages;

use App\Filament\Resources\VkCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVkCollections extends ListRecords
{
    protected static string $resource = VkCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
