<?php

namespace App\Filament\Resources\SpamFilterResource\Pages;

use App\Filament\Resources\SpamFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpamFilter extends EditRecord
{
    protected static string $resource = SpamFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
