<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;
use App\Models\Sample;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Task;
class TaskDelayedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taskDelayed:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check delayed tasks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $tasks = Task::whereRaw('pickup_time < collection_date')
        ->where('delayed_reason', '=', '')
        ->get();

        foreach ($tasks as $task) {
            $task->delayed_reason = 'pickup_delayed';  
            $driver = Driver::find($task->driver_id);
            $driver->sendNotification( 'Delayed Task', 'You have delay in pickup task',[$driver->fcm_token],$task,'no_action');
            $task->save();
        }

        $tasks1   = Task::whereRaw('dropoff_time < close_date')
        ->where('delayed_reason', '=', '')
        ->get();

        foreach ($tasks1 as $task) {
            $task->delayed_reason = 'dropoff_delayed'; 
            $driver = Driver::find($task->driver_id);
            $driver->sendNotification( 'Delayed Task', 'You have delay in drop off task',[$driver->fcm_token],$task,'no_action');
            $task->save(); 
        }
        return Command::SUCCESS;
    }
}
