<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;

class DriverArrivedAtPickUpLocationEvent
{
    use Dispatchable;


    /**
     * The order instance.
     *
     * @var \App\Models\Task
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
