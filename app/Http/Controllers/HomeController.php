<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $tables = \App\Models\Table::all();
        return Inertia('Welcome', [
            'tableOptions' => $tables->map(fn($table) => [
                'id' => $table->id,
                'name' => $table->name,
                'seats' => $table->seats,
            ]),
        ]);
    }
}
