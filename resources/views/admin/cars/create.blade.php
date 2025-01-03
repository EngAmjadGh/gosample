@extends('layouts.master')
@section('title')
    @lang('translation.cars')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.cars')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.create') }} {{ trans('translation.car') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.cars.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="driver_id">{{ trans('translation.car.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id">
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}" {{ old('driver_id') == $id ? 'selected' : '' }}>
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
                            <label class="required" for="imei">{{ trans('translation.car.fields.imei') }}</label>
                            <input class="form-control {{ $errors->has('imei') ? 'is-invalid' : '' }}" type="text"
                                name="imei" id="imei" value="{{ old('imei', '') }}" required>
                            @if ($errors->has('imei'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('imei') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="plate_number">{{ trans('translation.car.fields.plate_number') }}</label>
                            <input class="form-control {{ $errors->has('plate_number') ? 'is-invalid' : '' }}"
                                type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', '') }}"
                                required>
                            @if ($errors->has('plate_number'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('plate_number') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="model">{{ trans('translation.car.fields.model') }}</label>
                            <input class="form-control {{ $errors->has('model') ? 'is-invalid' : '' }}" type="text"
                                name="model" id="model" value="{{ old('model', '') }}">
                            @if ($errors->has('model'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('model') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="color">{{ trans('translation.car.fields.color') }}</label>
                            <input class="form-control {{ $errors->has('color') ? 'is-invalid' : '' }}" type="text"
                                name="color" id="color" value="{{ old('color', '') }}">
                            @if ($errors->has('color'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('color') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="contact_person">{{ trans('translation.car.fields.contact_person') }}</label>
                            <input class="form-control {{ $errors->has('contact_person') ? 'is-invalid' : '' }}"
                                type="text" name="contact_person" id="contact_person"
                                value="{{ old('contact_person', '') }}" required>
                            @if ($errors->has('contact_person'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('contact_person') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="description">{{ trans('translation.car.fields.description') }}</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                                id="description">{{ old('description') }}</textarea>
                            @if ($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="afaqi">{{ trans('translation.car.fields.afaqi') }}</label>
                            <select class="form-control {{ $errors->has('afaqi') ? 'is-invalid' : '' }}" name="afaqi"
                                id="afaqi" required>
                                <option value="0" {{ old('afaqi') == 'disabled' ? 'selected' : '' }}>
                                    No
                                </option>
                                <option value="1" {{ old('afaqi') == 'enabled' ? 'selected' : '' }}>
                                    Yes
                                </option>
                            </select>
                        </div>
                    </div>
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
