@extends('layouts.master')
@section('title')
    @lang('translation.drivers')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.drivers')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.edit') }} {{ trans('translation.driver') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.drivers.update', [$driver->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('translation.driver.fields.name') }}</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                                name="name" id="name" value="{{ old('name', $driver->name) }}" required>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="password">{{ trans('translation.driver.fields.password') }}</label>
                            <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                                name="password" id="password">
                            @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">{{ trans('translation.driver.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Driver::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', $driver->status) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="username">{{ trans('translation.driver.fields.username') }}</label>
                            <input class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" type="text"
                                name="username" id="username" value="{{ old('username', $driver->username) }}" required>
                            @if ($errors->has('username'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('username') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="mobile">{{ trans('translation.driver.fields.mobile') }}</label>
                            <input class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}" type="text"
                                name="mobile" id="mobile" value="{{ old('mobile', $driver->mobile) }}" required>
                            @if ($errors->has('mobile'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('mobile') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="email">{{ trans('translation.driver.fields.email') }}</label>
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text"
                                name="email" id="email" value="{{ old('email', $driver->email) }}" required>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="working_hours_start">{{ trans('translation.driver.fields.working_hours_start') }}</label>
                            <input class="form-control {{ $errors->has('working_hours_start') ? 'is-invalid' : '' }}"
                                type="time" name="working_hours_start" id="working_hours_start"
                                value="{{ old('working_hours_start', $driver->working_hours_start) }}" required>
                            @if ($errors->has('working_hours_start'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('working_hours_start') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="working_hours_end">{{ trans('translation.driver.fields.working_hours_end') }}</label>
                            <input class="form-control {{ $errors->has('working_hours_end') ? 'is-invalid' : '' }}"
                                type="time" name="working_hours_end" id="working_hours_end"
                                value="{{ old('working_hours_end', $driver->working_hours_end) }}" required>
                            @if ($errors->has('working_hours_end'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('working_hours_end') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="second_shift_working_hours_start">{{ trans('translation.driver.fields.second_shift_working_hours_start') }}</label>
                            <input
                                class="form-control {{ $errors->has('second_shift_working_hours_start') ? 'is-invalid' : '' }}"
                                type="time" name="second_shift_working_hours_start" id="second_shift_working_hours_start"
                                value="{{ old('second_shift_working_hours_start', $driver->second_shift_working_hours_start) }}"
                                required>
                            @if ($errors->has('second_shift_working_hours_start'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('second_shift_working_hours_start') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="second_shift_working_hours_end">{{ trans('translation.driver.fields.second_shift_working_hours_end') }}</label>
                            <input
                                class="form-control {{ $errors->has('second_shift_working_hours_end') ? 'is-invalid' : '' }}"
                                type="time" name="second_shift_working_hours_end" id="second_shift_working_hours_end"
                                value="{{ old('second_shift_working_hours_end', $driver->second_shift_working_hours_end) }}"
                                required>
                            @if ($errors->has('second_shift_working_hours_end'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('second_shift_working_hours_end') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ trans('translation.driver.fields.language') }}</label>
                            <select class="form-control {{ $errors->has('language') ? 'is-invalid' : '' }}"
                                name="language" id="language">
                                <option value disabled {{ old('language', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Driver::LANGUAGE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('language', $driver->language) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('language'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('language') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">

                        <div class="form-group">
                            <label class="required" for="zone_id">{{ trans('translation.driver.fields.zone') }}</label>
                            <select class="form-control select {{ $errors->has('zone') ? 'is-invalid' : '' }}"
                                name="zone_id" id="zone_id" required>
                                <option value="">Select zone</option>
                                @foreach ($zones as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('zone_id') ? old('zone_id') : $driver->zone->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('zone'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('zone') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="national_id">{{ trans('translation.driver.fields.national_id') }}</label>
                            <div class="form-icon">

                                <input
                                    class="form-control form-control-icon {{ $errors->has('national_id') ? 'is-invalid' : '' }}"
                                    type="text" placeholder="1xxxxxx" name="national_id" id="national_id"
                                    value="{{ old('national_id', $driver->national_id) }}" required>
                                <i class="ri-cellphone-line"></i>
                            </div>
                            @if ($errors->has('national_id'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('national_id') }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="form-group mt-2">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('translation.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
