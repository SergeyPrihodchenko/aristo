<?php

namespace App\Filament\Resources;

use App\Models\TelegramPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\TelegramPostResource\Pages;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPostResource extends Resource
{
    protected static ?string $model = TelegramPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Telegram посты';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Заголовок')
                ->maxLength(255),

            Forms\Components\FileUpload::make('photo')
                ->disk('tg-posts')
                ->label('Картинка')
                ->image()
                ->live()
                ->directory('telegram-posts'),

            Forms\Components\Textarea::make('message')
                ->label('Сообщение')
                ->required()
                ->rows(20)
                ->live(debounce: 500)
                ->helperText('Можно использовать HTML Telegram'),

            Forms\Components\Toggle::make('is_sent')
                ->label('Отправлено')
                ->disabled(),

            Forms\Components\Section::make('📱 Предпросмотр Telegram')
                ->schema([
                    Forms\Components\View::make('filament.telegram.preview')
                        ->viewData(fn (callable $get) => [
                            'message' => $get('message'),
                            'photo' => $get('photo'),
                        ])
                ])
            ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\IconColumn::make('is_sent')->boolean(),
                Tables\Columns\TextColumn::make('sent_at')->dateTime(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label('Отправить')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->action(fn (TelegramPost $record) => self::sendToTelegram($record)),
            ])
            ->bulkActions([]);
    }

    public static function sendToTelegram(TelegramPost $record): void
    {
        $token = config('app.telegram.bot_token');
        $chatId = config('app.telegram.telegram_group_id');

        $url = "https://api.telegram.org/bot{$token}/sendPhoto";

        $caption = $record->message;

        $path = $record->photo
            ? asset('storage/tg-posts/' . $record->photo)
            : null;
        $response = Http::withOptions([
            'proxy' => config('services.proxy')
        ])
        ->attach(
            'photo',
            fopen($path, 'r'),
            basename($path)
        )->post($url, [
            'chat_id' => $chatId,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ]);

        if($response->successful()) {
            $record->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
        } else {
            Log::alert($response->json());
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTelegramPosts::route('/'),
            'create' => Pages\CreateTelegramPost::route('/create'),
            'edit' => Pages\EditTelegramPost::route('/{record}/edit'),
        ];
    }
}