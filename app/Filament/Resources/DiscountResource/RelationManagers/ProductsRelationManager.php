<?php

namespace App\Filament\Resources\DiscountResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('products', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->size(50),

                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('price')
                    ->money('RUB'),

                Tables\Columns\TextColumn::make('discount_price')
                    ->money('RUB')
                    ->state(function ($record) {
                        $discount = $this->getOwnerRecord();
                        return $discount->type === 'percentage'
                            ? $record->price * (1 - $discount->amount / 100)
                            : max(0, $record->price - $discount->amount);
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
