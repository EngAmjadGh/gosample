@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.shipment.title') }}
        </div>

        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.shipments.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.id') }}
                            </th>
                            <td>
                                {{ $shipment->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.carrier') }}
                            </th>
                            <td>
                                {{ $shipment->carrier }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.sender_name') }}
                            </th>
                            <td>
                                {{ $shipment->sender_name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.sender_long') }}
                            </th>
                            <td>
                                {{ $shipment->sender_long }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.sender_lat') }}
                            </th>
                            <td>
                                {{ $shipment->sender_lat }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.sender_mobile') }}
                            </th>
                            <td>
                                {{ $shipment->sender_mobile }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.receiver_name') }}
                            </th>
                            <td>
                                {{ $shipment->receiver_name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.receiver_long') }}
                            </th>
                            <td>
                                {{ $shipment->receiver_long }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.receiver_lat') }}
                            </th>
                            <td>
                                {{ $shipment->receiver_lat }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.receiver_mobile') }}
                            </th>
                            <td>
                                {{ $shipment->receiver_mobile }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.reference_number') }}
                            </th>
                            <td>
                                {{ $shipment->reference_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.pickup_otp') }}
                            </th>
                            <td>
                                {{ $shipment->pickup_otp }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.notes') }}
                            </th>
                            <td>
                                {{ $shipment->notes }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.batch') }}
                            </th>
                            <td>
                                {{ $shipment->batch }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.dropoff_otp') }}
                            </th>
                            <td>
                                {{ $shipment->dropoff_otp }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.sla_code') }}
                            </th>
                            <td>
                                {{ $shipment->sla_code }}
                            </td>
                        </tr>
                        @if($task && isset($task->id))
                            <tr>
                                <th>
                                    Task
                                </th>
                                <td>
                                    <a href="{{route('admin.tasks.show', $task->id)}}" class="btn btn-xs btn-primary" target="_blank">{{$task->id}}</a>
                                </td>
                            </tr>
                            @isset($task->driver_id)
                                <tr>
                                    <th>
                                        Driver
                                    </th>
                                    <td>
                                        @if(isset($task->driver) && isset($task->driver->name))
                                            {{$task->driver->name ?? ''}}
                                        @endif
                                    </td>
                                </tr>
                            @endisset
                            <tr>
                                <th>
                                    Location from
                                </th>
                                <td>
                                    @if(isset($task->from))
                                        {{$task->from->name ?? ''}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Location to
                                </th>
                                <td>
                                    @if(isset($task->to))
                                        {{$task->to->name ?? ''}}
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.status_code') }}
                            </th>
                            <td>
                                {{ $shipment->status_code }}
                            </td>
                        </tr>



                    </tbody>
                </table>

                <div class="card">
                    <div class="card-header">
                        Assign Driver and Deliver Shipment
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Assign Driver</h5>
                                <form method="POST" action="{{ route('admin.shipments.assignDriver', $shipment->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="driver">Select Driver:</label>
                                        <select class="form-control" id="driver" name="driver">
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}"
                                                @if(isset($task) && isset($task->driver_id) && $task->driver_id == $driver->id)
                                                    selected
                                                @endif
                                                >{{ $driver->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Assign</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h5>Deliver Shipment</h5>
                                <form method="POST" action="{{ route('admin.shipments.deliver', $shipment->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Deliver</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.shipments.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
