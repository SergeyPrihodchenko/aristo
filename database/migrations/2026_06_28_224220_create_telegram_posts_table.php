<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_posts', function (Blueprint $table) {
            // Тип расписания
            $table->enum('schedule_type', [
                'once',
                'daily',
                'weekly',
                'monthly',
            ])->default('once')->after('photo');

            // День недели (1 = понедельник, 7 = воскресенье)
            $table->unsignedTinyInteger('weekday')
                ->nullable()
                ->after('schedule_type');

            // День месяца (1-31)
            $table->unsignedTinyInteger('day_of_month')
                ->nullable()
                ->after('weekday');

            // Время публикации
            $table->time('publish_time')
                ->nullable()
                ->after('day_of_month');

            // Для разовой публикации
            $table->timestamp('scheduled_at')
                ->nullable()
                ->after('publish_time');

            // Последняя успешная отправка
            $table->timestamp('last_sent_at')
                ->nullable()
                ->after('scheduled_at');

            // Активна ли публикация
            $table->boolean('is_active')
                ->default(true)
                ->after('last_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('telegram_posts', function (Blueprint $table) {
            $table->dropColumn([
                'schedule_type',
                'weekday',
                'day_of_month',
                'publish_time',
                'scheduled_at',
                'last_sent_at',
                'is_active',
            ]);
        });
    }
};