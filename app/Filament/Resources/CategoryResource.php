<?php

namespace App\Filament\Resources;

use App\Enums\CategoryStatus;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers\VkMappingsRelationManager;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основное')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options(CategoryStatus::getOptions())
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Иерархия')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Родительская категория')
                            ->options(fn (Forms\Get $get) =>
                            Category::query()
                                ->where('type', $get('type'))
                                ->pluck('name', 'id'))
                            ->nullable()
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Интеграция с VK')
                    ->schema([
                        Forms\Components\TextInput::make('vk_category_id')
                            ->label('ID категории в VK')
                            ->numeric()
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (CategoryStatus $state) => $state->getLabel())
                    ->color(fn (CategoryStatus $state) : string => match ($state) {
                        CategoryStatus::CAT_POST => 'success',
                        CategoryStatus::CAT_PRODUCT => 'warning',
                        CategoryStatus::CAT_NEWS => 'danger',
                    }),

                TextColumn::make('parent.name')
                    ->label('Субкатегория')
                    ->placeholder('Нет'),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Товары')
                    ->visible(fn () => request()->has('type') && request('type') === 'product'),
            ])
            ->filters([
                // Фильтр по типу категории
                SelectFilter::make('Тип')
                    ->options(CategoryStatus::getOptions())
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //ChildrenRelationManager::class,
            VkMappingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
