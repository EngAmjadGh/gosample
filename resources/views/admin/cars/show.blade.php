@extends('layouts.master')
@section('title')
    @lang('translation.cars')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.cars')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.show') }} {{ trans('translation.cars') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.cars.index') }}">
                        {{ trans('translation.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.id') }}
                            </th>
                            <td>
                                {{ $car->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.driver') }}
                            </th>
                            <td>
                                {{ $car->driver->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.imei') }}
                            </th>
                            <td>
                                {{ $car->imei }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.plate_number') }}
                            </th>
                            <td>
                                {{ $car->plate_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.model') }}
                            </th>
                            <td>
                                {{ $car->model }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.color') }}
                            </th>
                            <td>
                                {{ $car->color }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.contact_person') }}
                            </th>
                            <td>
                                {{ $car->contact_person }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('translation.car.fields.description') }}
                            </th>
                            <td>
                                {{ $car->description }}
                            </td>
                        </tr>

                        <tr>
                            <th>
                                {{ trans('cruds.car.fields.container') }}
                            </th>
                            <td>
                                <a href="{{ route('admin.containers.create') }}"
                                    class="btn btn-primary">{{ trans('global.create') }}</a>

                                <table class="table table-bordered table-striped mt-2">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('translation.container.fields.id') }}</th>
                                            <th>{{ trans('translation.container.fields.type') }}</th>
                                            <th>{{ trans('translation.container.fields.model') }}</th>
                                            <th>{{ trans('translation.container.fields.status') }}</th>
                                            <th>{{ trans('translation.actions') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($car->containers as $container)
                                            <tr>
                                                <td>{{ $container->id }}</td>
                                                <td>{{ $container->type }}</td>
                                                <td>{{ $container->model }}</td>
                                                <td>{{ $container->status }}</td>
                                                <td>
                                                    <a href="{{ route('admin.containers.edit', $container->id) }}"
                                                        class="btn btn-sm btn-info">{{ trans('translation.edit') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.cars.index') }}">
                        {{ trans('translation.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="card">
        <ul class="nav nav-pills nav-justified mb-3" role="tablist">

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#car_car_link_histories" role="tab" data-toggle="tab">
                    {{ trans('translation.carLinkHistory') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#car_tasks" role="tab" data-toggle="tab">
                    {{ trans('translation.tasks') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#carTracking" role="tab" data-toggle="tab">
                    {{ trans('translation.carTracking') }}
                </a>
            </li>

        </ul>

        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="car_car_link_histories">
                @includeIf('admin.cars.relationships.carCarLinkHistories', [
                    'carLinkHistories' => $car->carCarLinkHistories,
                ])
            </div>
            <div class="tab-pane" role="tabpanel" id="car_tasks">
                @includeIf('admin.cars.relationships.carTasks', ['tasks' => $car->carTasks])
            </div>
            <div class="tab-pane" role="tabpanel" id="carTracking">
                @includeIf('admin.cars.relationships.carTracking', ['carTracking' => $car->carTracking])
            </div>
        </div>


    </div> --}}
@endsection
