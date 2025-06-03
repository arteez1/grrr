<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TmCommandResource\Pages;
use App\Filament\Resources\TmCommandResource\RelationManagers;
use App\Models\TmCommand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TmCommandResource extends Resource
{
    protected static ?string $model = TmCommand::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bot_id')
                    ->relationship('bot', 'name')
                    ->required(),
                Forms\Components\TextInput::make('command')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('/'),
                Forms\Components\TextInput::make('handler_method')
                    ->required(),
                Forms\Components\Textarea::make('description'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bot.name'),
                Tables\Columns\TextColumn::make('command'),
                Tables\Columns\TextColumn::make('handler_method'),
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
            'index' => Pages\ListTmCommands::route('/'),
            'create' => Pages\CreateTmCommand::route('/create'),
            'edit' => Pages\EditTmCommand::route('/{record}/edit'),
        ];
    }
}
