@extends('layouts.master')
@section('title')
    @lang('translation.attendances')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.attendances')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('translation.attendance') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.attendances.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.attendance.fields.id') }}
                        </th>
                        <td>
                            {{ $attendance->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.attendance.fields.driver') }}
                        </th>
                        <td>
                            {{ $attendance->driver->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.attendance.fields.checkin_time') }}
                        </th>
                        <td>
                            {{ $attendance->checkin_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.attendance.fields.checkout_time') }}
                        </th>
                        <td>
                            {{ $attendance->checkout_time }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.attendances.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection