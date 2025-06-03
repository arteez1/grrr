<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VkCollectionResource\Pages;
use App\Filament\Resources\VkCollectionResource\RelationManagers;
use App\Filament\Resources\VkCollectionResource\RelationManagers\ProductsRelationManager;
use App\Models\VkCollection;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VkCollectionResource extends Resource
{
    protected static ?string $model = VkCollection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'VK Интеграция';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('vk_collection_id')
                            ->label('VK ID подборки')
                            ->numeric()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Синхронизация')
                    ->schema([
                        Forms\Components\DateTimePicker::make('synced_at')
                            ->label('Последняя синхронизация')
                            ->displayFormat('d.m.Y')
                            ->disabled(), // Запретить редактирование вручную

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vk_collection_id')
                    ->label('VK ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Статус'),

                TextColumn::make('synced_at')
                    ->label('Синхронизировано')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Товары'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('sync')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn (VkCollection $record) => $record->touch('synced_at')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVkCollections::route('/'),
            'create' => Pages\CreateVkCollection::route('/create'),
            'edit' => Pages\EditVkCollection::route('/{record}/edit'),
        ];
    }
}
