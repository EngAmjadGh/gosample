<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Models\ScheduledTask;
use App\Models\Task;
use Carbon\Carbon;


class DailyScheduledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("DailyScheduledJob");

        // Get all active scheduled tasks
    $scheduledTasks = ScheduledTask::where('status', 'enabled')->get();

    foreach ($scheduledTasks as $scheduledTask) {
        // \Log::info($scheduledTask);
        // Check if the scheduled task is due today
        if ($scheduledTask->isDue()) {
            // \Log::info("new genereated tasks");
            // Create a new task based on the scheduled task's attributes
            $newTask = new Task([
                'status' => Task::STATUS_SELECT['NEW'], // Set the status to 'NEW' or any other appropriate value
                // 'start_date' => Carbon::now(), // Set the start date to the current date and time
                // Map other fields from ScheduledTask to Task here
                'driver_id' => $scheduledTask->driver_id,
                'from_location' => $scheduledTask->from_location_id,
                'to_location' => $scheduledTask->to_location_id,
                'billing_client' => $scheduledTask->client_id,
                'task_type' => $scheduledTask->task_type,
                // Add more fields as needed
                'pickup_time'=>$scheduledTask->start_date.' '.$scheduledTask->selected_hour,
            ]);
            $newTask->save(); // Save the new task to the database
        } else{
            // \Log::info("already genereated");
        }
    }


    }
}
