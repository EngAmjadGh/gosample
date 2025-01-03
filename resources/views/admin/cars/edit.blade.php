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
            {{ trans('translation.edit') }} {{ trans('translation.car') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.cars.update', [$car->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="driver_id">{{ trans('translation.car.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id">
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('driver_id') ? old('driver_id') : $car->driver->id ?? '') == $id ? 'selected' : '' }}>
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
                                name="imei" id="imei" value="{{ old('imei', $car->imei) }}" required>
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
                                type="text" name="plate_number" id="plate_number"
                                value="{{ old('plate_number', $car->plate_number) }}" required>
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
                                name="model" id="model" value="{{ old('model', $car->model) }}">
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
                                name="color" id="color" value="{{ old('color', $car->color) }}">
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
                                value="{{ old('contact_person', $car->contact_person) }}" required>
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
                            <label for="afaqi">{{ trans('translation.car.fields.afaqi') }}</label>
                            <select class="form-control {{ $errors->has('afaqi') ? 'is-invalid' : '' }}" name="afaqi"
                                id="afaqi">
                                <option value="1" {{ old('afaqi', $car->afaqi) == '1' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="0" {{ old('afaqi', $car->afaqi) == '0' ? 'selected' : '' }}>No</option>
                            </select>
                            @if ($errors->has('afaqi'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('afaqi') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="description">{{ trans('translation.car.fields.description') }}</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                                id="description">{{ old('description', $car->description) }}</textarea>
                            @if ($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
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

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            initializeTimePicker();
        });
    </script>
@endsection
