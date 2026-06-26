<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:dev')]
#[Description('Command description')]
class Dev extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tgUser = \App\Models\TgUser::where('id', 1)->first();
        $user = $tgUser->user;
        dd($user);
    }
}
