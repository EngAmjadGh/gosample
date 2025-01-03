@extends('layouts.master')
@section('title')
    @lang('translation.containers')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.containers')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.edit') }} {{ trans('translation.container.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.containers.update', [$container->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="car_id">{{ trans('translation.container.fields.car') }}</label>
                            <select class="form-control select2 {{ $errors->has('car') ? 'is-invalid' : '' }}"
                                name="car_id" id="car_id">
                                @foreach ($cars as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('car_id') ? old('car_id') : $container->car->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('car'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('car') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.container.fields.car_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="imei">{{ trans('translation.container.fields.sensor') }}</label>
                            <input class="form-control {{ $errors->has('imei') ? 'is-invalid' : '' }}" type="text"
                                name="imei" id="imei" value="{{ old('imei', $container->imei) }}" required>
                            @if ($errors->has('imei'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('imei') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.container.fields.imei_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">{{ trans('translation.container.fields.type') }}</label>
                            <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type"
                                id="type" required>
                                <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Container::TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('type', $container->type) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('type') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.container.fields.type_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="description">{{ trans('translation.container.fields.description') }}</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                                id="description">{{ old('description', $container->description) }}</textarea>
                            @if ($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.container.fields.description_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">{{ trans('translation.container.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Container::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', $container->status) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.container.fields.status_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="model">{{ trans('translation.container.fields.model') }}</label>
                            <input class="form-control {{ $errors->has('model') ? 'is-invalid' : '' }}" type="text"
                                name="model" id="model" value="{{ old('model', $container->model) }}" required>
                            @if ($errors->has('model'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('model') }}
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
