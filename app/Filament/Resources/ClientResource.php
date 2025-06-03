<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Filament\Resources\ClientResource\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\ClientResource\RelationManagers\ReviewsRelationManager;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Связанный пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),

                Forms\Components\Section::make('Контакты')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(100),

                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Социальные сети')
                    ->schema([
                        Forms\Components\TextInput::make('tm_user_id')
                            ->label('Telegram ID'),

                        Forms\Components\TextInput::make('vk_user_id')
                            ->label('VK ID')
                            ->numeric(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                TextColumn::make('user.email')
//                    ->label('Пользователь')
//                    ->sortable()
//                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('ФИО')
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Аккаунт')
                    ->placeholder('Не привязан')
                    ->sortable(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Заказы'),

//                TextColumn::make('email')
//                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->placeholder('Все клиенты'),
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
            OrdersRelationManager::class,
            ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
