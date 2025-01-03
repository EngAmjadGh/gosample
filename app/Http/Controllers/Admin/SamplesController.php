<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySampleRequest;
use App\Http\Requests\StoreSampleRequest;
use App\Http\Requests\UpdateSampleRequest;
use App\Models\Container;
use App\Models\Location;
use App\Models\Sample;
use App\Models\Task;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SamplesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sample_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

         $logged_id_user = auth()->user();
        if ($request->ajax()) {
            $query = Sample::with(['location', 'task', 'container','task.driver'])->leftJoin('tasks','tasks.id','samples.task_id')->select(sprintf('samples.*', (new Sample())->table));
            
            if( $logged_id_user->client_id != null)
            {
                $query = $query->where('billing_client', $logged_id_user->client_id);
            }

            // Apply search criteria
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('samples.created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->filled('barcode_id')) {
                $query->where('barcode_id', $request->barcode_id);
            }
           
            if ($request->filled('confirmed_by_client')) {
                $query->where('samples.confirmed_by_client', $request->confirmed_by_client);
            }
            if ($request->filled('task_id')) {
                $query->where('task_id', $request->task_id);
            }


            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'sample_showw';
                $editGate = 'sample_editw';
                $deleteGate = 'sample_delete';
                $crudRoutePart = 'samples';

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
            $table->editColumn('barcode_id', function ($row) {
                return $row->barcode_id ? $row->barcode_id : '';
            });
            $table->addColumn('location_name', function ($row) {
                return $row->location ? $row->location->name : '';
            });

            $table->addColumn('task_id', function ($row) {
                return '<a href="' . route('admin.tasks.show', $row->task_id) . '" class="btn btn-xs btn-primary">'. $row->task_id.'</a>';
            });
            $table->addColumn('driver_id', function ($row) {
                return $row->task ? $row->task->driver->name : '';
            });
            $table->addColumn('collection_date', function ($row) {
                return $row->task ? $row->task->collection_date : '';
            });
            $table->addColumn('to_location', function ($row) {
                return $row->task ? $row->task->to->name : '';
            });
            $table->addColumn('close_date', function ($row) {
                return $row->task ? $row->task->close_date : '';
            });

            $table->editColumn('box_count', function ($row) {
                return $row->box_count ? $row->box_count : '';
            });
            $table->editColumn('sample_count', function ($row) {
                return $row->sample_count ? $row->sample_count : '';
            });
            $table->editColumn('confirmed_by_client', function ($row) {
                return $row->confirmed_by_client ? $row->confirmed_by_client : '';
            });
            $table->editColumn('confirmed_by', function ($row) {
                return $row->confirmed_by ? $row->confirmed_by : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Sample::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('sample_type', function ($row) {
                return $row->sample_type ? $row->sample_type : '';
            });
           

            $table->rawColumns(['actions', 'placeholder', 'location', 'task', 'container','to_location','task_id']);

            return $table->make(true);
        }

        return view('admin.samples.index');
    }

    public function lost(Request $request)
    {
        abort_if(Gate::denies('sample_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Sample::with(['location', 'task', 'container'])
            ->select(sprintf('%s.*', (new Sample())->table));
            $query = $query->where('confirmed_by_client','LOST');
            // Apply search criteria
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->filled('barcode_id')) {
                $query->where('barcode_id', $request->barcode_id);
            }
           
            if ($request->filled('confirmed_by_client')) {
                $query->where('confirmed_by_client', $request->confirmed_by_client);
            }
            if ($request->filled('task_id')) {
                $query->where('task_id', $request->task_id);
            }
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'sample_showw';
                $editGate = 'sample_editw';
                $deleteGate = 'sample_delete';
                $crudRoutePart = 'samples';

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
            $table->editColumn('barcode_id', function ($row) {
                return $row->barcode_id ? $row->barcode_id : '';
            });
            $table->addColumn('location_name', function ($row) {
                return $row->location ? $row->location->name : '';
            });

            $table->addColumn('task_id', function ($row) {
                return $row->task_id ? $row->task_id : '';
            });

            $table->addColumn('container_imei', function ($row) {
                return $row->container ? $row->container->id : '';
            });
            // $table->addColumn('driver', function ($row) {
            //     if( $row->task != null && $row->task->driver != null)
            //     {
            //         return $row->task->driver->name;
            //     }
            // });

            $table->editColumn('box_count', function ($row) {
                return $row->box_count ? $row->box_count : '';
            });
            $table->editColumn('sample_count', function ($row) {
                return $row->sample_count ? $row->sample_count : '';
            });
            $table->editColumn('confirmed_by_client', function ($row) {
                return $row->confirmed_by_client ? $row->confirmed_by_client : '';
            });
            $table->editColumn('confirmed_by', function ($row) {
                return $row->confirmed_by ? $row->confirmed_by : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Sample::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('sample_type', function ($row) {
                return $row->sample_type ? $row->sample_type : '';
            });
            $table->editColumn('temperature_type', function ($row) {
                return $row->temperature_type ? $row->temperature_type : '';
            });
            $table->editColumn('bag_code', function ($row) {
                return $row->bag_code ? $row->bag_code : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'location', 'task', 'container']);

            return $table->make(true);
        }

        return view('admin.samples.lost');
    }

    public function create()
    {
        abort_if(Gate::denies('sample_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tasks = Task::pluck('collect_lat', 'id')->prepend(trans('global.pleaseSelect'), '');

        $containers = Container::pluck('imei', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.samples.create', compact('containers', 'locations', 'tasks'));
    }

    public function store(StoreSampleRequest $request)
    {
        $sample = Sample::create($request->all());

        return redirect()->route('admin.samples.index');
    }

    public function edit(Sample $sample)
    {
        abort_if(Gate::denies('sample_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tasks = Task::pluck('collect_lat', 'id')->prepend(trans('global.pleaseSelect'), '');

        $containers = Container::pluck('imei', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sample->load('location', 'task', 'container');

        return view('admin.samples.edit', compact('containers', 'locations', 'sample', 'tasks'));
    }

    public function update(UpdateSampleRequest $request, Sample $sample)
    {
        $sample->update($request->all());

        return redirect()->route('admin.samples.index');
    }

    public function show(Sample $sample)
    {
        abort_if(Gate::denies('sample_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sample->load('location', 'task', 'container');

        return view('admin.samples.show', compact('sample'));
    }

    public function destroy(Sample $sample)
    {
        abort_if(Gate::denies('sample_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sample->delete();

        return back();
    }

    public function massDestroy(MassDestroySampleRequest $request)
    {
        Sample::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
