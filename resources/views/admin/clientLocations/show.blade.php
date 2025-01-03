@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('cruds.clientLocation.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.client-locations.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.clientLocation.fields.id') }}
                        </th>
                        <td>
                            {{ $clientLocation->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientLocation.fields.client') }}
                        </th>
                        <td>
                            {{ $clientLocation->client->status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientLocation.fields.location') }}
                        </th>
                        <td>
                            {{ $clientLocation->location->name ?? '' }}
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.client-locations.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection