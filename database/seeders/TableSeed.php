<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Table::create([
            'seats' => 8,
            'name' => '8-max',
        ]);

        \App\Models\Table::create([
            'seats' => 10,
            'name' => '10-max',
        ]);
        
        \App\Models\Table::create([
            'seats' => 10,
            'name' => '10-max-2',
        ]);
    }
}
