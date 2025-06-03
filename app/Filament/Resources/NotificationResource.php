<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Notifications\Action;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->required(),

                TextInput::make('type')
                    ->required()
                    ->placeholder('order_created'),

                Textarea::make('message')
                    ->required()
                    ->rows(3),

                Textarea::make('data')
                    ->label('Данные (JSON)')
                    ->rows(5)
                    ->json(),

                DateTimePicker::make('read_at')
                    ->label('Прочитано')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('notifiable.name')
                    ->label('Получатель'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Прочитано')
                    ->boolean(),
            ])
            ->filters([
                // Фильтр по типу уведомления
                SelectFilter::make('type')
                    ->options([
                        'order_created' => 'Новый заказ',
                        'review_pending' => 'Ожидает модерации',
                    ]),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                // Отметить как прочитанное
                Action::make('markAsRead')
                    ->icon('heroicon-o-check')
                    ->action(fn (Notification $record) => $record->markAsRead())
                    ->hidden(fn (Notification $record) => $record->read_at !== null),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
