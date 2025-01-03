@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('cruds.clientAccount.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.client-accounts.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.clientAccount.fields.id') }}
                        </th>
                        <td>
                            {{ $clientAccount->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientAccount.fields.client') }}
                        </th>
                        <td>
                            {{ $clientAccount->client->status ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientAccount.fields.username') }}
                        </th>
                        <td>
                            {{ $clientAccount->username }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientAccount.fields.password') }}
                        </th>
                        <td>
                            {{ $clientAccount->password }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientAccount.fields.name') }}
                        </th>
                        <td>
                            {{ $clientAccount->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.clientAccount.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\ClientAccount::STATUS_SELECT[$clientAccount->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.client-accounts.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection