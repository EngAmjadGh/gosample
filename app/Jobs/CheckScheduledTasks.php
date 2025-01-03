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

class CheckScheduledTasks implements ShouldQueue
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

        // \Log::info("CheckScheduledTasks started.....");
        $currentDateTime = now();

         // Get scheduled tasks that meet your criteria
         $tasks = ScheduledTask::where('status', 'enabled')
         ->where('day', date('l')) // Check if it's the correct day
         ->where('selected_hour', date('H')) // Check if it's the correct hour
         ->where(function ($query) use ($currentDateTime) {
            $query->where(function ($query) use ($currentDateTime) {
                $query->where('start_date', '<=', $currentDateTime)
                    ->where('end_date', '>=', $currentDateTime);
            })->orWhere(function ($query) use ($currentDateTime) {
                $query->where('start_date', '<=', $currentDateTime->format('Y-m-d'))
                    ->where('end_date', '>=', $currentDateTime->format('Y-m-d'));
            });
        })
         ->get();


        // Create new tasks based on the conditions you need
        foreach ($tasks as $scheduledTask) {

            $today = Carbon::today();
            $dateTime = $today->setHour($scheduledTask->selected_hour)->setMinute(0)->setSecond(0);

            // Check if a task with the same properties already exists
            $existingTask = Task::where('from_location', $scheduledTask->from_location_id)
            ->where('to_location', $scheduledTask->to_location_id)
            ->where('driver_id', $scheduledTask->driver_id)
            ->where('billing_client', $scheduledTask->client_id)
            ->where('pickup_time', $dateTime)
            ->whereDate('created_at', Carbon::today())
            ->first();

            if (!$existingTask) {
                // Create a new task
                \Log::info("create new task..");
                $task = new Task;
                $task->from_location = $scheduledTask->from_location_id;
                $task->to_location = $scheduledTask->to_location_id;
                $task->billing_client = $scheduledTask->client_id;
                $task->driver_id = $scheduledTask->driver_id;
                $task->task_type = $scheduledTask->task_type;
                $task->pickup_time = $dateTime;
                // $task->pickup_time = $scheduledTask->selected_hour;
                // Set other task properties based on the ScheduledTask
                $task->save();
            }
            else{
                // \Log::info($existingTask);
                // \Log::info($existingTask->id);
                // \Log::info("task is created before");
            }   
                
            

            // \Log::info("CheckScheduledTasks ended.....");
        }
    }
}
