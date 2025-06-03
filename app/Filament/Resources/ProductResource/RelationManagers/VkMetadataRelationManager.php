<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Enums\VkProductMarketStatus;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VkMetadataRelationManager extends RelationManager
{
    protected static string $relationship = 'vkMetadata';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('vk_product_id')
                ->label('VK ID товара')
                ->numeric()
                ->disabled(),

            Forms\Components\Fieldset::make('Размеры')
                ->schema([
                    Forms\Components\TextInput::make('width')
                        ->label('Ширина (мм)')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('height')
                        ->label('Высота (мм)')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('depth')
                        ->label('Глубина (мм)')
                        ->required()
                        ->numeric(),
                ])->columns(3),

            TextInput::make('weight')
                ->label('Вес (г)')
                ->required()
                ->numeric(),

            Forms\Components\Select::make('availability')
                ->options(VkProductMarketStatus::getOptions())
                ->required(),

            Forms\Components\TagsInput::make('vk_tags')
                ->label('Хэштеги VK')
                ->placeholder('+новый тег'),

            Forms\Components\Section::make('Подборки VK')
                ->schema([
                    Forms\Components\Select::make('vk_collections')
                        ->relationship('vkCollections', 'title')
                        ->multiple()
                        ->preload()
                ])
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vk_product_id')
                    ->label('VK ID'),

                Tables\Columns\TextColumn::make('dimensions')
                    ->label('Размеры'),

                Tables\Columns\TextColumn::make('weight')
                    ->formatStateUsing(fn ($state) => "{$state} г"),

                Tables\Columns\TextColumn::make('availability')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        0 => 'Доступен',
                        1 => 'Скрыт',
                        2 => 'Нет в наличии',
                    })
                    ->color(fn ($state) => match($state) {
                        0 => 'success',
                        1 => 'gray',
                        2 => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
