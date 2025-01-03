@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.scheduledTask.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.scheduled-tasks.update', [$scheduledTask->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('cruds.scheduledTask.fields.name') }}</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                                name="name" id="name" value="{{ old('name', $scheduledTask->name) }}" required>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.name_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ trans('cruds.scheduledTask.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss">
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('global.pleaseSelect') }}</option>
                                @foreach (App\Models\ScheduledTask::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', $scheduledTask->status) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.status_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="start_date">{{ trans('cruds.scheduledTask.fields.start_date') }}</label>
                            <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                type="date" name="start_date" id="start_date"
                                value="{{old('start_date') ?? $scheduledTask->start_date}}" required>
                            @if ($errors->has('start_date'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('start_date') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.start_date_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="end_date">{{ trans('cruds.scheduledTask.fields.end_date') }}</label>
                            <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                type="date" name="end_date" id="end_date"
                                value="{{ old('end_date', $scheduledTask->end_date) }}" required>
                            @if ($errors->has('end_date'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('end_date') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.end_date_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="from_location_id">{{ trans('cruds.scheduledTask.fields.from_location') }}</label>
                            <select class="form-control select2 {{ $errors->has('from_location') ? 'is-invalid' : '' }}"
                                name="from_location_id" id="from_location_id" required>
                                @foreach ($from_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('from_location_id') ? old('from_location_id') : $scheduledTask->from_location->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('from_location'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('from_location') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.from_location_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="to_location_id">{{ trans('cruds.scheduledTask.fields.to_location') }}</label>
                            <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}"
                                name="to_location_id" id="to_location_id" required>
                                @foreach ($to_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('to_location_id') ? old('to_location_id') : $scheduledTask->to_location->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('to_location'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('to_location') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.to_location_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="client_id">{{ trans('cruds.scheduledTask.fields.client') }}</label>
                            <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}"
                                name="client_id" id="client_id" required>
                                @foreach ($clients as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('client_id') ? old('client_id') : $scheduledTask->client->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('client'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('client') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.client_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="day">{{ trans('cruds.scheduledTask.fields.days') }}</label>
                            <select class="form-control {{ $errors->has('day') ? 'is-invalid' : '' }}" name="day"
                                id="day" required>
                                @foreach ($days as $day)
                                    <option value="{{ $day }}"
                                        {{ (old('day') ? old('day') : $scheduledTask->day ?? '') == $day ? 'selected' : '' }}>
                                        {{ $day }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('day'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('day') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">{{ trans('cruds.scheduledTask.fields.task_type') }}</label>
                            <select class="form-control {{ $errors->has('task_type') ? 'is-invalid' : '' }}"
                                name="task_type" id="task_type" required>
                                <option value disabled {{ old('task_type', null) === null ? 'selected' : '' }}>
                                    {{ trans('global.pleaseSelect') }}</option>
                                @foreach (App\Models\ScheduledTask::TASK_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('task_type', $scheduledTask->task_type) === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('task_type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task_type') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.task_type_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="added_by">{{ trans('cruds.scheduledTask.fields.added_by') }}</label>
                            <input class="form-control {{ $errors->has('added_by') ? 'is-invalid' : '' }}" type="text"
                                name="added_by" id="added_by" value="{{ old('added_by', $scheduledTask->added_by) }}">
                            @if ($errors->has('added_by'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('added_by') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.added_by_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="driver_id">{{ trans('cruds.scheduledTask.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id">
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ (old('driver_id') ? old('driver_id') : $scheduledTask->driver->id ?? '') == $id ? 'selected' : '' }}>
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
                            <label class="required"
                                for="selected_hour">{{ trans('cruds.scheduledTask.fields.selected_hour') }}</label>
                            <input type="time" class="form-control {{ $errors->has('selected_hour') ? 'is-invalid' : '' }}" id="selected_hour" name="selected_hour" value="{{old('selected_hour',$scheduledTask->selected_hour)}}">
                            @if ($errors->has('selected_hour'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('selected_hour') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.selected_hour_helper') }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
