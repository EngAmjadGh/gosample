@extends('layouts.master')

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.scheduledTask.title_singular') }}
        </div>

        @if ($errors->has('visit_hours'))
            <div class="alert alert-danger">
                {{ $errors->first('visit_hours') }}
            </div>
        @endif

        <div class="card-body">
            <form method="POST" action="{{ route('admin.scheduled-tasks.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('cruds.scheduledTask.fields.name') }}</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                                name="name" id="name" value="{{ old('name', '') }}" required>
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
                                        {{ old('status', 'enabled') === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
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
                                type="text" data-provider="flatpickr" data-date-format="Y-m-d" name="start_date"
                                id="start_date" value="{{ old('start_date') }}" required>
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
                                data-provider="flatpickr" data-date-format="Y-m-d" type="text" name="end_date"
                                id="end_date" value="{{ old('end_date') }}" required>
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
                                name="from_location_id[]" id="from_location_id" multiple required>
                                @foreach ($from_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('from_location_id', [])) ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
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
                        {{-- <div class="form-group">
                    <label class="required"
                        for="to_location_id">{{ trans('cruds.scheduledTask.fields.to_location') }}</label>
                    <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}"
                        name="to_location_id" id="to_location_id" required>
                        @foreach ($to_locations as $id => $entry)
                            <option value="{{ $id }}" {{ old('to_location_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('to_location'))
                        <div class="invalid-feedback">
                            {{ $errors->first('to_location') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.scheduledTask.fields.to_location_helper') }}</span>
                </div> --}}
                        <div class="form-group">
                            <label class="required"
                                for="to_location_id">{{ trans('cruds.scheduledTask.fields.to_location') }}</label>
                            <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}"
                                name="to_location_id" id="to_location_id" required>
                                @foreach ($to_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ old('to_location_id') == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
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
                                    <option value="{{ $id }}" {{ old('client_id') == $id ? 'selected' : '' }}>
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
                            <label class="required">{{ trans('cruds.scheduledTask.fields.task_type') }}</label>
                            <select class="form-control {{ $errors->has('task_type') ? 'is-invalid' : '' }}"
                                name="task_type" id="task_type" required>
                                <option value disabled {{ old('task_type', null) === null ? 'selected' : '' }}>
                                    {{ trans('global.pleaseSelect') }}</option>
                                @foreach (App\Models\ScheduledTask::TASK_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('task_type', 'SAMPLE') === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
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
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="days">{{ trans('cruds.scheduledTask.fields.days') }}</label>

                            <select class="form-control select2 {{ $errors->has('days') ? 'is-invalid' : '' }}"
                                name="days[]" id="days" multiple required>
                                @foreach ($days as $day)
                                    <option value="{{ $day }}"
                                        {{ in_array($day, old('days', [])) ? 'selected' : '' }}>
                                        {{ $day }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('days'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('days') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">

                        <div class="form-group">
                            <label for="driver_id">{{ trans('cruds.scheduledTask.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id" required>
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
                </div>
                {{-- <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="selected_hour">{{ trans('cruds.scheduledTask.fields.selected_hour') }}</label>
                            <select class="form-control {{ $errors->has('selected_hour') ? 'is-invalid' : '' }}"
                                name="selected_hour" id="selected_hour" required>
                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                @for ($hour = 0; $hour <= 23; $hour++)
                                    <option value="{{ $hour }}"
                                        {{ old('selected_hour') == $hour ? 'selected' : '' }}>
                                        {{ sprintf('%02d:00', $hour) }}
                                    </option>
                                @endfor
                            </select>
                            @if ($errors->has('selected_hour'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('selected_hour') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.scheduledTask.fields.selected_hour_helper') }}</span>
                        </div>
                    </div>
                </div> --}}


                <div class="row">
                    <div class="col-6">
                        <div id="visits-container">
                            <!-- Container to hold dynamically added visit input fields -->
                        </div>


                    </div>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-success mt-2" id="add-visit">
                        Add Visit
                    </button>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger mt-2" type="submit">
                        {{ trans('global.save') }}
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

        document.addEventListener("DOMContentLoaded", function() {

            // Function to add a new visit input field
            function addVisitInput() {
                const visitsContainer = $("#visits-container");

                // Create a new div to hold the visit input fields
                const visitDiv = $('<div class="row mb-2"></div>');

                // Create the first column for the label
                const labelColumn = $('<div class="col-3"></div>');

                // Create a label for selecting the visit hour
                const visitLabel = $('<label class="required mt-2">Visit Hour:</label>');
                labelColumn.append(visitLabel);

                // Create the second column for the time picker input
                const inputColumn = $('<div class="col-7 mt-2"></div>');

                // Create the visit hour time picker input field
                const visitInput = $(
                    '<input  class="form-control timepicker mt-2" name="visit_hours[]"  type="time" required>'
                );
                inputColumn.append(visitInput);

                // Create the third column for the remove button
                const buttonColumn = $('<div class="col-2 mt-2"></div>');

                // Create the remove button
                const removeButton = $(
                    '<button class="btn link-danger remove-payment-way fs-15" type="button"><i class="ri-delete-bin-line"></i></button>'
                );

                // Add an event listener to remove the visit input when the remove button is clicked
                removeButton.click(function() {
                    visitDiv.remove();
                });
                buttonColumn.append(removeButton);

                // Append the columns to the visit div
                visitDiv.append(labelColumn);
                visitDiv.append(inputColumn);
                visitDiv.append(buttonColumn);

                // Append the visit div to the visits container
                visitsContainer.append(visitDiv);

                // Initialize timepicker for the new input field
                visitInput.timepicker({
                    timeFormat: 'HH:mm', // Specify the format of the time
                    interval: 60, // Specify intervals for the time (in minutes)
                    dropdown: true, // Enable dropdown for the time picker
                    scrollbar: true, // Enable scrollbar for the time picker
                    // Add any additional options you need
                });
            }

            // Add event listener to the "Add Visit" button
            $("#add-visit").click(addVisitInput);

            function initializeTimePicker() {
                // Select the input field with the timepicker class
                $('.timepicker').timepicker({
                    timeFormat: 'H:i', // Specify the format of the time
                    interval: 30, // Specify intervals for the time (in minutes)
                    dropdown: true, // Enable dropdown for the time picker
                    scrollbar: true // Enable scrollbar for the time picker
                    // Add any additional options you need
                });
            }

        });
    </script>
@endsection
