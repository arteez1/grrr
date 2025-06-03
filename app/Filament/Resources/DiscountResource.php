<?php

namespace App\Filament\Resources;

use App\Enums\DiscountStatus;
use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Filament\Resources\DiscountResource\RelationManagers\ProductsRelationManager;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Маркетинг';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основные параметры')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('type')
                            ->options(DiscountStatus::getOptions())
                            ->required()
                            ->live(),

                        TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->rules([
                                fn ($get) => $get('type') === 'percentage'
                                    ? 'lte:100'
                                    : null,
                            ]),
                    ])->columns(3),

                Forms\Components\Section::make('Срок действия')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->afterOrEqual('start_date')->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('Ограничения')
                    ->schema([
                        Forms\Components\TextInput::make('max_uses')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Скидка'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Активна' => 'success',
                        'Истекла' => 'danger',
                        'Запланирована' => 'warning',
                        default => 'gray',
                    }),

//                TextColumn::make('amount')
//                    ->formatStateUsing(fn ($state, Discount $record) =>
//                    $record->type === 'percentage'
//                        ? "{$state}%"
//                        : "₽{$state}"
//                    ),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Использовано')
                    ->suffix(fn ($record) => $record->max_uses ? "/{$record->max_uses}" : ''),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(DiscountStatus::getOptions()),

                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('apply_to_products')
                    ->icon('heroicon-o-shopping-bag')
                    ->form([
                        Forms\Components\Select::make('product_ids')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                    ])
                    ->action(function (Discount $record, array $data) {
                        $record->products()->sync($data['product_ids']);
                    }),
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
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}
