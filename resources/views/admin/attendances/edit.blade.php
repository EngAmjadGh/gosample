@extends('layouts.master')
@section('title')
    @lang('translation.attendances')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.attendances')
        @endslot
    @endcomponent
    <div class="card">
        <div class="card-header">
            {{ trans('translation.edit') }} {{ trans('translation.attendance') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.attendances.update', [$attendance->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="driver_id">{{ trans('translation.attendance.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id">
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('driver_id') ? old('driver_id') : $attendance->driver->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('driver'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('driver') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="checkin_time">{{ trans('translation.attendance.fields.checkin_time') }}</label>
                            <input class="form-control datetime {{ $errors->has('checkin_time') ? 'is-invalid' : '' }}"
                                type="text" name="checkin_time" id="checkin_time"
                                value="{{ old('checkin_time', $attendance->checkin_time) }}">
                            @if ($errors->has('checkin_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('checkin_time') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="checkout_time">{{ trans('translation.attendance.fields.checkout_time') }}</label>
                    <input class="form-control datetime {{ $errors->has('checkout_time') ? 'is-invalid' : '' }}"
                        type="text" name="checkout_time" id="checkout_time"
                        value="{{ old('checkout_time', $attendance->checkout_time) }}" required>
                    @if ($errors->has('checkout_time'))
                        <div class="invalid-feedback">
                            {{ $errors->first('checkout_time') }}
                        </div>
                    @endif
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
