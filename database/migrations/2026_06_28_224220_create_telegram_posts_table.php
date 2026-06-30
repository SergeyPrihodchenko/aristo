<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_posts', function (Blueprint $table) {
            // Тип расписания
            $table->enum('schedule_type', [
                'once',
                'daily',
                'weekly',
                'monthly',
            ])->default('once');
            // День недели (1 = понедельник, 7 = воскресенье)
            $table->unsignedTinyInteger('weekday')
                ->nullable();
            // День месяца (1-31)
            $table->unsignedTinyInteger('day_of_month')
                ->nullable();
            // Время публикации
            $table->time('publish_time')
                ->nullable();
            // Для разовой публикации
            $table->timestamp('scheduled_at')
                ->nullable();
            // Последняя успешная отправка
            $table->timestamp('last_sent_at')
                ->nullable();
            // Активна ли публикация
            $table->boolean('is_active')
                ->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_posts');
    }
};