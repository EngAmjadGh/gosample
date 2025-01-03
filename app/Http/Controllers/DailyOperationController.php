<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Car;
use App\Models\Sample;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Task;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Spatie\Period\Period;
use Spatie\Period\Precision;

use App\Models\User;
use App\Models\Notifications;
use DB;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;


class DailyOperationController extends Controller
{

    public function index(Request $request)
    {
        $logged_id_user = auth()->user();
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Task::with(['from', 'to', 'client', 'driver', 'car'])->select(sprintf('%s.*', (new Task())->table));
            
            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }
           
            $table = Datatables::of($query)

            ->filter(function ($query)  use ($request) {
                if ($request->status !=null) {
                    $query->where('status', '=',  $request->status );
                }
                if ($request->driver_id !=null) {
                    $query->where('driver_id', '=', $request->driver_id );
                }
                if ($request->billing_client !=null) {
                    $query->where('billing_client', '=', $request->billing_client );
                }
                if ($request->from_location !=null) {
                    $query->where('from_location', '=', $request->from_location );
                }
                if ($request->to_location !=null) {
                    $query->where('to_location', '=', $request->to_location );
                }
                if ($request->keyword !=null) {
                    $query->where('tasks.id', '=', $request->keyword );
                }
                if ($request->delayed_reason !=null) {
                    $query->where('tasks.delayed_reason', '=', $request->delayed_reason );
                }
                if($request->date_from !=null && $request->date_to !=null)
                {
                    $query->whereBetween('tasks.created_at',
                        [
                            Carbon::createFromDate($request->date_from )->toDateString(),
                            Carbon::createFromDate($request->date_to)
                            //  ->addDays(1)
                             ->toDateString()
                        ]
                        );
                } else{
                    if ($request->date_from !=null) {
                        $query->where('tasks.created_at', '>=', $request->date_from );
                    }
                    if ($request->date_to !=null) {
                        $query->where('tasks.created_at', '>=', $request->date_to );
                    }
                }

                if ($request->task_date !=null) {
                    $query->whereDate('tasks.created_at','=', $request->task_date );
                }
            });
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'task_show';
                $editGate = 'task_edit';
                $deleteGate = 'task_delete';
                $crudRoutePart = 'tasks';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('from_location_name', function ($row) {
                return $row->from ? $row->from->name : '';
            });

            $table->addColumn('to_location_name', function ($row) {
                return $row->to ? $row->to->name : '';
            });

