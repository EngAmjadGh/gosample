<?php

namespace App\Console\Commands;

use App\Events\CurrentDriverLocationEvent;
use App\Models\Task;
use Illuminate\Console\Command;

class TakasiCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'takasi:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get all in_Freezer & out_freezer tasks and takasi = YES and created at = today or yesterday
        $tasks = Task::whereIn('status',['IN_FREEZER','OUT_FREEZER'])->where('takasi','YES')->get();
        foreach ($tasks as $task)
        {
            // event(new CurrentDriverLocationEvent($task));
        }

        // foreach task, send event to MOH


        return 0;
    }
}
