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
use Illuminate\Support\Facades\View;

use Akaunting\Apexcharts\Chart;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');




    }

    public function index()
    {

        // calcalute number of delayed tasks

    //     $r = new Task();
    //    \Log::error( $r->delayedTasks());


        $logged_id_user = auth()->user();
        if($logged_id_user->client_id != null)
        {
            $notifications = Notifications::with(['client','driver','fromLocation','toLocation'])
            ->where('billing_client',$logged_id_user->client_id)
            ->orderBy('created_at','desc')
            ->paginate(10);

            foreach($notifications as $notification)
            {
                $notification->title = explode('\\',$notification->type )[2];
            }

            $top_drivers = Task::select('tasks.driver_id', 'drivers.name',DB::raw('count(*) as total'))
            ->leftJoin('drivers','drivers.id','=','tasks.driver_id')
            ->groupby('driver_id','drivers.name')
            ->orderBy('total','desc')
            ->paginate(5);


            $cars = Car::count();
            $tasks = Task::where('billing_client',$logged_id_user->client_id)->count();
            $samples = Sample::leftJoin('tasks','tasks.id','task_id')->where('tasks.billing_client',$logged_id_user->client_id)->count();
            $drivers = Driver::count();
            $users = User::count();
            $locations = Location::leftJoin('client_location','client_location.location_id','locations.id')
            ->where('client_location.client_id',$logged_id_user->client_id)->count();
            $clients = 1;
            return view('dashboard',[
                'top_drivers' => $top_drivers,
                'clients' => $clients,
                'notifications' => $notifications,
                'tasks' => $tasks,
                'locations' => $locations,
                'drivers' => $drivers,
                'samples' => $samples,
                'cars' => $cars,
                'users' => $users,
            ]);
        }else{
            $notifications = Notifications::with(['client','driver','fromLocation','toLocation'])
            ->orderBy('created_at','desc')
            ->paginate(10);

            foreach($notifications as $notification)
            {
                $notification->title = explode('\\',$notification->type )[2];
            }

            $top_drivers = Task::select('tasks.driver_id', 'drivers.name',DB::raw('count(*) as total'))
            ->leftJoin('drivers','drivers.id','=','tasks.driver_id')
            ->groupby('driver_id','drivers.name')
            ->orderBy('total','desc')
            ->paginate(5);

            $cars = Car::count();
            $tasks = Task::count();
            $samples = Sample::count();
            $drivers = Driver::count();
            $users = User::count();
            $locations = Location::count();
            $clients = Client::count();
            return view('dashboard',[
                'top_drivers' => $top_drivers,
                'clients' => $clients,
                'notifications' => $notifications,
                'tasks' => $tasks,
                'locations' => $locations,
                'drivers' => $drivers,
                'samples' => $samples,
                'cars' => $cars,
                'users' => $users,
            ]);
        }


    }
    public function welcome()
    {
        return view('welcome');
    }
    public function tasksdashboard(Request $request)
    {

        $tasks = Task::
        leftJoin('clients','clients.id','billing_client')
        ->select('clients.arabic_name', DB::raw('count(*) as total'));
        if($request->status != null)
        {
            $tasks =$tasks->where('tasks.status',$request->status);
        }
        if($request->driver_id != null)
        {
            $tasks =$tasks->where('driver_id',$request->driver_id);
        }
        if($request->billing_client != null)
        {
            $tasks =$tasks->where('billing_client',$request->billing_client);
        }
        if($request->from_location != null)
        {
            $tasks =$tasks->where('from_location',$request->from_location);
        }
        if($request->to_location != null)
        {
            $tasks =$tasks->where('to_location',$request->to_location);
        }
        if($request->date_from !=null && $request->date_to !=null)
        {
            $tasks =$tasks->whereBetween('tasks.created_at',
                [
                    Carbon::createFromDate($request->date_from )->toDateString(),
                    Carbon::createFromDate($request->date_to)
                        //  ->addDays(1)
                         ->toDateString()
                ]
            );
        } else{
            if ($request->date_from !=null) {
                $tasks =$tasks->where('tasks.created_at', '>=', $request->date_from );
            }
            if ($request->date_to !=null) {
                $tasks =$tasks->where('tasks.created_at', '>=', $request->date_to );
            }
        }
        $tasks_all =clone $tasks;
        $tasks_pending =clone $tasks;
        $tasks_closed =clone $tasks;

        $tasks_all =$tasks->groupby('clients.arabic_name')
        ->orderby('total','desc')
        ->get();

        $tasks_pending =$tasks_pending->groupby('clients.arabic_name')
        // ->where('tasks.status','=','NEW')
        ->whereNotIn('tasks.status',['CLOSED','NO_SAMPLES'])
        ->orderby('total','desc')
        ->get();

        $tasks_closed =$tasks_closed->groupby('clients.arabic_name')
        ->where('tasks.status','CLOSED')
        ->orderby('total','desc')
        ->get();




        $categories = [];
        $data = [];
        $data_closed = [];
        $data_pending = [];

        $index = 0;
        foreach ($tasks_all as $task) {
            $categories[]=$task->arabic_name;
            $data[]=$task->total;
            $index =$index +1;
        }
        foreach ($tasks_closed as $task) {
            $categories[]=$task->arabic_name;
            $data_closed[]=$task->total;
            $index =$index +1;
        }
        foreach ($tasks_pending as $task) {
            $categories[]=$task->arabic_name;
            $data_pending[]=$task->total;
            $index =$index +1;
        }


        $chart = (new Chart)->setType('bar')
        ->setWidth('100%')
        ->setTitle(trans('translation.Tasks_Clients'))
        ->setWidth('100%')
        ->setSubtitle(trans('translation.All_Data'))
        ->setHeight(300)
            ->setXaxisCategories($categories)
            // ->setXaxisCategories(['2001', '2002','2003','20021', '22002','20032','22','20021', '22002','20032','22','23'])
            ->setDataset('Tasks', 'donut', [
                [
                    'name'  => 'Tasks',
                    'data'  =>  $data
                ],
                [
                    'name'  => 'Closed Tasks',
                    'data'  => $data_closed
                ],
                [
                    'name'  => 'Pending Tasks',
                    'data'  => $data_pending
                ]
            ]);



        $clients = Client::all();
        $logged_id_user = auth()->user();
        if($logged_id_user->client_id != null)
        {

            $drivers = Driver::all();
            $locations = Location::all();
            return view('tasks-dashboard',compact('locations','drivers','clients','chart'));
        } else{
            $drivers = Driver::all();
            $locations = Location::all();
            return view('tasks-dashboard',compact('locations','drivers','clients','chart'));
        }
        // return view('tasks-dashboard');
    }

    public function map(Request $request)
    {


        $logged_id_user = auth()->user();
        if($logged_id_user->client_id != null)
        {
            // get driver of client
            $drivers = Driver::leftJoin('client_driver','driver_id','drivers.id')->get();
            $locations = Driver::select('drivers.*','imei','plate_number','model')
            ->leftJoin('cars','cars.driver_id','drivers.id')
            ->leftJoin('client_driver','client_driver.driver_id','drivers.id')
            ->where('client_driver.client_id',$logged_id_user->client_id)
            ->whereNotNull('cars.lat')
            ->with(['driverActiveTasks' =>function ($query) use ($logged_id_user) {
                $query->where('billing_client', $logged_id_user->client_id);
            }

            ,'driverActiveDelayedTasks','driverActiveTasks.from','driverActiveTasks.to','driverActiveTasks.samples','car','car.carTracking'])
            ;
            if($request->driver_id != null)
            {
                $locations = $locations->where('drivers.id',$request->driver_id);
            }
            if($request->imei != null)
            {
                $locations = $locations->where('cars.imei',$request->imei);
            }
            $locations = $locations->get();


            return view('map',compact('locations','drivers'));
        } else{
            $drivers = Driver::all();
            $locations = Driver::select('drivers.*','imei','plate_number','model')
            ->leftJoin('cars','cars.driver_id','drivers.id')
            ->whereNotNull('cars.lat')
            ->with(['driverActiveTasks','driverActiveDelayedTasks','driverActiveTasks.from','driverActiveTasks.to','driverActiveTasks.samples','car','car.carTracking']);
            if($request->driver_id != null)
            {
                $locations = $locations->where('drivers.id',$request->driver_id);
            }
            if($request->imei != null)
            {
                $locations = $locations->where('cars.imei',$request->imei);
            }
            if($request->plate_number != null)
            {
                $locations = $locations->where('cars.plate_number',$request->plate_number);
            }
            $locations = $locations->get();
            return view('map',compact('locations','drivers'));
        }

    }

    public function map_old(Request $request)
    {


        $logged_id_user = auth()->user();
        if($logged_id_user->client_id != null)
        {
            // get driver of client
            $drivers = Driver::leftJoin('client_driver','driver_id','drivers.id')->get();
            $locations = Driver::select('drivers.*','imei','plate_number','model')
            ->leftJoin('cars','cars.driver_id','drivers.id')
            ->leftJoin('client_driver','client_driver.driver_id','drivers.id')
            ->where('client_driver.client_id',$logged_id_user->client_id)
            ->whereNotNull('cars.lat')
            ->with(['driverActiveTasks' =>function ($query) use ($logged_id_user) {
                $query->where('billing_client', $logged_id_user->client_id);
            }

            ,'driverActiveDelayedTasks','driverActiveTasks.from','driverActiveTasks.to','driverActiveTasks.samples','car','car.carTracking'])
            ;
            if($request->driver_id != null)
            {
                $locations = $locations->where('drivers.id',$request->driver_id);
            }
            if($request->imei != null)
            {
                $locations = $locations->where('cars.imei',$request->imei);
            }
            $locations = $locations->get();


            return view('map',compact('locations','drivers'));
        } else{
            $drivers = Driver::all();
            $locations = Driver::select('drivers.*','imei','plate_number','model')
            ->leftJoin('cars','cars.driver_id','drivers.id')
            ->whereNotNull('cars.lat')
            ->with(['driverActiveTasks','driverActiveDelayedTasks','driverActiveTasks.from','driverActiveTasks.to','driverActiveTasks.samples','car','car.carTracking']);
            if($request->driver_id != null)
            {
                $locations = $locations->where('drivers.id',$request->driver_id);
            }
            if($request->imei != null)
            {
                $locations = $locations->where('cars.imei',$request->imei);
            }
            if($request->plate_number != null)
            {
                $locations = $locations->where('cars.plate_number',$request->plate_number);
            }
            $locations = $locations->get();
            return view('map',compact('locations','drivers'));
        }

    }
}
