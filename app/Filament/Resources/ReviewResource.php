<?php

namespace App\Filament\Resources;

use AnourValar\EloquentSerialize\Service;
use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Выбор типа отзыва (product, post, general)
                Select::make('type')
                    ->options([
                        'product' => 'Товар',
                        'post' => 'Новости',
                        'general' => 'Общий',
                    ])
                    ->required()
                    ->reactive(),

                // Динамический выбор объекта на основе типа
                Select::make('reviewable_id')
                    ->label('Объект')
                    ->searchable()
                    ->options(function ($get) {
                        $type = $get('type');
                        return match ($type) {
                            'product' => Product::pluck('name', 'id'),
                            'post' => Post::pluck('title', 'id'),
                            default => [],
                        };
                    })
                    ->required(),

                Select::make('client_id')
                    ->label('Клиент')
                    ->relationship('client', 'email')
                    ->searchable()
                    ->required(),

                Textarea::make('content')
                    ->required()
                    ->rows(5),

                TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),

                Toggle::make('is_approved')
                    ->label('Одобрено')
                    ->onColor('success')
                    ->offColor('danger'),

                Select::make('approved_by')
                    ->label('Модератор')
                    ->relationship('approver', 'name')
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'product' => 'Товар',
                        'post' => 'Новости',
                        'general' => 'Общий',
                    })
                    ->color(fn ($state) => match ($state) {
                        'product' => 'success',
                        'post' => 'warning',
                        'general' => 'gray',
                    }),

                TextColumn::make('reviewable.name')
                    ->label('Объект')
                    ->formatStateUsing(function (Review $record) {
                        return $record->reviewable?->name ?? $record->reviewable?->title;
                    }),

                TextColumn::make('client.email')
                    ->label('Клиент')
                    ->searchable(),

//                TextColumn::make('product.name')
//                    ->label('Товар')
//                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Оценка')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),

                IconColumn::make('is_approved')
                    ->label('Одобрено')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i'),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
