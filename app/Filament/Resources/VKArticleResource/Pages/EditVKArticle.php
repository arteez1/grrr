<?php

namespace App\Filament\Resources\VKArticleResource\Pages;

use App\Filament\Resources\VKArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVKArticle extends EditRecord
{
    protected static string $resource = VKArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
