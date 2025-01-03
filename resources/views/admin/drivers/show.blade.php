@extends('layouts.master')
@section('title')
    @lang('translation.drivers')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.drivers')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('translation.driver') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.drivers.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.id') }}
                        </th>
                        <td>
                            {{ $driver->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.name') }}
                        </th>
                        <td>
                            {{ $driver->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Driver::STATUS_SELECT[$driver->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.username') }}
                        </th>
                        <td>
                            {{ $driver->username }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.mobile') }}
                        </th>
                        <td>
                            {{ $driver->mobile }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.email') }}
                        </th>
                        <td>
                            {{ $driver->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.language') }}
                        </th>
                        <td>
                            {{ App\Models\Driver::LANGUAGE_SELECT[$driver->language] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.zone') }}
                        </th>
                        <td>
                            {{ $driver->zone ? $driver->zone->name : '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.lat') }}
                        </th>
                        <td>
                            {{ $driver->lat }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.lng') }}
                        </th>
                        <td>
                            {{ $driver->lng }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.driver.fields.accepted_terms') }}
                        </th>
                        <td>
                            {{ $driver->accepted_terms }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.drivers.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <ul class="nav nav-pills nav-justified mb-3" role="tablist">

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#driver_car_link_histories" role="tab" data-toggle="tab">
                {{ trans('translation.carLinkHistory') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab"  href="#driver_tasks" role="tab" data-toggle="tab">
                {{ trans('translation.tasks') }}
            </a>
        </li>
       
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="driver_car_link_histories">
            @includeIf('admin.drivers.relationships.driverCarLinkHistories', ['carLinkHistories' => $driver->driverCarLinkHistories])
        </div>
        <div class="tab-pane" role="tabpanel" id="driver_tasks">
            @includeIf('admin.drivers.relationships.driverTasks', ['tasks' => $driver->driverTasks])
        </div>
    </div>

</div>

@endsection