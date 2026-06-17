<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontErrorController extends Controller
{
    public function index(Request $request)
    {
        $error = $request->post('error');
        
        // Всегда преобразуем в JSON
        Log::error("FRONTEND :: " . json_encode($error, JSON_UNESCAPED_UNICODE));
    }
}