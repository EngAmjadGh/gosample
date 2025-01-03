<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use Carbon\Carbon; // Import Carbon

class RemoveOldNewTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        \Log::info("RemoveOldNewTasks is running...");
        \Log::info("...");
        // Get all tasks created before today and with status 'new'
        $tasks = Task::where('status', 'NEW')
            ->where('created_at', '<', Carbon::today())
            ->get();

        // Delete the tasks
        foreach ($tasks as $task) {
            $task->is_unused = true;
            $task->save();
            $task->delete();
        }
    }
}
