<?php

namespace App\Filament\Resources\TmBotResource\Pages;

use App\Filament\Resources\TmBotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTmBot extends EditRecord
{
    protected static string $resource = TmBotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
