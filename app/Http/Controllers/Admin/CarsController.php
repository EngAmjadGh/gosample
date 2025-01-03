<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCarRequest;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CarsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('car_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Car::with(['driver'])->select(sprintf('%s.*', (new Car)->table));
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->filled('imei')) {
                $query->where('imei', $request->imei);
            }
            if ($request->filled('plate_number')) {
                $query->where('plate_number', $request->plate_number);
            }
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'car_show';
                $editGate      = 'car_edit';
                $deleteGate    = 'car_delete';
                $crudRoutePart = 'cars';

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
            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->editColumn('driver.mobile', function ($row) {
                return $row->driver ? (is_string($row->driver) ? $row->driver : $row->driver->mobile) : '';
            });
            $table->editColumn('imei', function ($row) {
                return $row->imei ? $row->imei : '';
            });
            $table->editColumn('plate_number', function ($row) {
                return $row->plate_number ? $row->plate_number : '';
            });
            $table->editColumn('model', function ($row) {
                return $row->model ? $row->model : '';
            });
            $table->editColumn('color', function ($row) {
                return $row->color ? $row->color : '';
            });
            $table->editColumn('contact_person', function ($row) {
                return $row->contact_person ? $row->contact_person : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'driver']);

            return $table->make(true);
        }

        return view('admin.cars.index');
    }

    public function create()
    {
        abort_if(Gate::denies('car_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.cars.create', compact('drivers'));
    }

    public function store(StoreCarRequest $request)
    {
        $car = Car::create($request->all());

        return redirect()->route('admin.cars.index');
    }

    public function edit(Car $car)
    {
        abort_if(Gate::denies('car_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $car->load('driver');

        return view('admin.cars.edit', compact('car', 'drivers'));
    }

    public function update(UpdateCarRequest $request, Car $car)
    {
        $car->update($request->all());

        return redirect()->route('admin.cars.index');
    }

    public function show(Car $car)
    {
        abort_if(Gate::denies('car_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $car->load('driver', 'carCarLinkHistories', 'carTasks');

        return view('admin.cars.show', compact('car'));
    }

    public function destroy(Car $car)
    {
        abort_if(Gate::denies('car_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $car->delete();

        return back();
    }

    public function massDestroy(MassDestroyCarRequest $request)
    {
        $cars = Car::find(request('ids'));

        foreach ($cars as $car) {
            $car->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
