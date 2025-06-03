<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VkMessageResource\Pages;
use App\Filament\Resources\VkMessageResource\RelationManagers;
use Filament\Notifications\Notification;
use App\Models\VkMessage;
use App\Services\VkMessageService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VkMessageResource extends Resource
{
    protected static ?string $model = VkMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user_id')->label('ID пользователя')->disabled(),
                Textarea::make('message')->disabled(),
                Select::make('direction')->options(['in' => 'Входящее', 'out' => 'Исходящее']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label('ID пользователя')
                    ->searchable(),

                TextColumn::make('message')
                    ->label('Текст')
                    ->limit(50),

                TextColumn::make('direction')
                    ->label('Направление')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'primary',
                    }),

                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->options([
                        'in' => 'Входящие',
                        'out' => 'Исходящие',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('reply')
                    ->label('Ответить')
                    ->icon('heroicon-o-reply')
                    ->form([
                        Textarea::make('message')->label('Текст ответа')->required(),
                    ])
                    ->action(function (VkMessage $record, array $data) {

                        try {
                            app(VkMessageService::class)->sendMessage(
                                userId: $record->user_id,
                                message: $data['message']
                            );
                            Notification::make()
                                ->title('Ответ отправлен!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Ошибка: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVkMessages::route('/'),
            'create' => Pages\CreateVkMessage::route('/create'),
            'edit' => Pages\EditVkMessage::route('/{record}/edit'),
        ];
    }
}
