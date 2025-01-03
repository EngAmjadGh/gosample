<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAttendanceRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttendancesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attendances = Attendance::with(['driver'])->get();

        return view('admin.attendances.index', compact('attendances'));
    }

    public function create()
    {
        abort_if(Gate::denies('attendance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.attendances.create', compact('drivers'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $attendance = Attendance::create($request->all());

        return redirect()->route('admin.attendances.index');
    }

    public function edit(Attendance $attendance)
    {
        abort_if(Gate::denies('attendance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $attendance->load('driver');

        return view('admin.attendances.edit', compact('attendance', 'drivers'));
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $attendance->update($request->all());

        return redirect()->route('admin.attendances.index');
    }

    public function show(Attendance $attendance)
    {
        abort_if(Gate::denies('attendance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attendance->load('driver');

        return view('admin.attendances.show', compact('attendance'));
    }

    public function destroy(Attendance $attendance)
    {
        abort_if(Gate::denies('attendance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attendance->delete();

        return back();
    }

    public function massDestroy(MassDestroyAttendanceRequest $request)
    {
        Attendance::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
