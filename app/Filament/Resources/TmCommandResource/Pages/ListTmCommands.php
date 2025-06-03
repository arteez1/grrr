<?php

namespace App\Filament\Resources\TmCommandResource\Pages;

use App\Filament\Resources\TmCommandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTmCommands extends ListRecords
{
    protected static string $resource = TmCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
