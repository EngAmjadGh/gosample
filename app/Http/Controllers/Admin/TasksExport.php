<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class TasksExport implements FromView
{
    public $tasks, $temperatures;
        
    public function __construct($tasks,$temperatures) {
        $this->tasks = $tasks;
        $this->temperatures = $temperatures;
    }
    
    public function view(): View
    {
        $no_sample_tasks = DB::table('tasks')
            ->where('status', 'NO_SAMPLES')
            ->count();

            $ids = [];
            foreach($this->tasks as $task) {
                $ids[] = $task->id;
            }

        $bag_count = DB::table('samples')->whereIn('task_id', $ids)->groupBy('bag_code')->count();

          
           
            
       
        return view('report_template_excel', [
            'tasks' => $this->tasks,
            'temperatures' => $this->temperatures,
            'no_sample_tasks' => $no_sample_tasks,
            'bag_count' => $bag_count
        ]);


 
       
    }

    
}