<?php

namespace App\Filament\Resources\TmCommandResource\Pages;

use App\Filament\Resources\TmCommandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTmCommand extends EditRecord
{
    protected static string $resource = TmCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
