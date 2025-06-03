<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VKArticleResource\Pages;
use App\Filament\Resources\VKArticleResource\RelationManagers;
use App\Models\Notification;
use App\Models\VKArticle;
use App\Services\VkArticleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VKArticleResource extends Resource
{
    protected static ?string $model = VKArticle::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'VK Интеграция';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Заголовок'),
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->label('Контент (VK-разметка)')
                    ->toolbarButtons([
                        'bold', 'italic', 'link', 'blockquote',
                    ]),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Запланировать публикацию'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Опубликовать сразу'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Заголовок'),
                Tables\Columns\TextColumn::make('short_url')
                    ->label('Ссылка'),
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Связанный пост')
                    ->url(fn ($record) => PostResource::getUrl('edit', [$record->post->id]))
                    ->disabledClick(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Опубликовано'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('Опубликовать в VK')
                    ->icon('heroicon-m-cloud-arrow-up')
                    ->action(function (VkArticle $record) {
                        try {
                            VkArticleService::publish($record);
                            Notification::make()
                                ->title('Статья успешно опубликована!')
                                ->success()
                                ->send();
                        } catch (\RuntimeException $e) {
                            Notification::make()
                                ->title('Ошибка публикации')
                                ->body($e->getMessage())
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
            'index' => Pages\ListVKArticles::route('/'),
            'create' => Pages\CreateVKArticle::route('/create'),
            'edit' => Pages\EditVKArticle::route('/{record}/edit'),
        ];
    }
}
