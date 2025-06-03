<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;

use App\Filament\Resources\ProductResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\RelatedProductsRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\VkCollectionRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\VkMetadataRelationManager;
use App\Jobs\PublishProductToVkJob;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->schema([
                    // Основные поля
                    TextInput::make('name')
                        ->label('Название')
                        ->required()->minLength(1)->maxLength(125)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                            if($operation === 'edit'){
                                return;
                            }
                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->label('СЕО-ссылка')
                        ->required()->minLength(1)->maxLength(125)
                        ->unique(ignoreRecord: true),

                    TextInput::make('sku')
                        ->label('Артикул')
                        ->required()->minLength(5)
                        ->unique(ignoreRecord: true),

                    RichEditor::make('description')
                        ->columnSpanFull()->minLength(50),
                ]),

                Section::make('Цена и наличие')
                    ->schema([
                        TextInput::make('price')
                            ->numeric()->prefix('₽')
                            ->required(),

                        TextInput::make('old_price')
                            ->numeric()->prefix('₽')
                            ->nullable(),

                        TextInput::make('stock_quantity')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Публикация')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Опубликовать на сайте')
                            ->onColor('success'),

                        Toggle::make('is_published_vk')
                            ->label('Опубликовать ВКонтакте')
                            ->onColor('success')
                            ->reactive(),

                        Toggle::make('is_published_tm')
                            ->label('Опубликовать Телеграм')
                            ->onColor('success')
                            ->reactive(),
                    ])->columns(3),

                Section::make('Изображения')
                    ->schema([
                        FileUpload::make('main_image')
                            ->label('Основное изображение (сайт)')
                            ->directory('products/main')
                            ->image()
                            ->required(),

                        FileUpload::make('vk_image')
                            ->label('Изображение для VK')
                            ->directory('products/vk')
                            ->image()
                            ->visible(fn ($get) => $get('is_published_vk')),

                        FileUpload::make('tm_image')
                            ->label('Изображение для Telegram')
                            ->directory('products/telegram')
                            ->image()
                            ->visible(fn ($get) => $get('is_published_tm')),
                    ])->columns(3),



                Section::make('Категории')
                    ->schema([
//                        Select::make('categories')
//                            ->relationship('categories', 'name')
//                            ->required()
//                            ->multiple()
//                            ->preload() // Опционально: загрузка данных при прокрутке
//                            ->searchable(), // Опционально: поиск,

                        Select::make('vkCollections')
                            ->relationship('vkCollections', 'title')
                            ->visible(fn ($get) => $get('is_published_vk'))
                            ->multiple()
                            ->preload() // Опционально: загрузка данных при прокрутке
                            ->searchable(), // Опционально: поиск,
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->size(50),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->money('RUB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Опубликован'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name'),

                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                Action::make('publish_to_vk')
//                    ->label('Опубликовать в VK')
//                    ->action(function (Product $product) {
//                        PublishProductToVkJob::dispatch($product)->delay(now()->addMinutes(5));
//                        Notification::make()->success()->title('Товар отправлен на публикацию')->send();
//                    }),
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
            VkMetadataRelationManager::class,
            RelatedProductsRelationManager::class,
            CategoriesRelationManager::class,
            //RelationManagers\TagsRelationManager::class,
            VkCollectionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
