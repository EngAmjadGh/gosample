<?php

namespace App\CustomLog;

use Illuminate\Http\Request;
use Spatie\HttpLogger\LogWriter;
use Log;
class CustomLogWriter implements LogWriter
{
    public function logRequest(Request $request): void
    {
        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();

        // Exclude logging for the specified API route
        $excludedRoute = '/api/driver/location';
       

        $bodyAsJson = json_encode($request->except(config('http-logger.except')));

        $message = "{$method} {$uri} - {$bodyAsJson}";
       
        if ($uri === $excludedRoute || strpos($uri, '/admin/') === 0 ) {
            
        } else{
            Log::info($message);
        }
       
    }
}
