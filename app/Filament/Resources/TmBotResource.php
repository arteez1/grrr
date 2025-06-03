<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TmBotResource\Pages;
use App\Filament\Resources\TmBotResource\RelationManagers;
use App\Models\TmBot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TmBotResource extends Resource
{
    protected static ?string $model = TmBot::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('token')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->options(TmBot::getTypes())
                    ->required(),
                Forms\Components\TextInput::make('webhook_url')
                    ->url(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\KeyValue::make('settings')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'customer' => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTmBots::route('/'),
            'create' => Pages\CreateTmBot::route('/create'),
            'edit' => Pages\EditTmBot::route('/{record}/edit'),
        ];
    }
}
