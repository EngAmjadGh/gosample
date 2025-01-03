<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyScheduledTaskRequest;
use App\Http\Requests\StoreScheduledTaskRequest;
use App\Http\Requests\UpdateScheduledTaskRequest;
use App\Models\Client;
use App\Models\Location;
use App\Models\ScheduledTask;
use App\Models\Driver;
use Illuminate\Support\Carbon;

use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ScheduledTaskController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('scheduled_task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $logged_id_user = auth()->user();
        if ($request->ajax()) {
            $query = ScheduledTask::with(['from_location', 'to_location', 'client'])->select(sprintf('%s.*', (new ScheduledTask)->table));
            $query->where('scheduled_tasks.parent_id',null);
             // Apply search criteria
             if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->filled('driver_id')) {
                $query->where('driver_id', $request->driver_id);
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->client_id);
            }
            if ($request->filled('from_location')) {
                $query->where('scheduled_tasks.from_location_id', $request->from_location);
            }
            if ($request->filled('to_location')) {
                $query->where('scheduled_tasks.to_location_id', $request->to_location);
            }


            $table = Datatables::of($query);


            $table->addColumn('placeholder', '&nbsp;');
            /*$table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'scheduled_task_show';
                $editGate      = 'scheduled_task_edit';
                $deleteGate    = 'scheduled_task_delete';
                $crudRoutePart = 'scheduled-tasks';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });*/

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? ScheduledTask::STATUS_SELECT[$row->status] : '';
            });

            $table->addColumn('from_location_name', function ($row) {
                return $row->from_location ? $row->from_location->name : '';
            });

            $table->addColumn('to_location_name', function ($row) {
                return $row->to_location ? $row->to_location->name : '';
            });

            $table->addColumn('client_status', function ($row) {
                return $row->client ? $row->client->english_name : '';
            });

            $table->editColumn('task_type', function ($row) {
                return $row->task_type ? ScheduledTask::TASK_TYPE_SELECT[$row->task_type] : '';
            });
            $table->editColumn('added_by', function ($row) {
                return $row->added_by ? $row->added_by : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });
            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
            $table->rawColumns([ 'placeholder', 'from_location', 'to_location', 'client','driver']);

            return $table->make(true);
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


        return view('admin.scheduledTasks.index',[
            'clients' =>  $clients,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('scheduled_task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday"];

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.scheduledTasks.create', compact('days','drivers','clients', 'from_locations', 'to_locations'));
    }
    public function quick()
    {
        abort_if(Gate::denies('scheduled_task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday"];

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.scheduledTasks.quick', compact('days','drivers','clients', 'from_locations', 'to_locations'));
    }



    public function store(StoreScheduledTaskRequest $request)
    {

        $data = $request->except(['from_location_id', 'selected_days', 'visit_hours']); // Exclude from_location, selected_days, and visit_hours
        $fromLocations = $request->input('from_location_id');
        $selectedDays = $request->input('days');
        $selectedHours = $request->input('visit_hours');

        $numberOfLocations = count($fromLocations);
        $totalVisits =  $numberOfLocations;


        if (!is_array($selectedHours) || count($selectedHours) !== $totalVisits) {
            $errorMessage = 'The number of visit hours must be equal to ' . count($fromLocations) . ' (number of selected locations in from_location)';
            return redirect()->back()->withInput()->withErrors(['visit_hours' => $errorMessage]);
        }

        //need add parent id
        $parent_id = null;
        foreach ($fromLocations as $locationIndex => $fromLocationId) {
            foreach ($selectedDays as $selectedDay) {
                // Get the selected hour for this location (use modulo to loop through the hours)
                $selectedHour = $selectedHours[$locationIndex % count($selectedHours)];

                // Create a new scheduled task with the same details
                $scheduledTask = new ScheduledTask($data);
                $scheduledTask->parent_id = $parent_id;
                $scheduledTask->from_location_id = $fromLocationId;
                $scheduledTask->day = $selectedDay;
                $scheduledTask->selected_hour = $selectedHour;
                $scheduledTask->save();
                if (empty($parent_id)) {
                    $parent_id = $scheduledTask->id;
                }
            }
        }

        return redirect()->route('admin.scheduled-tasks.index');
    }



    public function quickAction(StoreScheduledTaskRequest $request)
    {
        $data = $request->except(['from_location_id', 'days', 'visit_hours']); // Exclude from_location, days, and visit_hours
        $fromLocationId = $request->input('from_location_id'); // Assuming it's a single value, not an array
        $selectedDays = $request->input('days');
        $selectedHours = $request->input('visit_hours');

        $numberOfDays = count($selectedDays);
        $numberOfHours = count($selectedHours);
        $totalVisits = $numberOfDays * $numberOfHours;

        if ($totalVisits === 0) {
            return redirect()->back()->withInput()->withErrors(['general' => 'You must select at least one day and one visit hour.']);
        }
        $parent_id = null;
        foreach ($selectedDays as $selectedDay) {
            foreach ($selectedHours as $selectedHour) {
                // Create a new scheduled task for each combination of day and hour
                $scheduledTask = new ScheduledTask($data);
                $scheduledTask->parent_id = $parent_id;
                $scheduledTask->from_location_id = $fromLocationId;
                $scheduledTask->day = $selectedDay;
                $scheduledTask->selected_hour = $selectedHour;
                $scheduledTask->save();
                if (empty($parent_id)) {
                    $parent_id = $scheduledTask->id;
                }
            }
        }

        return redirect()->route('admin.scheduled-tasks.index');
    }


    public function edit(ScheduledTask $scheduledTask)
    {
        abort_if(Gate::denies('scheduled_task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        //$scheduledTask->load('from_location', 'to_location', 'client');
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday"];

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        return view('admin.scheduledTasks.edit', compact('days','clients','drivers', 'from_locations', 'scheduledTask', 'to_locations'));
    }

    public function update(UpdateScheduledTaskRequest $request, ScheduledTask $scheduledTask)
    {
        $scheduledTask->update($request->all());

        if (empty($scheduledTask->parent_id)){
            return redirect()->route('admin.scheduled-tasks.show', $scheduledTask);
        }
        $scheduled = ScheduledTask::where('id',$scheduledTask->parent_id)->first();
        if (isset($scheduled->id)) {
            return redirect()->route('admin.scheduled-tasks.show', $scheduled);
        }
        return redirect()->route('admin.scheduled-tasks.index');
    }

    public function show(Request $request,ScheduledTask $scheduledTask)
    {
        abort_if(Gate::denies('scheduled_task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = ScheduledTask::with(['from_location', 'to_location', 'client'])->select(sprintf('%s.*', (new ScheduledTask)->table));
            $query->where(function($q) use($scheduledTask){
                $q->where('scheduled_tasks.parent_id',$scheduledTask->id)
                    ->orWhere('scheduled_tasks.id', $scheduledTask->id);
            });
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = '';
                $editGate      = 'scheduled_task_edit';
                $deleteGate    = 'scheduled_task_delete';
                $crudRoutePart = 'scheduled-tasks';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? ScheduledTask::STATUS_SELECT[$row->status] : '';
            });

            $table->addColumn('from_location_name', function ($row) {
                return $row->from_location ? $row->from_location->name : '';
            });

            $table->addColumn('to_location_name', function ($row) {
                return $row->to_location ? $row->to_location->name : '';
            });

            $table->addColumn('client_status', function ($row) {
                return $row->client ? $row->client->english_name : '';
            });

            $table->editColumn('task_type', function ($row) {
                return $row->task_type ? ScheduledTask::TASK_TYPE_SELECT[$row->task_type] : '';
            });
            $table->editColumn('added_by', function ($row) {
                return $row->added_by ? $row->added_by : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });
            $table->addColumn('sequence', function ($row) {
                static $index = 0;
                return ++$index;
            });
            $table->rawColumns(['actions', 'placeholder', 'from_location', 'to_location', 'client','driver']);

            return $table->make(true);
        }
       // $scheduledTask->load('from_location', 'to_location', 'client');
        return view('admin.scheduledTasks.show', compact('scheduledTask'));
    }

    public function destroy(ScheduledTask $scheduledTask)
    {
        abort_if(Gate::denies('scheduled_task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schedule = null;
        $parent_id = $scheduledTask->parent_id;
        if (empty($scheduledTask->parent_id)){
            $schedule = ScheduledTask::where('parent_id',$scheduledTask->id)->first();
            if (isset($schedule->id)) {
                $schedule->parent_id = null;
                $schedule->save();
                $allParent = ScheduledTask::where('parent_id',$scheduledTask->id)->update(['parent_id' => $schedule->id]);
            }
        }
        $scheduledTask->delete();
        if (!empty($schedule)) {
            if (isset($schedule->id)) {
                return redirect()->route('admin.scheduled-tasks.show', $schedule);
            }
        }

        if (!empty($parent_id)) {
            $newScheduled = ScheduledTask::where('id', $parent_id)->first();
            if (isset($newScheduled->id)) {
                return redirect()->route('admin.scheduled-tasks.show', $newScheduled);
            }
        }
        return redirect()->route('admin.scheduled-tasks.index');
    }

    public function deleteBasedOnParent(ScheduledTask $scheduledTask)
    {
        abort_if(Gate::denies('scheduled_task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (empty($scheduledTask->parent_id)) {
            ScheduledTask::where('parent_id', $scheduledTask->id)->delete();

        }
        $scheduledTask->delete();
        return redirect()->route('admin.scheduled-tasks.index');
    }

    public function massDestroy(MassDestroyScheduledTaskRequest $request)
    {
        $scheduledTasks = ScheduledTask::find(request('ids'));

        foreach ($scheduledTasks as $scheduledTask) {
            $scheduledTask->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    // public function indexSchedule(Request $request)
    // {
    //     $driverId = $request->input('driver');
    //     $day = $request->input('day');

    //     $query = ScheduledTask::with('driver','from_location','to_location');

    //     if ($driverId) {
    //         $query->where('driver_id', $driverId);
    //     }

    //     if ($day) {
    //         $query->where('day', $day);
    //     }

    //     $tasks = $query->get();
    //     // Format the tasks as needed for the view
    //     $schedule = $this->formatScheduleForCalendar($tasks);
    //     return view('admin.scheduledTasks.index-schedule', compact('schedule'));
    // }

    public function indexSchedule(Request $request)
    {
        $logged_id_user = auth()->user();

        $query = ScheduledTask::with('driver', 'from_location', 'to_location', 'client');

        // Apply filters
        if (!empty($request->input('driver_id'))) {
            $query->where('driver_id', $request->driver_id);
        }
        if (!empty($request->input('billing_client'))) {
            $query->where('client_id', $request->billing_client);
        }
        if (!empty($request->input('from_location'))) {
            $query->where('from_location_id', $request->from_location);
        }
        if (!empty($request->input('to_location'))) {
            $query->where('to_location_id', $request->to_location);
        }

        $tasks = $query->get();

        $schedule = $this->formatScheduleForCalendar($tasks);

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

        return view('admin.scheduledTasks.index-schedule',[
            'clients' =>  $clients,
            'schedule' =>  $schedule,
            'locations' =>  $locations,
            'drivers' =>  $drivers
        ]);
    }

    private function formatScheduleForCalendar($tasks)
    {
        $today = Carbon::today();
        $endOfMonth = Carbon::today()->endOfMonth();
        $events = collect();

        foreach ($tasks as $task) {
            $startDate = Carbon::parse($task->start_date);
            $endDate = Carbon::parse($task->end_date);
            $taskStartDate = $startDate->max($today);
            $taskEndDate =  $endDate->min($endOfMonth);

            for ($date = $taskStartDate->copy(); $date->lte($taskEndDate); $date->addDay()) {
                if (strtolower($date->format('l')) == strtolower($task->day)) {
                    $events->push([
                        'title' => $task->name,
                        'start' => $date->toDateString(),
                        'end' => $date->copy()->addDay()->toDateString(),
                    ]);
                }
            }
        }

        return $events;
    }

    private function formatSchedule($tasks)
    {
        $schedule = [];

        foreach ($tasks as $task) {
            $driverId = $task->driver_id; // Assuming driver_id is the field in your table
            $day = $task->day; // Assuming day is the field in your table

            // Initialize arrays if not already set
            if (!isset($schedule[$driverId])) {
                $schedule[$driverId] = [];
            }

            if (!isset($schedule[$driverId][$day])) {
                $schedule[$driverId][$day] = [];
            }

            // Add the task to the appropriate driver and day
            $schedule[$driverId][$day][] = $task;
        }

        return $schedule;
    }

}
