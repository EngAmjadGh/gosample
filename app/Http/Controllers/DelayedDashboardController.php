<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Car;
use App\Models\Notifications;
use App\Models\Driver;
use App\Models\Sample;
use App\Models\Task;
use App\Models\Client;
use App\Models\Location;
use DB;
use Illuminate\Support\Carbon;

use Akaunting\Apexcharts\Chart;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

use Gate;
use Symfony\Component\HttpFoundation\Response;

class DelayedDashboardController extends Controller
{

    
   

    public function index()
    {
        abort_if(Gate::denies('delayeddashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $r = new Task();
        $clients = 0;
        $pickup_delayedTasks =  $r->pickup_delayedTasks();
        $drop_off_delayedTasks =  $r->drop_off_delayedTasks();
        $cars = 0;

        $delayed_tasks_in_freezer =  $r->delayed_tasks_in_freezer();
        $delayed_tasks_delivered =  $r->delayed_tasks_delivered();
        $play_sound = 0;


        $lost_samples = Sample::where('confirmed_by_client','LOST')->get();

        if(count( $pickup_delayedTasks) > 0 || count( $drop_off_delayedTasks ) > 0)
        {
            $play_sound = 1;
        }
        //     $r = new Task();
        //    \Log::error( $r->delayedTasks());
        return view('alerts-dashboard',[
            'pickup_delayedTasks' => $pickup_delayedTasks,
            'drop_off_delayedTasks' => $drop_off_delayedTasks,
            'delayed_tasks_in_freezer' => $delayed_tasks_in_freezer,
            'delayed_tasks_delivered' => $delayed_tasks_delivered,
            'clients' => $clients,
            'lost_samples' => $lost_samples,
            'cars' => $cars,
            'play_sound' => $play_sound,
        ]);

        
    }
    public function welcome()
    {
        return view('welcome');
    }
    
    
    
    

    
}
