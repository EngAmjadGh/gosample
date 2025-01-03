<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDriverScheduleRequest;
use App\Http\Requests\StoreDriverScheduleRequest;
use App\Http\Requests\UpdateDriverScheduleRequest;
use App\Models\Driver;
use App\Models\DriverSchedule;
use App\Models\Location;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverScheduleController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('driver_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driverSchedules = DriverSchedule::with(['from', 'to', 'driver'])->get();

        \Log::info($driverSchedules);

        return view('admin.driverSchedules.index', compact('driverSchedules'));
    }

    public function create()
    {
        abort_if(Gate::denies('driver_schedule_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.driverSchedules.create', compact('drivers', 'from_locations', 'to_locations'));
    }

    public function store(StoreDriverScheduleRequest $request)
    {
        $driverSchedule = DriverSchedule::create($request->all());

        return redirect()->route('admin.driver-schedules.index');
    }

    public function edit(DriverSchedule $driverSchedule)
    {
        abort_if(Gate::denies('driver_schedule_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $driverSchedule->load('from', 'to', 'driver');

        return view('admin.driverSchedules.edit', compact('driverSchedule', 'drivers', 'from_locations', 'to_locations'));
    }

    public function update(UpdateDriverScheduleRequest $request, DriverSchedule $driverSchedule)
    {
        $driverSchedule->update($request->all());

        return redirect()->route('admin.driver-schedules.index');
    }

    public function show(DriverSchedule $driverSchedule)
    {
        abort_if(Gate::denies('driver_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driverSchedule->load('from', 'to', 'driver');

        return view('admin.driverSchedules.show', compact('driverSchedule'));
    }

    public function destroy(DriverSchedule $driverSchedule)
    {
        abort_if(Gate::denies('driver_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driverSchedule->delete();

        return back();
    }

    public function massDestroy(MassDestroyDriverScheduleRequest $request)
    {
        DriverSchedule::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
