<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCarDriverRequest;
use App\Http\Requests\StoreCarDriverRequest;
use App\Http\Requests\UpdateCarDriverRequest;
use App\Models\Car;
use App\Models\CarDriver;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarDriverController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('car_driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carDrivers = CarDriver::with(['car', 'driver'])->get();

        return view('admin.carDrivers.index', compact('carDrivers'));
    }

    public function create()
    {
        abort_if(Gate::denies('car_driver_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.carDrivers.create', compact('cars', 'drivers'));
    }

    public function store(StoreCarDriverRequest $request)
    {
        $carDriver = CarDriver::create($request->all());

        return redirect()->route('admin.car-drivers.index');
    }

    public function edit(CarDriver $carDriver)
    {
        abort_if(Gate::denies('car_driver_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $carDriver->load('car', 'driver');

        return view('admin.carDrivers.edit', compact('carDriver', 'cars', 'drivers'));
    }

    public function update(UpdateCarDriverRequest $request, CarDriver $carDriver)
    {
        $carDriver->update($request->all());

        return redirect()->route('admin.car-drivers.index');
    }

    public function show(CarDriver $carDriver)
    {
        abort_if(Gate::denies('car_driver_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carDriver->load('car', 'driver');

        return view('admin.carDrivers.show', compact('carDriver'));
    }

    public function destroy(CarDriver $carDriver)
    {
        abort_if(Gate::denies('car_driver_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carDriver->delete();

        return back();
    }

    public function massDestroy(MassDestroyCarDriverRequest $request)
    {
        CarDriver::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
