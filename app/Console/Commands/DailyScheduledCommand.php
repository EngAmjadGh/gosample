<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyScheduledCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'daily:scheduled-command';
    protected $signature = 'daily-schedule:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Description of your daily scheduled command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("DailyScheduledCommand");
        return Command::SUCCESS;
    }
}
