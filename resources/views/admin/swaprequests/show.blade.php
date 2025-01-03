@extends('layouts.master')
@section('title')
    @lang('translation.swaprequests')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.swaprequests')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('translation.swaprequest.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.swaprequests.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.swaprequest.fields.id') }}
                        </th>
                        <td>
                            {{ $swaprequest->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.swaprequest.fields.task') }}
                        </th>
                        <td>
                            {{ $swaprequest->task->collect_lat ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.swaprequest.fields.driver') }}
                        </th>
                        <td>
                            {{ $swaprequest->driver->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.swaprequest.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Swap::STATUS_SELECT[$swaprequest->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.swaprequests.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection