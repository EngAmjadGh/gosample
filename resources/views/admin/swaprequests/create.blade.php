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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="task_message" class="alert" style="display: none;"></div>

    <div class="card">
        <div class="card-header">
            {{ trans('translation.create') }} {{ trans('translation.swaprequest.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.swaprequests.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="driver_a">{{ trans('translation.swaprequest.fields.driver_a') }}</label>
                            <select class="form-control select {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_a" id="driver_a" required>
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}" {{ old('driver_a') == $id ? 'selected' : '' }}>
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
                            <label class="required"
                                for="task_id">{{ trans('translation.swaprequest.fields.task') }}</label>
                            <select class="form-control select2 {{ $errors->has('task') ? 'is-invalid' : '' }}"
                                name="task_id[]" id="task_id" multiple="multiple" required>
                                {{-- @foreach ($tasks as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, (array) old('task_id', [])) ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach --}}
                            </select>
                            @if ($errors->has('task'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('translation.swaprequest.fields.task_helper') }}</span>
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
                                    <option value="{{ $id }}" {{ old('driver_id') == $id ? 'selected' : '' }}>
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
                                        {{ old('status', 'new') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
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
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                    placeholder: "Select a record",
                    allowClear: true
                }

            );
        });
    </script>

    <script>
        $('#driver_a').change(function() {
            var driverId = $(this).val();
            if (driverId) {
                $.ajax({
                    url: '/api/swap/tasks/list',
                    type: "POST",
                    data: {
                        "driver_id": driverId
                    }, // Convert the data to JSON
                    dataType: "json",
                    success: function(response) {
                        $('#task_id').empty();
                        $('#task_message').empty().hide(); // Hide and clear message
                        if (response.status && response.data) {
                            console.log(response.data);
                            var taskCount = response.data.length;
                            if (taskCount > 0) {
                                $.each(response.data, function(index, item) {
                                    $('#task_id').append('<option value="' + item
                                        .id + '">' + item.id + ' ' +
                                        item.from.name + '</option>');
                                });
                                $('#task_message').addClass('alert-success').removeClass(
                                    'alert-warning').html('We have ' + taskCount +
                                    ' tasks.').show();
                            } else {
                                $('#task_message').addClass('alert-warning').removeClass(
                                    'alert-success').html('No tasks found.').show();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + status + " " + error);
                    }
                });
            } else {
                $('#task_id').empty();
                $('#task_message').empty().hide();
            }
        });
    </script>
@endsection
