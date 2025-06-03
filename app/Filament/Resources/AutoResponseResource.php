<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoResponseResource\Pages;
use App\Filament\Resources\AutoResponseResource\RelationManagers;
use App\Models\AutoResponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AutoResponseResource extends Resource
{
    protected static ?string $model = AutoResponse::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('keyword')
                    ->required()
                    ->label('Ключевое слово'),
                Forms\Components\Textarea::make('response_text')
                    ->required()
                    ->label('Текст ответа'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Активно'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('keyword'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            'index' => Pages\ListAutoResponses::route('/'),
            'create' => Pages\CreateAutoResponse::route('/create'),
            'edit' => Pages\EditAutoResponse::route('/{record}/edit'),
        ];
    }
}
