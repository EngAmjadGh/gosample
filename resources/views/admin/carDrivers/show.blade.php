@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('cruds.carDriver.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.car-drivers.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.carDriver.fields.id') }}
                        </th>
                        <td>
                            {{ $carDriver->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.carDriver.fields.car') }}
                        </th>
                        <td>
                            {{ $carDriver->car->imei ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.carDriver.fields.driver') }}
                        </th>
                        <td>
                            {{ $carDriver->driver->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.carDriver.fields.is_linked') }}
                        </th>
                        <td>
                            {{ $carDriver->is_linked }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.car-drivers.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection