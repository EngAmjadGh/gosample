@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.moneyTransfer.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.money-transfers.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.id') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.driver') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->driver->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.client') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->client->english_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.from_location') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->from_location->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.to_location') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->to_location->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.status') }}
                            </th>
                            <td>
                                {{ App\Models\MoneyTransfer::STATUS_SELECT[$moneyTransfer->status] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.from_location_otp') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->from_location_otp }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.to_otp') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->to_otp }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.moneyTransfer.fields.amount') }}
                            </th>
                            <td>
                                {{ $moneyTransfer->amount }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.money-transfers.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
