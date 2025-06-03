<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiIntegrationResource\Pages;
use App\Filament\Resources\ApiIntegrationResource\RelationManagers;
use App\Models\ApiIntegration;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiIntegrationResource extends Resource
{
    protected static ?string $model = ApiIntegration::class;
    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                KeyValue::make('credentials')
                    ->keyLabel('Параметр')
                    ->valueLabel('Значение'),
                Toggle::make('is_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('credentials')->limit(30),
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
            'index' => Pages\ListApiIntegrations::route('/'),
            'create' => Pages\CreateApiIntegration::route('/create'),
            'edit' => Pages\EditApiIntegration::route('/{record}/edit'),
        ];
    }
}