            $table->addColumn('billing_client_status', function ($row) {
                return $row->client ? $row->client->english_name : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->addColumn('close_date', function ($row) {
                return $row->close_date ? $row->close_date : '';
            });

            $table->addColumn('plate_number', function ($row) {
                return $row->car ? $row->car->plate_number : '';
            });
            $table->addColumn('delayed_reason', function ($row) {
                return $row->delayed_reason ? $row->delayed_reason : '';
            });

            $table->addColumn('hours', function ($row) {
                return $row->close_date ? parent::hoursandmins(Period::make($row->from_location_arrival_time,$row->close_date,  Precision::MINUTE())->length(), '%02d Hours, %02d Minutes')
                : '';
            });

            $table->editColumn('close_hour', function ($row) {
                return $row->close_hour ? $row->close_hour : '';
            });
            
            $table->editColumn('box_count', function ($row) {
                return $row->box_count ? $row->box_count : '';
            });
            $table->editColumn('sample_count', function ($row) {
                return $row->sample_count ? $row->sample_count : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? Task::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('task_type', function ($row) {
                return $row->task_type ? Task::TASK_TYPE_SELECT[$row->task_type] : '';
            });
            $table->editColumn('confirmed_by_client', function ($row) {
                return $row->confirmed_by_client ? Task::CONFIRMED_BY_CLIENT_SELECT[$row->confirmed_by_client] : '';
            });
            $table->editColumn('ayenati', function ($row) {
                return $row->ayenati ? Task::AYENATI_SELECT[$row->ayenati] : '';
            });
            $table->editColumn('takasi', function ($row) {
                return $row->takasi ? Task::TAKASI_SELECT[$row->takasi] : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Task::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('added_by', function ($row) {
                return $row->added_by ? $row->added_by : '';
            });
            $table->editColumn('from_location_arrival_time', function ($row) {
                return $row->from_location_arrival_time ? $row->from_location_arrival_time : '';
            });
            $table->editColumn('deliver_signature', function ($row) {
                return $row->deliver_signature ? $row->deliver_signature : '';
            });
            $table->editColumn('deliver_confirmation_code', function ($row) {
                return $row->deliver_confirmation_code ? $row->deliver_confirmation_code : '';
            });
            $table->editColumn('confirmation_code', function ($row) {
                return $row->confirmation_code ? $row->confirmation_code : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('takasi_number', function ($row) {
                return $row->takasi_number ? $row->takasi_number : '';
            });
            $table->editColumn('to_takasi_number', function ($row) {
                return $row->to_takasi_number ? $row->to_takasi_number : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'from_location', 'to_location', 'billing_client', 'driver', 'car']);

            return $table->make(true);
        } else{
            \Log::error("no ajax");
        }


        
        
        if( $logged_id_user->client_id != null)
        {
            \Log::info("message");
                $clients = Client::where('id', $logged_id_user->client_id)->get();
                $locations = Location::select('locations.*')
                ->leftJoin('client_location','client_location.location_id','locations.id')
                ->where('client_location.client_id',$logged_id_user->client_id)
                ->get();
                $drivers = Driver::all();
        } else{
            $clients = Client::all();
            $locations = Location::all();
            $drivers = Driver::all();
        }
        return view('daily-operation',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    

    }
    

    public function export()
    {
        $billing_client = 25;
        if($billing_client){
            $clint = Client::find($billing_client);
            $client_logo = $clint->logo;
            // dd($client_logo );
        }else{
            $client_logo = null;
        }
        $sample_barcode = '';//$sample_barcode;
        $keyWord = '';//'%'.$keyWord .'%';
        $from = '2022-11-10';
        $to = '2022-11-10';
        $from_location = null;
        $to_location = null;
        $task_type = '"SAMPLE"';
        $driver_id = 85;
        $status = 'CLOSED';
        // $status = '';
        if($from == $to)
        {
            $reportDate =  $to;
        } else{
            $reportDate = 'From '. $from. '- To '. $to;
        }
        $to =  $to.' 23:59:59';
        $from =  $from.' 00:00:00';
        $query = 'select  tasks.id as id  ,  from_location.name as "from_organization_name",  tasks.from_location_arrival_time as "from_location_arrival_time",
                                    TIMESTAMPDIFF(Minute, tasks.from_location_arrival_time,  tasks.collection_date) as "from_stay_time",
                                    to_location.name as "to_organization_name",
                                    tasks.to_location_arrival_time as "to_location_arrival_time",
                                    TIMESTAMPDIFF(Minute, tasks.to_location_arrival_time, tasks.close_date ) as "to_stay_time",
                                    TIMESTAMPDIFF(Minute,  tasks.from_location_arrival_time, tasks.close_date) as "trip_duration",
                                    GROUP_CONCAT(samples.bag_code) as "bag_code",
                                    GROUP_CONCAT(samples.temperature_type) as "temperature_type",
                                    count(samples.id) as "bags_count",
                                    tasks.confirmed_by_client,
                                    tasks.confirmation_time
                                    from tasks
                                    left join drivers on drivers.ID = tasks.driver_id
                                    left join locations as from_location on from_location.ID = tasks.from_location
                                    left join locations as to_location on to_location.ID = tasks.to_location
                                    left join samples as samples on samples.task_id = tasks.id
                                    WHERE tasks.deleted_at is null and tasks.id > 1 ';


        $billing_client=25;
        if($billing_client !=null)
        {
            $billing_client=$billing_client;
            $query =  $query.' and tasks.billing_client= '.$billing_client;
        }

        if($from_location !=null)
        {
            $query =  $query.' and tasks.from_location= '.$from_location;
        }

        if($to_location !=null)
        {
            $query =  $query.' and tasks.to_location= '.$to_location;
        }


        if($task_type !=null)
        {
            $query =  $query.' and tasks.task_type= '.$task_type;
        }


        if($from !=null && $to !=null )
        {
            $query =  $query." and tasks.created_at BETWEEN '".date('Y-m-d H:i:s', strtotime( $from))."' and '".date('Y-m-d H:i:s', strtotime( $to))." '";
        }

        if($status !=null)
        {
            $query =  $query." and tasks.status= '".$status."'";
        }

        \Log::info($query);
        $tasks = DB::select($query.' group by tasks.id order by from_location.name asc, tasks.from_location_arrival_time asc;');
        \Log::info($tasks);
        $roomBags = 0;
        $refBags = 0;
        $frozenBags = 0;

        $roomSamples = 0;
        $refSamples = 0;
        $frozenSamples = 0;


        $summaryReport = '';
        if( $billing_client== 26) // mdlab
        {
            // this is mdlab report request by mtc
            $summaryReport = collect($tasks)
                ->groupBy('from_organization_name')
                ->map(function ($task) {
                    return [
                        'trip_duration' => $task->sum('trip_duration'),
                        'count' => $task->count(),
                    ];
                });
        }


        foreach ($tasks as $task)
        {
            $task->from_stay_time = floor($task->from_stay_time / 60).'H:'.($task->from_stay_time -   floor($task->from_stay_time / 60) * 60).'M';
            $task->to_stay_time = floor($task->to_stay_time / 60).'H:'.($task->to_stay_time -   floor($task->to_stay_time / 60) * 60).'M';
            $task->trip_duration = floor($task->trip_duration / 60).'H:'.($task->trip_duration -   floor($task->trip_duration / 60) * 60).'M';
            if($task->bag_code == null)
            {
                $task->bags = array();
            }else{
                $task->bags2 = array_count_values (explode(',', $task->bag_code));
                $task->bags = array_unique (explode(',', $task->bag_code));
                $task->temperature_types2 = array_count_values (explode(',', $task->temperature_type));
                $task->temperature_types = array_unique (explode(',', $task->temperature_type));
                $tempVar = json_decode(json_encode($task->temperature_types2),true);
                $bagVar = json_decode(json_encode($task->bags2),true);
                $task->data = array();
                foreach ($tempVar as $key => $value){

                    $temp = new Task();
                    $temp->temperature  =$key;
                    $temp->count  =$value;
                    foreach ($bagVar as $key1 => $value1) {
                        if($value == $value1)
                        {
                            $temp->bag  =$key1;
                            break;
                        }
                    }
                    $task->data[]=$temp;
                    switch ($key)
                    {
                        case 'ROOM':
                            $temp->temperature_label = '+15C TO +25C';
                            $roomBags += 1;
                            $roomSamples += $value;
                            break;
                        case 'REFRIGERATE':
                            $temp->temperature_label = '+2C TO +8C';
                            $refSamples += $value;
                            $refBags += 1;
                            break;
                        case 'FROZEN':
                            $temp->temperature_label = '0C TO -18C';
                            $frozenSamples += $value;
                            $frozenBags += 1;
                            break;
                    }
                }
            }

        }





        $pickup_smaple = $roomSamples+$refSamples+$frozenSamples;
        $pickup_container = $frozenBags+$refBags+$roomBags;

        $condition = Task::where(function ($query) use ($keyWord,$sample_barcode) {
            $query->orWhere('from_location', 'LIKE', $keyWord)
                ->orWhereHas('driver', function ($query) use ($keyWord) {
                    $query->where('name', 'LIKE', $keyWord);
                })
                ->orWhereHas('from', function ($query) use ($keyWord) {
                    $query->where('name', 'LIKE', $keyWord);
                })
                ->orWhereHas('to', function ($query) use ($keyWord) {
                    $query->where('name', 'LIKE', $keyWord);
                })
                ->orWhereHas('client', function ($query) use ($keyWord) {
                    $query->where('english_name', 'LIKE', $keyWord);
                })
                ->orWhere('type', 'LIKE', $keyWord);

        })

            ->when($sample_barcode, function  ($query) use ($sample_barcode) {
                $query->whereHas('samples', function ($query) use ($sample_barcode){
                    $query->where('barcode_id', 'LIKE',  $sample_barcode);
                });
            })
            ->when($status, function  ($query)  use ($status){
                $query->where('tasks.status', $status);
            })

            ->when($task_type, function  ($query) use($task_type) {
                $query->where('task_type', $task_type);
            })
            ->when($from_location, function  ($query) use ($from_location) {
                $query->where('from_location', $from_location);
            })
            ->when($to_location, function  ($query)  use ($to_location){
                $query->where('to_location', $to_location);
            })
            ->when($billing_client, function  ($query) use ($billing_client) {
                $query->where('billing_client', $billing_client);
            })
            ->when($driver_id, function  ($query) use ($driver_id) {
                $query->where('driver_id', $driver_id);
            })
            ->whereDate('tasks.created_at', date('Y-m-d'))
        ;
        $served_orginization = $condition->whereIn('tasks.status',['CLOSED','NO_SAMPLES'])
            ->distinct('from_location')->count('from_location');

        $visited_orginization = $condition->where('status','NO_SAMPLES')->count();



        $pick_sum_data = array(
            $pickup_container,$pickup_smaple
        );

        //summary data
        $summary = Task::with(['client' => function ($query) {
            $query->select('id', 'english_name');
        }])
            ->when($sample_barcode, function  ($query) use ($sample_barcode) {
                $query->whereHas('samples', function ($query) use ($sample_barcode){
                    $query->where('barcode_id', 'LIKE',  $sample_barcode);
                });
            })
            ->when($status, function  ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($task_type, function  ($query)  use ($task_type){
                $query->where('task_type', $task_type);
            })
            ->when($from_location, function  ($query) use ($from_location) {
                $query->where('from_location', $from_location);
            })
            ->when($to_location, function  ($query)  use ($to_location){
                $query->where('to_location', $to_location);
            })
            ->when($billing_client, function  ($query) use ($billing_client) {
                $query->where('billing_client', $billing_client);
            })
            ->when($driver_id, function  ($query) use ($driver_id) {
                $query->where('driver_id', $driver_id);
            })
            ->whereDate('created_at', date('Y-m-d'))

            ->select('status','billing_client',DB::raw('count(*) as total'))->groupBy('status')->get();

        
            // Generate HTML for PDF report using template file
            $html = View::make('report_template', [
                'tasks' => $tasks,
                'summary' => $summary,
                'pick_sum_data' => $pick_sum_data,
                'client_logo' => $client_logo,
                'billing_client' => $billing_client,
                'reportDate' => $reportDate,
                'summaryReport' => $summaryReport,
                'frozenSamples' => $frozenSamples,
                'visited_orginization'=> $visited_orginization,
                'served_orginization'=> $served_orginization,
                'frozenBags'=> $frozenBags,
                'roomBags'=> $roomBags,
                'refBags'=> $refBags,
                'roomSamples'=> $roomSamples,
                'refSamples' => $refSamples
                ])->render();
            
            // Generate PDF using Dompdf
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Download PDF file
            return $dompdf->stream('users_report.pdf');

            

//         $pdf = PDF::loadView('livewire.exports.tasks.tasks',
//             compact('tasks','summary','pick_sum_data','client_logo','summaryReport','billing_client','reportDate',
//                 'visited_orginization','served_orginization','frozenBags','roomBags','refBags','roomSamples','refSamples','frozenSamples'))
//             ->save(public_path() . '/export/tasks.pdf');

// //        $file="./download/info.pdf";
//         return Response::download(public_path() . '/export/tasks.pdf');
//        $redirect('/export/tasks.pdf');


    }
}
