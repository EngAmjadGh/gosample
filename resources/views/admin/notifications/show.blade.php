@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.notification.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.notifications.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.id') }}
                        </th>
                        <td>
                            {{ $notification->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.task') }}
                        </th>
                        <td>
                            {{ $notification->task->collect_lat ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.from_location') }}
                        </th>
                        <td>
                            {{ $notification->from_location->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.to_location') }}
                        </th>
                        <td>
                            {{ $notification->to_location->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.driver') }}
                        </th>
                        <td>
                            {{ $notification->driver->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.billing_client') }}
                        </th>
                        <td>
                            {{ $notification->billing_client->english_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.type') }}
                        </th>
                        <td>
                            {{ $notification->type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.notifiable_type') }}
                        </th>
                        <td>
                            {{ $notification->notifiable_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.notifiable') }}
                        </th>
                        <td>
                            {{ $notification->notifiable }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.data') }}
                        </th>
                        <td>
                            {{ $notification->data }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.notification.fields.read_at') }}
                        </th>
                        <td>
                            {{ $notification->read_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.notifications.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection