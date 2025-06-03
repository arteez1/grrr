<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpamFilterResource\Pages;
use App\Filament\Resources\SpamFilterResource\RelationManagers;
use App\Models\SpamFilter;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpamFilterResource extends Resource
{
    protected static ?string $model = SpamFilter::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label('Тип фильтра')
                    ->options(SpamFilter::getTypes())
                    ->required()
                    ->reactive(),
                TextInput::make('value')
                    ->label('Значение')
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        function ($get) {
                            return function ($attribute, $value, $fail) use ($get) {
                                if ($get('type') === 'regex' && !@preg_match("/{$value}/", '')) {
                                    $fail('Некорректное регулярное выражение');
                                }
                            };
                        }
                    ]),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => SpamFilter::TYPES[$state])
                    ->color(fn (string $state): string => match ($state) {
                        'keyword' => 'primary',
                        'ip' => 'success',
                        'user_id' => 'warning',
                        'regex' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->searchable(),
                ToggleColumn::make('is_active'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSpamFilters::route('/'),
            'create' => Pages\CreateSpamFilter::route('/create'),
            'edit' => Pages\EditSpamFilter::route('/{record}/edit'),
        ];
    }
}
