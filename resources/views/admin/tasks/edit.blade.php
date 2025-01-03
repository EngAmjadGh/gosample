@extends('layouts.master')
@section('title')
    @lang('translation.tasks')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.tasks')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.edit') }} {{ trans('translation.task.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.tasks.update', [$task->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="from_location">{{ trans('translation.task.fields.from_location') }}</label>
                            <select class="form-control select2 {{ $errors->has('from_location') ? 'is-invalid' : '' }}"
                                name="from_location" id="from_location">
                                @foreach ($from_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('from_location') ? old('from_location') : $task->from->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('from_location'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('from_location') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.task.fields.from_location_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                            <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}"
                                name="to_location" id="to_location">
                                @foreach ($to_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('to_location') ? old('to_location') : $task->to->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('to_location'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('to_location') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.task.fields.to_location_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="billing_client">{{ trans('translation.task.fields.billing_client') }}</label>
                            <select class="form-control select2 {{ $errors->has('billing_client') ? 'is-invalid' : '' }}"
                                name="billing_client" id="billing_client">
                                @foreach ($billing_clients as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('billing_client') ? old('billing_client') : $task->client->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('billing_client'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('billing_client') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.task.fields.billing_client_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id">
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('driver_id') ? old('driver_id') : $task->driver->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('driver'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('driver') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.task.fields.driver_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ trans('translation.task.fields.task_type') }}</label>
                            <select class="form-control {{ $errors->has('task_type') ? 'is-invalid' : '' }}"
                                name="task_type" id="task_type">
                                <option value disabled {{ old('task_type', null) === null ? 'selected' : '' }}>
                                    {{ trans('global.pleaseSelect') }}</option>
                                @foreach (App\Models\Task::TASK_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('task_type', $task->task_type) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('task_type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task_type') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ trans('translation.task.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss">
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Task::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', 'NEW') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ trans('translation.task.fields.takasi') }}</label>
                            <select class="form-control {{ $errors->has('takasi') ? 'is-invalid' : '' }}" name="takasi"
                                id="takasi">
                                <option value disabled {{ old('takasi', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Task::TAKASI_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('takasi', 'NO') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('takasi'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('takasi') }}
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
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            initializeTimePicker();
        });
    </script>
@endsection
