<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Клиент')
                            ->relationship('client', 'full_name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options(OrderStatus::getOptions())
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Детали заказа')
                    ->schema([
                        Forms\Components\Select::make('delivery_method')
                            ->options([
                                'pickup' => 'Самовывоз',
                                'courier' => 'Курьер'
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Наличные',
                                'card' => 'Карта',
                                'online' => 'Онлайн'
                            ])
                            ->required(),

                        Forms\Components\Select::make('discount_id')
                            ->relationship('discount', 'code')
                            ->searchable(),
                    ])->columns(3),

                /*TextInput::make('total_amount')
                    ->numeric()
                    ->required()
                    ->prefix('₽'),*/


                /*Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->label('Товар')
                            ->options(Product::pluck('name', 'id'))
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        TextInput::make('price_at_purchase')
                            ->numeric()
                            ->prefix('₽')
                            ->required(),
                    ])
                    ->columns(3),*/
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.full_name')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->money('RUB')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(OrderStatus::getOptions()),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::getOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('syncToVk')
                    ->icon('heroicon-o-arrow-up')
                    ->action(fn (Order $order) => app(VkOrderService::class)->syncOrder($order)),
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
            //RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
