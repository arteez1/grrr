<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Product;
use App\Services\VkProductSyncService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VkCollectionRelationManager extends RelationManager
{
    protected static string $relationship = 'vkCollection';
    protected static ?string $title = 'Подборки VK';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Активна'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vk_collection_id')
                    ->label('ID в VK')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Активна'),

                Tables\Columns\TextColumn::make('synced_at')
                    ->label('Последняя синх.')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Только активные'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordTitle(fn ($record) => $record->title)
                    ->preloadRecordSelect()
                    ->after(function (Product $product) {
                        app(VkProductSyncService::class)->syncVkCollections($product);
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->after(function (Product $product) {
                        app(VkProductSyncService::class)->syncVkCollections($product);
                    }),

                Tables\Actions\Action::make('sync')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Product $product) {
                        app(VkProductSyncService::class)->syncVkCollections($product);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->after(function (Product $product) {
                        app(VkProductSyncService::class)->syncVkCollections($product);
                    }),
            ]);
    }
}
