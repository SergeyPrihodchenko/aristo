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
use Illuminate\Support\Facades\Storage;

class TelegramPostResource extends Resource
{
    protected static ?string $model = TelegramPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Telegram посты';

    public static function form(Form $form): Form
    {

        return $form->schema([

            Forms\Components\TextInput::make('title')
                ->label('Название публикации')
                ->maxLength(255),

            Forms\Components\FileUpload::make('photo')
                ->disk('tg-posts')
                ->label('Изображение')
                ->image()
                ->directory('telegram-posts'),

            Forms\Components\Select::make('schedule_type')
                ->label('Когда публиковать')
                ->options([
                    'once' => '📅 Один раз',
                    'daily' => '🔁 Каждый день',
                    'weekly' => '📆 Каждую неделю',
                    'monthly' => '🗓 Каждый месяц',
                ])
                ->default('once')
                ->live()
                ->required(),

            Forms\Components\DateTimePicker::make('scheduled_at')
                ->label('Дата и время публикации')
                ->seconds(false)
                ->visible(fn (Forms\Get $get) => $get('schedule_type') === 'once')
                ->required(fn (Forms\Get $get) => $get('schedule_type') === 'once'),

            Forms\Components\Select::make('weekday')
                ->label('День недели')
                ->options([
                    1 => 'Понедельник',
                    2 => 'Вторник',
                    3 => 'Среда',
                    4 => 'Четверг',
                    5 => 'Пятница',
                    6 => 'Суббота',
                    7 => 'Воскресенье',
                ])
                ->visible(fn (Forms\Get $get) => $get('schedule_type') === 'weekly')
                ->required(fn (Forms\Get $get) => $get('schedule_type') === 'weekly'),

            Forms\Components\TextInput::make('day_of_month')
                ->label('День месяца')
                ->numeric()
                ->minValue(1)
                ->maxValue(31)
                ->visible(fn (Forms\Get $get) => $get('schedule_type') === 'monthly')
                ->required(fn (Forms\Get $get) => $get('schedule_type') === 'monthly'),

            Forms\Components\TimePicker::make('publish_time')
                ->label('Время публикации')
                ->seconds(false)
                ->visible(fn (Forms\Get $get) => in_array($get('schedule_type'), [
                    'daily',
                    'weekly',
                    'monthly',
                ]))
                ->required(fn (Forms\Get $get) => in_array($get('schedule_type'), [
                    'daily',
                    'weekly',
                    'monthly',
                ])),

            Forms\Components\Textarea::make('message')
                ->label('Текст сообщения')
                ->rows(15)
                ->required()
                ->live(debounce: 500),

            Forms\Components\Toggle::make('is_active')
                ->label('Публикация активна')
                ->default(true)
                ->helperText('Если отключить, публикация не будет отправляться по расписанию.'),

            Forms\Components\Section::make('📱 Предпросмотр')
                ->schema([
                    Forms\Components\View::make('filament.telegram.preview')
                        ->viewData(fn (callable $get) => [
                            'message' => $get('message'),
                            'photo' => $get('photo'),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable(),

                Tables\Columns\TextColumn::make('schedule')
                    ->label('Расписание')
                    ->state(function (TelegramPost $record) {

                        return match ($record->schedule_type) {
                            'once' => '📅 ' . optional($record->scheduled_at)?->format('d.m.Y H:i'),

                            'daily' => '🔁 Каждый день в ' .
                                optional($record->publish_time)?->format('H:i'),

                            'weekly' => '📆 Каждую ' . match ($record->weekday) {
                                1 => 'понедельник',
                                2 => 'вторник',
                                3 => 'среду',
                                4 => 'четверг',
                                5 => 'пятницу',
                                6 => 'субботу',
                                7 => 'воскресенье',
                                default => '',
                            } . ' в ' . optional($record->publish_time)?->format('H:i'),

                            'monthly' => "🗓 {$record->day_of_month}-го числа в " .
                                optional($record->publish_time)?->format('H:i'),

                            default => '-',
                        };
                    })
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),

                Tables\Columns\TextColumn::make('last_sent_at')
                    ->label('Последняя отправка')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label('Отправить сейчас')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
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
            ? Storage::disk('tg-posts')->path($record->photo)
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
            Log::info('Telegram post sent successfully.');
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