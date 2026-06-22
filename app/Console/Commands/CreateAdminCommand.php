<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:create-admin-command')]
#[Description('Command description')]
class CreateAdminCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
            $email = $this->ask('Enter admin email:');
            $password = $this->secret('Enter admin password:');
    
            $user = \App\Models\User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => bcrypt($password),
                'is_admin' => true,
            ]);
    
            $this->info('Admin user created successfully!');
    }
}
