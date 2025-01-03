<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Task;
use App\Models\Sample;
use App\Models\Swap;
use Carbon\Carbon;

class HeaderDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.topbar', function ($view) {
            $user = auth()->user();
            $user_client_id = null;
            if (isset($user->id) && $user->client_id != null) {
                $user_client_id = $user->id;
            }
            // $newTasksCount = 10;
            $newTasksCount = Task::where('status', 'NEW');
            $newSwapTasksCount = Swap::where('status', 'NEW');
            if ($user_client_id){
                $newTasksCount = $newTasksCount->where('billing_client',$user_client_id)->count();
                $newSwapTasksCount = $newSwapTasksCount->count();
            }else{
                $newTasksCount = $newTasksCount->count();
                $newSwapTasksCount = $newSwapTasksCount->count();
            }




            $r = new Task();
            $clients = 0;
            $fourDaysAgo = Carbon::now()->subDays(4);
            $pickup_delayedTasks =  $r->pickup_delayedTasks($user_client_id);
            $drop_off_delayedTasks =  $r->drop_off_delayedTasks($user_client_id);
            $cars = 0;

            $delayed_tasks_in_freezer =  $r->delayed_tasks_in_freezer($user_client_id);
            $delayed_tasks_delivered =  $r->delayed_tasks_delivered($user_client_id);
            $play_sound = 0;


            $lost_samples = Sample::where('samples.confirmed_by_client','LOST');
            if ($user_client_id){
                $lost_samples = $lost_samples->leftjoin('tasks','tasks.id','=','samples.task_id')->where('tasks.billing_client',$user_client_id);
            }
            $lost_samples = $lost_samples->where('samples.created_at', '>=', $fourDaysAgo)->get();


            $delayed_count = Task::where('status', '=', 'CLOSED')
            ->where('created_at', '>=', $fourDaysAgo)
            ->count();

            $result=  count($pickup_delayedTasks)+ count($drop_off_delayedTasks)+
            count($delayed_tasks_in_freezer)+ count($delayed_tasks_delivered)+ count($lost_samples);
            $view->with('newTasksCount',$newTasksCount);
            $view->with('newSwapTasksCount',$newSwapTasksCount);
            $view->with('delayed_count',$result);
            $view->with('lost_samples', $lost_samples);
            $view->with('pickup_delayedTasks', $pickup_delayedTasks);
            $view->with('drop_off_delayedTasks', $drop_off_delayedTasks);
            $view->with('delayed_tasks_in_freezer', $delayed_tasks_in_freezer);
            $view->with('delayed_tasks_delivered', $delayed_tasks_delivered);
        });
    }
}
