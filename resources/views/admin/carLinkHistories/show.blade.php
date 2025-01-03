@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('cruds.carLinkHistory.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.car-link-histories.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.id') }}
                        </th>
                        <td>
                            {{ $carLinkHistory->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.driver') }}
                        </th>
                        <td>
                            {{ $carLinkHistory->driver->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.car') }}
                        </th>
                        <td>
                            {{ $carLinkHistory->car->imei ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.action') }}
                        </th>
                        <td>
                            {{ App\Models\CarLinkHistory::ACTION_SELECT[$carLinkHistory->action] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.car-link-histories.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection