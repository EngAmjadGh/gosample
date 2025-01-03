@extends('layouts.master')
@section('title')
    @lang('translation.swaprequests')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.swaprequests')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.edit') }} {{ trans('translation.swaprequest.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.swaprequests.update', [$swaprequest->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="task_id">{{ trans('translation.swaprequest.fields.task') }}</label>
                            <select class="form-control select2 {{ $errors->has('task') ? 'is-invalid' : '' }}"
                                name="task_id" id="task_id" required>
                                @foreach ($tasks as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('task_id') ? old('task_id') : $swaprequest->task->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>



                            @if ($errors->has('task'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.swaprequest.fields.task_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="driver_a">{{ trans('translation.swaprequest.fields.driver_a') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_a" id="driver_a" required>
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('driver_a') ? old('driver_a') : $swaprequest->driverA->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('driver'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('driver') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.swaprequest.fields.driver_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="driver_id">{{ trans('translation.swaprequest.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id" required>
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('driver_id') ? old('driver_id') : $swaprequest->driver->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('driver'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('driver') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.swaprequest.fields.driver_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">{{ trans('translation.swaprequest.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Swap::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', $swaprequest->status) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.swaprequest.fields.status_helper') }}</span>
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
