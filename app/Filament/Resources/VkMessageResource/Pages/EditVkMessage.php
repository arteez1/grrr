<?php

namespace App\Filament\Resources\VkMessageResource\Pages;

use App\Filament\Resources\VkMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVkMessage extends EditRecord
{
    protected static string $resource = VkMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
