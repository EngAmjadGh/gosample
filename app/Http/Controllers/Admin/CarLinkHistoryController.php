<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCarLinkHistoryRequest;
use App\Http\Requests\StoreCarLinkHistoryRequest;
use App\Http\Requests\UpdateCarLinkHistoryRequest;
use App\Models\Car;
use App\Models\CarLinkHistory;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarLinkHistoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('car_link_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carLinkHistories = CarLinkHistory::with(['driver', 'car'])->get();

        return view('admin.carLinkHistories.index', compact('carLinkHistories'));
    }

    public function create()
    {
        abort_if(Gate::denies('car_link_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.carLinkHistories.create', compact('cars', 'drivers'));
    }

    public function store(StoreCarLinkHistoryRequest $request)
    {
        $carLinkHistory = CarLinkHistory::create($request->all());

        return redirect()->route('admin.car-link-histories.index');
    }

    public function edit(CarLinkHistory $carLinkHistory)
    {
        abort_if(Gate::denies('car_link_history_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $cars = Car::pluck('imei', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $carLinkHistory->load('driver', 'car');

        return view('admin.carLinkHistories.edit', compact('carLinkHistory', 'cars', 'drivers'));
    }

    public function update(UpdateCarLinkHistoryRequest $request, CarLinkHistory $carLinkHistory)
    {
        $carLinkHistory->update($request->all());

        return redirect()->route('admin.car-link-histories.index');
    }

    public function show(CarLinkHistory $carLinkHistory)
    {
        abort_if(Gate::denies('car_link_history_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carLinkHistory->load('driver', 'car');

        return view('admin.carLinkHistories.show', compact('carLinkHistory'));
    }

    public function destroy(CarLinkHistory $carLinkHistory)
    {
        abort_if(Gate::denies('car_link_history_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $carLinkHistory->delete();

        return back();
    }

    public function massDestroy(MassDestroyCarLinkHistoryRequest $request)
    {
        CarLinkHistory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
