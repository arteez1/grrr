<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RelatedProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'relatedProducts';
    protected static ?string $title = 'Сопутствующие товары';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('related_product_id')
                    ->label('Сопутствующий товар')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('relatedProduct.main_image')
                    ->label('')
                    ->size(50),

                TextColumn::make('relatedProduct.name')
                    ->label('Название'),

                TextColumn::make('relatedProduct.price')
                    ->label('Цена')
                    ->money('RUB'),

                Tables\Columns\TextColumn::make('relatedProduct.vkMetadata.vk_product_id')
                    ->label('ID в VK')
                    ->placeholder('Не синхронизирован')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Добавить сопутствующий товар'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
