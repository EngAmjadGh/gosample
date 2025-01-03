@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('cruds.elmNotification.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.elm-notifications.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.elmNotification.fields.id') }}
                        </th>
                        <td>
                            {{ $elmNotification->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.elmNotification.fields.task') }}
                        </th>
                        <td>
                            {{ $elmNotification->task->collect_lat ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.elmNotification.fields.type') }}
                        </th>
                        <td>
                            {{ $elmNotification->type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.elmNotification.fields.response_body') }}
                        </th>
                        <td>
                            {{ $elmNotification->response_body }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.elm-notifications.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection