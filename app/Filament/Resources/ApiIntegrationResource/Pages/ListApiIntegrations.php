<?php

namespace App\Filament\Resources\ApiIntegrationResource\Pages;

use App\Filament\Resources\ApiIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiIntegrations extends ListRecords
{
    protected static string $resource = ApiIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
