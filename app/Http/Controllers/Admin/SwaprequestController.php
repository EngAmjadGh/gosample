<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySwaprequestRequest;
use App\Http\Requests\StoreSwaprequestRequest;
use App\Http\Requests\UpdateSwaprequestRequest;
use App\Models\Driver;
use App\Models\Swap;
use App\Models\Task;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
class SwaprequestController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('swaprequest_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Swap::with(['task', 'driver'])->select(sprintf('%s.*', (new Swap)->table));
            // Apply search criteria
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->filled('driver_id')) {
                $query->where('driver_id', $request->driver_id);
            }
            if ($request->filled('task_id')) {
                $query->where('task_id', $request->task_id);
            }
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'swaprequest_show';
                $editGate      = 'swaprequest_edit';
                $deleteGate    = 'swaprequest_delete';
                $crudRoutePart = 'swaprequests';

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
            $table->addColumn('task_id', function ($row) {
                return $row->task ? $row->task_id : '';
            });

            $table->editColumn('task.status', function ($row) {
                return $row->task ? (is_string($row->task) ? $row->task : $row->task->status) : '';
            });
            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });
            $table->addColumn('driverA', function ($row) {
                return $row->driverA ? $row->driverA->name : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? Swap::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'task', 'driver']);

            return $table->make(true);
        }

        return view('admin.swaprequests.index');
    }

    public function create()
    {
        abort_if(Gate::denies('swaprequest_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

       

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');


        // $tasks = Task::pluck('id', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.swaprequests.create', compact('drivers'));
    }

    // public function store(StoreSwaprequestRequest $request)
    // {
    //     $request->status = 'new';
    //     if($request->driver_id == $request->driver_a)
    //     {
    //         $tasks = Task::where('status','<>','NO_SAMPLES')->where('status','<>','CLOSED')->pluck('id','id')->prepend(trans('translation.pleaseSelect'), '');
    //         $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');
    //         return view('admin.swaprequests.create', compact('drivers', 'tasks'))->withErrors(['driver' =>'please select different driver to swap request']);
    //     }
    //     $swaprequest = Swap::create($request->all());
    //     return redirect()->route('admin.swaprequests.index');
    // }

    public function store(StoreSwaprequestRequest $request)
    {

        \Log::info($request->all());
        $request->merge(['status' => 'new']);
        if ($request->driver_id == $request->driver_a) {
            $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');
            return view('admin.swaprequests.create', compact('drivers'))
                ->withErrors(['driver' => 'Please select a different driver to swap requests']);
        }
    
        if (!is_array($request->task_id)) {
            $request->task_id = [$request->task_id];
        }
    
        $taskIds = $request->input('task_id');

        $swapRequests = [];
        foreach ($taskIds as $taskId) {

            $swapRequest = new Swap();
            $swapRequest->task_id = $taskId;
            $swapRequest->status = 'new';
            $swapRequest->driver_a = $request->driver_a;
            $swapRequest->driver_id = $request->driver_id;
            $swapRequest->save();
        }
    
        return redirect()->route('admin.swaprequests.index');
    }
    

    public function edit(Swap $swaprequest)
    {
        abort_if(Gate::denies('swaprequest_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tasks = Task::pluck('id', 'id')
        ->where('status','<>','NO_SAMPLES')->where('status','<>','CLOSED')
        // ->where('created_at', '>=', Carbon::now()->subWeek())
        ->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $swaprequest->load('task', 'driver');

        return view('admin.swaprequests.edit', compact('drivers', 'swaprequest', 'tasks'));

    }

    public function update(UpdateSwaprequestRequest $request, Swap $swaprequest)
    {
        // $swaprequest->update($request->all());

        // return redirect()->route('admin.swaprequests.index');

        // if($swaprequest->status != 'new')
        // {
        //     $tasks = Task::where('status','<>','NO_SAMPLES')->where('status','<>','CLOSED')->pluck('id', 'id')->prepend(trans('translation.pleaseSelect'), '');

        //     $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        //     $swaprequest->load('task', 'driver');

        //     return view('admin.swaprequests.edit', compact('drivers', 'swaprequest', 'tasks'))->withErrors(['task' =>'Cannot update this swap request']);;
        // }
        $swaprequest->update($request->all());

        return redirect()->route('admin.swaprequests.index');
    }

    public function show(Swap $swaprequest)
    {
        abort_if(Gate::denies('swaprequest_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $swaprequest->load('task', 'driver');

        return view('admin.swaprequests.show', compact('swaprequest'));
    }

    public function destroy(Swap $swaprequest)
    {
        abort_if(Gate::denies('swaprequest_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $swaprequest->delete();

        return back();
    }

    public function massDestroy(MassDestroySwaprequestRequest $request)
    {
        $swaprequests = Swap::find(request('ids'));

        foreach ($swaprequests as $swaprequest) {
            $swaprequest->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
