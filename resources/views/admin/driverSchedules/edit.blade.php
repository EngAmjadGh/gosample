@extends('layouts.master')
@section('title')
    @lang('translation.driverSchedules')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.driverSchedules')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.edit') }} {{ trans('translation.driverSchedule.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.driver-schedules.update", [$driverSchedule->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="from_locationd">{{ trans('translation.driverSchedule.fields.from_location') }}</label>
                <select class="form-control select2 {{ $errors->has('from_location') ? 'is-invalid' : '' }}" name="from_locationd" id="from_location">
                    @foreach($from_locations as $id => $entry)
                        <option value="{{ $id }}" {{ (old('from_locationd') ? old('from_locationd') : $driverSchedule->from->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('from_location'))
                    <div class="invalid-feedback">
                        {{ $errors->first('from_location') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.driverSchedule.fields.from_location_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="to_location">{{ trans('translation.driverSchedule.fields.to_location') }}</label>
                <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}" name="to_location" id="to_location">
                    @foreach($to_locations as $id => $entry)
                        <option value="{{ $id }}" {{ (old('to_location') ? old('to_location') : $driverSchedule->to->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('to_location'))
                    <div class="invalid-feedback">
                        {{ $errors->first('to_location') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.driverSchedule.fields.to_location_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="driver_id">{{ trans('translation.driverSchedule.fields.driver') }}</label>
                <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}" name="driver_id" id="driver_id">
                    @foreach($drivers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $driverSchedule->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('driver'))
                    <div class="invalid-feedback">
                        {{ $errors->first('driver') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.driverSchedule.fields.driver_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('translation.driverSchedule.fields.note') }}</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{{ old('note', $driverSchedule->note) }}</textarea>
                @if($errors->has('note'))
                    <div class="invalid-feedback">
                        {{ $errors->first('note') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.driverSchedule.fields.note_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="plate_number">{{ trans('translation.driverSchedule.fields.plate_number') }}</label>
                <input class="form-control {{ $errors->has('plate_number') ? 'is-invalid' : '' }}" type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $driverSchedule->plate_number) }}">
                @if($errors->has('plate_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('plate_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.driverSchedule.fields.plate_number_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('translation.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection