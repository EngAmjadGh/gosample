<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;

class ScheduleLogController extends Controller
{
    public function index()
    {
        $channel = 'schedule';

        $formatter = tap(Log::channel($channel)->getLogger()->getHandlers()[0]->getFormatter(), function ($formatter) {
            $formatter->includeStacktraces();
        });

        $logs = collect(Log::channel($channel)->getLogger()->getHandlers()[0]->getRecords())
            ->map(function ($record) use ($formatter) {
                return [
                    'date' => $record['datetime']->format('Y-m-d H:i:s'),
                    'level' => $record['level_name'],
                    'message' => $formatter->format($record),
                ];
            });

        return view('schedules.logs', ['logs' => $logs]);
    }
}