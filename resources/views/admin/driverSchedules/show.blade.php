@extends('layouts.master')
@section('title')
    @lang('translation.driverSchedules')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.driverSchedules')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('translation.driverSchedule.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.driver-schedules.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.driverSchedule.fields.id') }}
                        </th>
                        <td>
                            {{ $driverSchedule->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driverSchedule.fields.from_location') }}
                        </th>
                        <td>
                            {{ $driverSchedule->from->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driverSchedule.fields.to_location') }}
                        </th>
                        <td>
                            {{ $driverSchedule->to->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driverSchedule.fields.driver') }}
                        </th>
                        <td>
                            {{ $driverSchedule->driver->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driverSchedule.fields.note') }}
                        </th>
                        <td>
                            {{ $driverSchedule->note }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driverSchedule.fields.plate_number') }}
                        </th>
                        <td>
                            {{ $driverSchedule->plate_number }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.driver-schedules.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection