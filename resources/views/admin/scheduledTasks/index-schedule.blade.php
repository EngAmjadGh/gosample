@extends('layouts.master')
@section('content')
    <h3 class="page-title">{{ trans('global.systemCalendar') }}</h3>
    <div class="card">
        <div class="card-header">
            {{ trans('global.systemCalendar') }}
        </div>

        <div class="card-body">


            <form id="filterForm">
                <div class="card-body">
                    <div class="col">
                        <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                        <select class="form-control select2" name="driver_id" id="driver_id">
                            <option value="">Select Driver</option>
                            @foreach ($drivers as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="billing_client">{{ trans('translation.task.fields.billing_client') }}</label>
                        <select class="form-control select2" name="billing_client" id="billing_client">
                            <option value="">Select Client</option>
                            @foreach ($clients as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->english_name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col">
                        <label for="from_location">{{ trans('translation.task.fields.from_location') }}</label>
                        <select class="form-control select2" name="from_location" id="from_location">
                            <option value="">Select Location</option>
                            @foreach ($locations as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                        <select class="form-control select2" name="to_location" id="to_location">
                            <option value="">Select Location</option>
                            @foreach ($locations as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>


                </div>
            </form>

            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />

            <div id='calendar'></div>
        </div>
    </div>



@endsection

@section('scripts')
    @parent
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js'></script>
    <script>
        $(document).ready(function() {

            // // page is now ready, initialize the calendar...
            // events = {!! json_encode($schedule) !!};
            // $('#calendar').fullCalendar({
            //     // put your options and callbacks here
            //     events: events,


            // })

            function initCalendar(events) {
                $('#calendar').fullCalendar('destroy');
                $('#calendar').fullCalendar({
                    events: events
                });
            }

            initCalendar({!! json_encode($schedule) !!});

            // Filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('admin.tasks.indexSchedule') }}',
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function(response) {
                        initCalendar(response.schedule);
                    }
                });
            });


        });
    </script>
@stop
