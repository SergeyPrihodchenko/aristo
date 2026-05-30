<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogViewerController extends Controller
{
    public function index()
    {
        $fileByDate = 'laravel-'.date('Y-m-d') . '.log';
        $logFile = storage_path('logs/' . $fileByDate);
        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found'], 404);
        }
    
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return response()->json(['logs' => array_reverse($logs)]);
    }
}
