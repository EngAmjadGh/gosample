<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Car;
use App\Models\Sample;
use App\Models\Client;
use App\Exports\TaskTimeReportExport;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Task;
use Gate;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Dompdf\Options;
use Dompdf\Dompdf;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Spatie\Period\Period;
use Spatie\Period\Precision;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $logged_id_user = auth()->user();
        $sortColumn = $request->sort_by;
        $sortOrder = $request->get('sort_order', 'desc');

        if (!in_array($sortColumn, ['created_at', 'updated_at', 'collection_date'])) {
            $sortColumn = 'collection_date'; // Default to 'created_at' if an invalid column is provided
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc'; // Default to 'desc' if an invalid order is provided
        }

        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Task::with(['from', 'to', 'client', 'driver', 'car'])->select(sprintf('tasks.*', (new Task())->table));

            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }

            // if ($request->filled('date_from') && $request->filled('date_to')) {
            //     $query->whereBetween('samples.created_at', [$request->date_from, $request->date_to]);
            // }
            // if ($request->filled('barcode_id')) {
            //     $query->where('barcode_id', $request->barcode_id);
            // }

            // if ($request->filled('confirmed_by_client')) {
            //     $query->where('samples.confirmed_by_client', $request->confirmed_by_client);
            // }
            // if ($request->filled('task_id')) {
            //     $query->where('task_id', $request->task_id);
            // }

            // if ($request->driver_id !=null) {
            //     $query = $query->where('driver_id', '=', $request->driver_id );
            // }
            // if ($request->status !=null) {
            //     $query = $query->where('status', '=',  $request->status );
            // }

            // ->filter(function ($query)  use ($request) {
                if ($request->status !=null) {
                    $query = $query->where('status', '=',  $request->status );
                }
                if ($request->driver_id !=null) {
                    $query = $query->where('driver_id', '=', $request->driver_id );
                }
                if ($request->billing_client !=null) {
                    $query = $query->where('billing_client', '=', $request->billing_client );
                }
                if ($request->from_location !=null) {
                    $query = $query->where('from_location', '=', $request->from_location );
                }
                if ($request->to_location !=null) {
                    $query = $query->where('to_location', '=', $request->to_location );
                }
                if ($request->keyword !=null) {
                    $query =  $query->where('tasks.id', '=', $request->keyword );
                }


                $date_column = $request->search_date ?? 'tasks.created_at';

                if($request->date_from !=null && $request->date_to !=null)
                {
                    $query = $query->whereBetween($date_column, [
                        Carbon::createFromFormat('Y-m-d\TH:i', $request->date_from)->toDateTimeString(),
                        Carbon::createFromFormat('Y-m-d\TH:i', $request->date_to)->toDateTimeString(),
                    ]);
                } else {
                    if ($request->date_from !=null) {
                        $query =  $query->where($date_column, '>=', Carbon::createFromFormat('Y-m-d\TH:i', $request->date_from)->toDateTimeString());
                    }
                    if ($request->date_to !=null) {
                        $query =  $query->where($date_column, '<=', Carbon::createFromFormat('Y-m-d\TH:i', $request->date_to)->toDateTimeString());
                    }
                }

            $query->orderBy($sortColumn, $sortOrder);

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
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

            $table->addColumn('client', function ($row) {
                return $row->client ? $row->client->english_name : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->addColumn('close_date', function ($row) {
                return $row->close_date ? $row->close_date : '';
            });

            $table->addColumn('car_imei', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('delayed_reason', function ($row) {
                return $row->delayed_reason ? $row->delayed_reason : '';
            });

            $table->addColumn('hours', function ($row) {
                if($row->collection_date == null || $row->close_date)
                {
                    return '';
                }
                return $row->close_date ? parent::hoursandmins(Period::make($row->collection_date,$row->close_date,  Precision::MINUTE())->length(), '%02d Hours, %02d Minutes')
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
            $table->editColumn('signature', function ($row) {
                return $row->signature ? $row->signature : '';
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


            $table->editColumn('confirmed_received_by_driver', function ($row) {
                if ($row->confirmed_received_by_driver == 1) {
                    return '<span class="confirmed">Confirmed</span>';
                } elseif ($row->confirmed_received_by_driver == 0) {
                    return '<span class="not-confirmed">Not Confirmed</span>';
                } else {
                    return '';
                }
            });

            $table->editColumn('driver_confirm_from_location', function ($row) {
                if ($row->driver_confirm_from_location == 1) {
                    return '<span class="confirmed">Confirmed</span>';
                } elseif ($row->driver_confirm_from_location == 0) {
                    return '<span class="not-confirmed">Not Confirmed</span>';
                } else {
                    return '';
                }
            });

            $table->editColumn('driver_confirm_to_location', function ($row) {
                if ($row->driver_confirm_to_location == 1) {
                    return '<span class="confirmed">Confirmed</span>';
                } elseif ($row->driver_confirm_to_location == 0) {
                    return '<span class="not-confirmed">Not Confirmed</span>';
                } else {
                    return '';
                }
            });
            $table->editColumn('to_takasi_number', function ($row) {
                return $row->to_takasi_number ? $row->to_takasi_number : '';
            });



            $table->rawColumns(['actions', 'placeholder', 'from_location', 'to_location', 'billing_client', 'driver', 'car',
            'driver_confirm_from_location','driver_confirm_to_location','confirmed_received_by_driver']);

            return $table->make(true);
        } else{
            \Log::error("no ajax");
        }




        if( $logged_id_user->client_id != null)
        {
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



        return view('admin.tasks.index',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }


    public function pickupdelayed(Request $request)
    {




        $logged_id_user = auth()->user();
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Task::with(['from', 'to', 'client', 'driver', 'car'])->select(sprintf('%s.*', (new Task())->table));

            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }
            ;




            // ->filter(function ($query)  use ($request) {
                $query= $query->whereRaw('pickup_time < collection_date');
                if ($request->status !=null) {
                    $query =  $query->where('status', '=',  $request->status );
                }
                if ($request->driver_id !=null) {
                    $query =  $query->where('driver_id', '=', $request->driver_id );
                }
                if ($request->billing_client !=null) {
                    $query =  $query->where('billing_client', '=', $request->billing_client );
                }
                if ($request->from_location !=null) {
                    $query =  $query->where('from_location', '=', $request->from_location );
                }
                if ($request->to_location !=null) {
                    $query =  $query->where('to_location', '=', $request->to_location );
                }
                if ($request->keyword !=null) {
                    $query =  $query->where('tasks.id', '=', $request->keyword );
                }
                if ($request->delayed_reason !=null) {
                    $query =  $query->where('tasks.delayed_reason', '=', $request->delayed_reason );
                }
                if($request->date_from !=null && $request->date_to !=null)
                {
                    $query =  $query->whereBetween('tasks.created_at',
                        [
                            Carbon::createFromDate($request->date_from )->toDateString(),
                            Carbon::createFromDate($request->date_to)
                            //  ->addDays(1)
                             ->toDateString()
                        ]
                        );
                } else{
                    if ($request->date_from !=null) {
                        $query =  $query->where('tasks.created_at', '>=', $request->date_from );
                    }
                    if ($request->date_to !=null) {
                        $query =   $query->where('tasks.created_at', '>=', $request->date_to );
                    }
                }

            // })
            // ;
            \Log::info($sortColumn);
            \Log::info( $sortOrder);
            $query = $query->orderBy($sortColumn, $sortOrder);
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
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

            $table->addColumn('car_imei', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('delayed_reason', function ($row) {
                return $row->delayed_reason ? $row->delayed_reason : '';
            });

            $table->addColumn('hours', function ($row) {
                return $row->close_date ? parent::hoursandmins(Period::make($row->collection_date,$row->close_date,  Precision::MINUTE())->length(), '%02d Hours, %02d Minutes')
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
            $table->editColumn('signature', function ($row) {
                return $row->signature ? $row->signature : '';
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



        return view('admin.tasks.pickupdelayed',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }

    public function dropdelayed(Request $request)
    {
        $logged_id_user = auth()->user();
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Task::with(['from', 'to', 'client', 'driver', 'car'])->select(sprintf('%s.*', (new Task())->table));

            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }
            // ->when(request('status'), function ($q) {
            //     return $q->where('status', request('status'));
            // })
            // ->when(request('driver_id'), function ($q) {
            //     return $q->where('driver_id', request('driver_id'));
            // })
            ;
            $table = Datatables::of($query)

            ->filter(function ($query)  use ($request) {
                $query->whereRaw('dropoff_time < close_date');
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

            })
            ;
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
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

            $table->addColumn('car_imei', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('delayed_reason', function ($row) {
                return $row->delayed_reason ? $row->delayed_reason : '';
            });

            $table->addColumn('hours', function ($row) {
                return $row->close_date ? parent::hoursandmins(Period::make($row->collection_date,$row->close_date,  Precision::MINUTE())->length(), '%02d Hours, %02d Minutes')
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
            $table->editColumn('signature', function ($row) {
                return $row->signature ? $row->signature : '';
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



        return view('admin.tasks.dropdelayed',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }
    public function collectedDelayed(Request $request)
    {
        $logged_id_user = auth()->user();
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Task::with(['from', 'to', 'client', 'driver', 'car'])->select(sprintf('%s.*', (new Task())->table));

            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }
            // ->when(request('status'), function ($q) {
            //     return $q->where('status', request('status'));
            // })
            // ->when(request('driver_id'), function ($q) {
            //     return $q->where('driver_id', request('driver_id'));
            // })
            ;
            $table = Datatables::of($query)

            ->filter(function ($query)  use ($request) {
                $query ->whereRaw('TIMESTAMPDIFF(MINUTE,  freezer_out_date,NOW() ) > 5')
                ->where('status','OUT_FREEZER');
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

            })
            ;
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
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

            $table->addColumn('car_imei', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('delayed_reason', function ($row) {
                return $row->delayed_reason ? $row->delayed_reason : '';
            });

            $table->addColumn('hours', function ($row) {
                return $row->close_date ? parent::hoursandmins(Period::make($row->collection_date,$row->close_date,  Precision::MINUTE())->length(), '%02d Hours, %02d Minutes')
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
            $table->editColumn('signature', function ($row) {
                return $row->signature ? $row->signature : '';
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



        return view('admin.tasks.collectedDelayed',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }


    public function outfreezerdelayed(Request $request)
    {
        $logged_id_user = auth()->user();
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Task::with(['from', 'to', 'client', 'driver', 'car'])->select(sprintf('%s.*', (new Task())->table));

            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }
            // ->when(request('status'), function ($q) {
            //     return $q->where('status', request('status'));
            // })
            // ->when(request('driver_id'), function ($q) {
            //     return $q->where('driver_id', request('driver_id'));
            // })
            ;
            $table = Datatables::of($query)

            ->filter(function ($query)  use ($request) {
                $query->whereRaw('TIMESTAMPDIFF(MINUTE,  collection_date,NOW() ) > 10')->where('status','COLLECTED');
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

            })
            ;
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
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

            $table->addColumn('car_imei', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('delayed_reason', function ($row) {
                return $row->delayed_reason ? $row->delayed_reason : '';
            });

            $table->addColumn('hours', function ($row) {
                return $row->close_date ? parent::hoursandmins(Period::make($row->collection_date,$row->close_date,  Precision::MINUTE())->length(), '%02d Hours, %02d Minutes')
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
            $table->editColumn('signature', function ($row) {
                return $row->signature ? $row->signature : '';
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



        return view('admin.tasks.outfreezerdelayed',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }

    public function exportExcelDetails(Request $request)
    {
        $status = $request->input('status');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $billingClient = $request->input('billing_client');
        $fromLocation = $request->input('from_location');
        $toLocation = $request->input('to_location');
        $driverId = $request->input('driver_id');

        // Log the request parameters (optional)

        // Create an instance of TaskTimeReportExport with the request parameters
        $export = new TaskTimeReportExport($status, $date_from, $date_to, $billingClient, $fromLocation, $toLocation, $driverId);

        // Return the Excel download
        return Excel::download($export, 'task_time_report.xlsx');
    }


    // public function exportExcelDetails(Request $request){

    //     return Excel::download(new TaskTimeReportExport(), 'task_time_report.xlsx');
    // }

    public function create()
    {
        abort_if(Gate::denies('task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');




        $logged_id_user = auth()->user();
        if($logged_id_user->client_id != null)
        {
            // $from_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $from_locations = Location::select('locations.*')
            ->leftJoin('client_location','client_location.location_id','locations.id')
            ->where('client_location.client_id',$logged_id_user->client_id)
            ->pluck('name', 'id');
            // ->get();
            $to_locations = $from_locations  ;//Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $billing_clients = Client::where('id',$logged_id_user->client_id)->pluck('english_name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');
        } else{
            $from_locations = Location::pluck('name', 'id');

            $to_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $billing_clients = Client::pluck('english_name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

            $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');
        }


        return view('admin.tasks.create', compact('billing_clients', 'cars', 'drivers', 'from_locations', 'to_locations'));
    }

    public function unUsed(Request $request)
    {
        abort_if(Gate::denies('unused_tasks'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tasks = Task::leftjoin('clients','clients.id','=','tasks.billing_client')
            ->leftjoin('drivers','drivers.id','=','tasks.driver_id')
            ->leftjoin('locations as from','from.id','=','tasks.from_location')
            ->leftjoin('locations as to','to.id','=','tasks.to_location')
            ->select('tasks.*','clients.english_name','drivers.name as dname','from.name as from_name','to.name as to_name')->onlyTrashed()->where('is_unused',true);
        if (!empty($request->input('client_id'))){
            $tasks = $tasks->where('clients.id',$request->input('client_id'));
        }
        if (!empty($request->input('driver_id'))){
            $tasks = $tasks->where('drivers.id',$request->input('driver_id'));
        }
        if($request->date_from !=null && $request->date_to !=null)
        {
            $tasks = $tasks->whereBetween('tasks.created_at', [
                Carbon::createFromFormat('Y-m-d\TH:i', $request->date_from)->toDateTimeString(),
                Carbon::createFromFormat('Y-m-d\TH:i', $request->date_to)->toDateTimeString(),
            ]);
        } else {
            if ($request->date_from !=null) {
                $tasks =  $tasks->where('tasks.created_at', '>=', Carbon::createFromFormat('Y-m-d\TH:i', $request->date_from)->toDateTimeString());
            }
            if ($request->date_to !=null) {
                $tasks =  $tasks->where('tasks.created_at', '<=', Carbon::createFromFormat('Y-m-d\TH:i', $request->date_to)->toDateTimeString());
            }
        }
        if ($request->ajax()) {
            return DataTables::of($tasks)
                ->make(true);
        }

        $clients = Client::all();
        $locations = Location::all();
        $drivers = Driver::all();
        return view('admin.tasks.un_used',['clients' =>  $clients, 'locations' =>  $locations, 'drivers' =>  $drivers]);

    }
    public function scan()
    {
        abort_if(Gate::denies('task_scan'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $to_locations = Location::orderBy('name','asc')->pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.tasks.scan', compact('drivers', 'to_locations'));
    }

    public function missing()
    {
        abort_if(Gate::denies('task_missing'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.tasks.missing');
    }

    public function store(StoreTaskRequest $request)
    {
        $logged_id_user = auth()->user();
        // $request->added_by = $logged_id_user->email;
        $driver = Driver::find( $request->driver_id);
        for ($i=0; $i < $request->time_of_visit; $i++) {
            foreach ($request->from_location as $from_location) {
                $task = new Task();//::create($request->all());
                $task->to_location = $request->to_location;
                $task->type = $request->type;
                $task->pickup_time = $request->pickup_time;
                $task->dropoff_time = $request->dropoff_time;
                $task->takasi = $request->takasi;
                $task->time_of_visit = $request->time_of_visit;
                $task->task_type = $request->task_type;
                $task->driver_id = $request->driver_id;
                $task->billing_client = $request->billing_client;
                $task->from_location = $from_location;
                $task->added_by = $logged_id_user->email;
                $task->created_at = now();
                $task->save();
                $driver->sendNotification( 'New Task', 'You have new task',[$driver->fcm_token],$task,'open_task');
            }
        }


        return redirect()->route('admin.tasks.index');
    }

    public function edit(Task $task)
    {
        abort_if(Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $billing_clients = Client::pluck('english_name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $task->load('from', 'to', 'client', 'driver', 'car');

        \Log::info($task);
        return view('admin.tasks.edit', compact('billing_clients', 'cars', 'drivers', 'from_locations', 'task', 'to_locations'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->all();

        if ($request->has('status') && $request->status == 'CLOSED') {
            $data['closed_by'] = 'admin'; // Add the 'closed_by' field with the value 'admin'
        }


        $task->update($data);


        return redirect()->route('admin.tasks.index');
    }

    public function show(Task $task)
    {
        abort_if(Gate::denies('task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->load('from', 'to', 'client', 'driver', 'car');
        $bags = Sample::where('task_id',$task->id)->get()->groupBy('bag_code');
        $bag_count = Sample::where('task_id',$task->id)->distinct('bag_code')->count('bag_code');
        $sample_count = Sample::where('task_id',$task->id)->count();
        $carTracking = DB::table('car_tracking')
            ->select(
                DB::raw("count(id) AS cnt"),
                DB::raw("COALESCE(ROUND(SUM(temp5),2),'0') AS total_temp_1"),
                DB::raw("COALESCE(ROUND(SUM(temp6),2),'0') AS total_temp_2"),
                DB::raw("COALESCE(ROUND(SUM(temp7),2),'0') AS total_temp_3"),
            )->where('task_id',$task->id)->first();
        return view('admin.tasks.show', compact('task','bags','bag_count','sample_count','carTracking'));
    }

    public function destroy(Task $task)
    {
        abort_if(Gate::denies('task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskRequest $request)
    {
        Task::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }


    public function dailyOperation(Request $request)
    {
        // return ;
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

            });
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'task_show_no';
                $editGate = 'task_edit_no';
                $deleteGate = 'task_delete_no';
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

            $table->addColumn('billing_client', function ($row) {
                return $row->client ? $row->client->english_name : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->addColumn('close_date', function ($row) {
                return $row->close_date ? $row->close_date : '';
            });

            $table->addColumn('car_imei', function ($row) {
                return $row->car ? $row->car->imei : '';
            });

            $table->addColumn('start_time', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('end_time', function ($row) {
                return $row->car ? $row->car->imei : '';
            });
            $table->addColumn('hours', function ($row) {
                return $row->close_date ? CarbonPeriod::create($row->close_date, $row->from_location_arrival_time)->duration() : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? Task::STATUS_SELECT[$row->status] : '';
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



        return view('admin.tasks.index',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }


    public function export(Request $request)
    {

        if($request->report_type == 'excel')
        {
            return $this->exportExcel($request);
        }
        // $data = [
        //     'title' => 'My Report',
        //     'date' => date('Y-m-d'),
        //     'items' => [
        //         ['id' => 1, 'name' => 'Item 1', 'quantity' => 10],
        //         ['id' => 2, 'name' => 'Item 2', 'quantity' => 5],
        //         ['id' => 3, 'name' => 'Item 3', 'quantity' => 20],
        //     ],
        // ];

        // return Excel::download(new MyReportExport($data), 'report.xlsx');





        $billing_client = $request->billing_client;
        if($billing_client != null){
            $clint = Client::find($billing_client);
            $client_logo = $clint->logo;
            // dd($client_logo );
        }else{
            $client_logo = null;
        }
        $sample_barcode = '';//$sample_barcode;
        $keyWord = '';//'%'.$keyWord .'%';
        // $to = date('Y-m-d', strtotime($request->date_to)). ' 11:59:59';
        // $from = date('Y-m-d', strtotime($request->date_from)). ' 00:00:00';

        $to = $request->date_to;
        $from = $request->date_from;

        $date_column = $request->search_date ?? 'tasks.created_at';



        $from_location = $request->from_location;
        $to_location = $request->to_location;
        // $task_type = '"SAMPLE"';
        $driver_id = $request->driver_id;
        // \Log::info( $driver_id);
        $status = $request->status;
        // $status = '';
        if($from == $to)
        {
            $reportDate =  $to;
        } else{
            $reportDate = 'From '. $from. '- To '. $to;
        }



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


        $billing_client=$request->billing_client;
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
        if($driver_id !=null)
        {
            $query =  $query.' and tasks.driver_id= '.$driver_id;
        }


        // if($task_type !=null)
        // {
        //     $query =  $query.' and tasks.task_type= '.$task_type;
        // }


        // if($from !=null && $to !=null )
        // {
        //     $query =  $query." and tasks.created_at BETWEEN '".date('Y-m-d H:i:s', strtotime( $from))."' and '".date('Y-m-d H:i:s', strtotime( $to))." '";
        // }

        if($from !=null && $to !=null )
        {
            $query =  $query." and ".$date_column." BETWEEN '".date('Y-m-d H:i:s', strtotime( $from))."' and '".date('Y-m-d H:i:s', strtotime( $to))." '";
        }

        if($status !=null)
        {
            $query =  $query." and tasks.status= '".$status."'";
        }

        // \Log::info($query);

        $tasks = DB::select($query.' group by tasks.id order by from_location.name asc, tasks.from_location_arrival_time asc;');
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

            // ->when($task_type, function  ($query) use($task_type) {
            //     $query->where('task_type', $task_type);
            // })
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
            // if($from !=null && $to !=null )
            // {
            //     $query =  $query." and ".$date_column." BETWEEN '".date('Y-m-d H:i:s', strtotime( $from))."' and '".date('Y-m-d H:i:s', strtotime( $to))." '";
            // }
            ->whereBetween($date_column, [date('Y-m-d H:i:s', strtotime( $from)), date('Y-m-d H:i:s', strtotime( $to))]);
            // ->whereDate($date_column, date('Y-m-d'))
            // ->whereDate('tasks.created_at', date('Y-m-d'))
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
            // ->when($task_type, function  ($query)  use ($task_type){
            //     $query->where('task_type', $task_type);
            // })
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

            // $css = file_get_contents(public_path('assets/css/export.css'));
            // $image = base64_encode(file_get_contents(public_path('/img/mtc_logo.jpg')));

            // $options->set('isRemoteEnabled', true);

            // Generate PDF using Dompdf
            // $dompdf = new Dompdf();

            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            // $dompdf->add_css($css);

            $dompdf->setPaper('A3', 'landscape');
            $dompdf->render();

            // Download PDF file

            // return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('users_report.pdf')->stream();
            return $dompdf->stream('users_report.pdf');



    }


    public function exportExcel(Request $request)
    {

        // if ( $this->from == null || $this->to == null ) {
        //     return session()->flash('error', 'Date from and date to needed to generate PDF');
        //  }


        $from = $request->date_from;
        $to = $request->date_to;


        $sample_barcode = $request->sample_barcode;
		$keyWord = '%'.$request->keyWord .'%';

        if($request->billing_client){
            $clint = Client::find($request->billing_client);
            $client_logo = $clint->logo;
            // dd($client_logo );
        }else{
            $client_logo = null;
        }
        $sample_barcode = $request->sample_barcode;
        $keyWord = '%'.$request->keyWord .'%';
        // $to =  $request->date_to;
        // $from =  $request->date_from.' 00:00:00';

        $to = date('Y-m-d', strtotime($request->date_to)). ' 11:59:59';

        // $from =  $request->date_from;
        $from = date('Y-m-d', strtotime($request->date_from)). ' 00:00:00';


        if($request->from != null)
        {
            $request->selected_date = $request->date_from;
        } else{
            $request->selected_date = Carbon::now();
        }

        $query = 'select  tasks.id as id  ,  from_location.name as "from_organization_name",  tasks.from_location_arrival_time as "from_location_arrival_time",freezer_date,
                                    close_date,TIMESTAMPDIFF(Minute, tasks.from_location_arrival_time,  tasks.collection_date) as "from_stay_time",
                                    to_location.name as "to_organization_name",
                                    drivers.name as "driverName",
                                    tasks.created_at,
                                    clients.english_name as "clientName",
                                    tasks.to_location_arrival_time as "to_location_arrival_time",
                                    TIMESTAMPDIFF(Minute, tasks.to_location_arrival_time, tasks.close_date ) as "to_stay_time",
                                    TIMESTAMPDIFF(Minute,  tasks.from_location_arrival_time, tasks.close_date) as "trip_duration",
                                    GROUP_CONCAT(samples.bag_code) as "bag_code",
                                    GROUP_CONCAT(samples.barcode_id) as "sample_code",
                                    GROUP_CONCAT(samples.sample_type) as "sample_types",
                                    GROUP_CONCAT(samples.temperature_type) as "temperature_type",
                                    count(samples.id) as "bags_count"
                                    from tasks
                                    left join drivers on drivers.ID = tasks.driver_id
                                    left join clients on clients.ID = tasks.billing_client
                                    left join locations as from_location on from_location.ID = tasks.from_location
                                    left join locations as to_location on to_location.ID = tasks.to_location
                                    left join samples as samples on samples.task_id = tasks.id
                                    WHERE  tasks.id > 1 ';

        if($request->billing_client !=null)
        {
            $query =  $query.' and tasks.billing_client= '.$request->billing_client;
        }

        if($request->date_from !=null && $request->date_to !=null )
        {
            $query =  $query." and tasks.created_at BETWEEN '".date('Y-m-d H:i:s', strtotime( $request->date_from))."' and '".date('Y-m-d H:i:s', strtotime( $request->date_to))." '";
        }

        if($request->to_location !=null)
        {
            $query =  $query.' and tasks.to_location= '.$request->to_location;
        }
        if($request->from_location !=null)
        {
            $query =  $query.' and tasks.from_location= '.$request->from_location;
        }
        if($request->driver_id !=null)
        {
            $query =  $query.' and tasks.driver_id= '.$request->driver_id;
        }
        if($request->status !=null)
        {
            $query =  $query." and tasks.status= '".$request->status."'";
        }

        $tasks = DB::select($query.' group by tasks.id  order by from_location.name;');
        $roomBags = 0;
        $refBags = 0;
        $frozenBags = 0;

        $roomSamples = 0;
        $refSamples = 0;
        $frozenSamples = 0;
        foreach ($tasks as $task)
        {
            $task->box_count = 0;
            $task->from_stay_time = floor($task->from_stay_time / 60).' H : '.($task->from_stay_time -   floor($task->from_stay_time / 60) * 60).' M';
            $task->to_stay_time = floor($task->to_stay_time / 60).' H : '.($task->to_stay_time -   floor($task->to_stay_time / 60) * 60).' M';
            $task->trip_duration = floor($task->trip_duration / 60).'H:'.($task->trip_duration -   floor($task->trip_duration / 60) * 60).'M';
            if($task->bag_code == null)
            {
                $task->temperature_types2 = array();
                $task->data = array();
                $task->sample_codes = array();
                $task->bags = array();
            }else{
                $task->sample_codes = explode(',', str_replace(' ', '', $task->sample_code) );
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
                    $task->data[]=$temp;
                }
            }

        }

         $temperatures = Sample::select('temperature_type',DB::raw('count(DISTINCT bag_code) as bcode'),DB::raw('count(*) as total'))
         ->when($request->date_from !='' || $request->date_to !='', function  ($query) use ($from, $to,$request) {
            $query->whereBetween('created_at',
                    [
                        Carbon::createFromDate($request->date_from)->toDateString(),
                        Carbon::createFromDate($request->date_to)->toDateString()
                    ]
            );

        })
         ->groupBy('temperature_type')->get();


        return Excel::download(new TasksExport($tasks,$temperatures), 'report.xlsx');
    }


}
