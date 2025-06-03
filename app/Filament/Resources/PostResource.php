<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основное')
                    ->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        Select::make('type')
                            ->options([
                                'article' => 'Статья',
                                'news' => 'Новость',
                            ])
                            ->required(),
                        Textarea::make('content')->rows(10)->required(),
                        Textarea::make('short_content')->rows(3)->label('Краткое описание'),
                    ]),

                Section::make('Изображения')
                    ->schema([
                        FileUpload::make('main_image')
                            ->label('Основное изображение')
                            ->directory('posts/main')
                            ->image(),

                        FileUpload::make('vk_image')
                            ->label('Для VK')
                            ->directory('posts/vk')
                            ->image()
                            ->visible(fn ($get) => $get('is_published_vk')),

                        FileUpload::make('tm_image')
                            ->label('Для Telegram')
                            ->directory('posts/telegram')
                            ->image()
                            ->visible(fn ($get) => $get('is_published_tm')),
                    ]),

                Select::make('vk_article_id')
                    ->label('Статья VK')
                    ->relationship('vkArticle', 'title')
                    ->searchable()
                    ->nullable(),

                Section::make('Публикация')
                    ->schema([
                        Toggle::make('is_published')->label('На сайте'),
                        Toggle::make('is_published_vk')->label('В VK')->reactive(),
                        Toggle::make('is_published_tm')->label('В Telegram')->reactive(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main_image')
                    ->label('Изображение')
                    ->width(100)
                    ->height(60),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'article' => 'success',
                        'news' => 'warning',
                    }),

                IconColumn::make('is_published_vk')
                    ->label('VK')
                    ->boolean(),

                IconColumn::make('is_published_tm')
                    ->label('TG')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                Action::make('publish_to_telegram')
//                    ->label('Опубликовать в Telegram')
//                    ->action(function (Post $post) {
//                        PublishPostToTelegramJob::dispatch($post);
//                    })
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
