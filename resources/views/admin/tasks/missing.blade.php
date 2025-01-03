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


@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

<div class="card">
    <div class="card-header">
        {{ trans('translation.missing') }} {{ trans('translation.sample.title') }}
    </div>
    <div class="card-body">

        <div class="row">
            <div class="form-group col">

                <div class="col-6">
                    <div class="mb-3">
                        <label for="sample">Enter Sample</label>
                        <input id="sample" class="form-control">
                    </div>
                </div>
                <!--end col-->
                <div class="col-lg-6">
                    <div class="text-start">
                        @can('mark_as_lost')
                            <button type="button" id="mark_as_lost" class="btn btn-danger waves-effect waves-light">Mark As
                                lost</button>
                        @endcan
                        <!-- Base Buttons -->
                        <button id="mark_as_confirmed" class="btn btn-success waves-effect waves-light">Mark As
                            confirmed</button>
                        @can('check_receiving_details')
                            <button id="get_details" class="btn btn-info waves-effect waves-light">Get Details</button>
                        @endcan
                    </div>
                </div>



            </div>
            <!--end col-->
        </div>
        <div id="resultCard" class="card"></div>
        <!--end row-->
    </div>
</div>

<div class="hstack flex-wrap gap-2">
    <button type="button" hidden id="success-message" data-toast data-toast-text="Success" data-toast-gravity="top"
        data-toast-position="center" data-toast-className="success" data-toast-duration="3000"
        class="btn btn-light w-xs"></button>
    <button type="button" hidden id="failed-message" data-toast data-toast-text="Error" data-toast-gravity="top"
        data-toast-position="center" data-toast-className="danger" data-toast-duration="3000"
        class="btn btn-light w-xs"></button>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/scandit-sdk@5.x"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    var user = {!! auth()->user()->toJson() !!};
    console.log(user);
    var username = user.email;
    var BatchSamples = [];
    $("#mark_as_lost").on("click", function() {
        $.ajax({
            type: "POST",
            url: "/api/client/samples/lost",
            data: JSON.stringify({
                'sample': $("#sample").val(),
                'marked_by': username
            }),
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
        }).done(function(response, textStatus, xhr) {
            var message = response.message;
            var data = response.data;

            if (response.status) {
                var cardContent = '<div class="card-body">';
                cardContent += '<h5 class="card-title">' + 'Result:' + '</h5>';
                cardContent += '<p class="card-text">' + response.message + '</p>';
                cardContent += '</div>';
                // Update the card with the content
                $('#resultCard').html(cardContent);
            } else {
                var cardContent = '<div class="card-body">';
                cardContent += '<h5 class="card-title">' + 'Result:' + '</h5>';
                cardContent += '<p class="card-text">' + response.message + '</p>';
                cardContent += '</div>';
                // Update the card with the content
                $('#resultCard').html(cardContent);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#failed-message').attr('data-toast-text', jqXHR.responseJSON.errorMessage);
            $('#failed-message').click();
        });
    });

    $("#get_details").on("click", function() {
        console.log('get')
        $.ajax({
            type: "POST",
            url: "/api/client/samples/details",
            data: JSON.stringify({
                'sample': $("#sample").val(),
                'username': username
            }),
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
        }).done(function(response, textStatus, xhr) {
            var message = response.message;
            var data = response.data;

            if (response.status) {
                // if (data.data.confirmed_by_client == 'YES') {
                //     $('#success-message').attr('data-toast-text', 'Sample is confirmed by ' + data.data
                //         .confirmed_by + ' at ' + data.data.updated_at);
                //     $('#success-message').click();
                // } else {
                //     if (data.data.confirmed_by_client == 'NO') {
                //         $('#success-message').attr('Sample is not confimed yet');
                //         $('#success-message').click();
                //     } else {
                //         $('#success-message').attr('Sample is lost, and is marked by ' + data.data
                //             .confirmed_by + ' at ' + data.data.updated_at);
                //         $('#success-message').click();
                //     }
                // }

                // API call succeeded
                // var message = response.message;
                // var data = response.data;

                // // Create the card content
                // var cardContent = '<div class="card-body">';
                // cardContent += '<h5 class="card-title">' + 'Result:' + '</h5>';
                // cardContent += '<p class="card-text">' + data + '</p>';
                // cardContent += '</div>';
                // // Update the card with the content
                // $('#resultCard').html(cardContent);


                // var message = response.message;
                // var data = response.data;

                // Create the card content
                var cardContent = '<div class="card-body">';
                // cardContent += '<h5 class="card-title">' + message + '</h5>';
                cardContent += '<p class="card-text">ID: ' + data.id + '</p>';
                cardContent += '<p class="card-text">Barcode ID: ' + data.barcode_id + '</p>';
                cardContent += '<p class="card-text">Location ID: ' + data.location_id + '</p>';
                cardContent += '<p class="card-text">Task ID: ' + data.task_id + '</p>';
                cardContent += '<p class="card-text">Temperature Type: ' + data.temperature_type +
                    '</p>';
                cardContent += '<p class="card-text">Confirmed By: ' + data.confirmed_by + '</p>';
                cardContent += '<p class="card-text">Confirmed By Client: ' + data.confirmed_by_client +
                    '</p>';
                cardContent += '<p class="card-text">Sample Type: ' + data.sample_type + '</p>';
                cardContent += '<p class="card-text">Status: ' + data.status + '</p>';
                cardContent += '<p class="card-text">Created At: ' + data.created_at + '</p>';
                cardContent += '<p class="card-text">Updated At: ' + data.updated_at + '</p>';
                // Add more properties as needed
                cardContent += '</div>';

                // Update the card with the content
                $('#resultCard').html(cardContent);

            } else {
                var cardContent = '<div class="card-body">';
                cardContent += '<h5 class="card-title">' + 'Result:' + '</h5>';
                cardContent += '<p class="card-text">' + response.data + '</p>';
                cardContent += '</div>';
                // Update the card with the content
                $('#resultCard').html(cardContent);

            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#resultCard').html('Error occurred during API request');

        });
    });

    $("#mark_as_confirmed").on("click", function() {
        // add to samples
        BatchSamples.push($("#sample").val());
        $.ajax({
            type: "POST",
            url: "/api/client/samples/confirm",
            data: JSON.stringify({
                'samples': BatchSamples,
                'confirmed_by': username
            }),
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
        }).done(function(response, textStatus, xhr) {
            BatchSamples = [];
            var message = response.message;
            var data = response.data;

            if (response.status) {
                var cardContent = '<div class="card-body">';
                cardContent += '<h5 class="card-title">' + 'Result:' + '</h5>';
                cardContent += '<p class="card-text">' + response.message + '</p>';
                cardContent += '</div>';
                // Update the card with the content
                $('#resultCard').html(cardContent);
            } else {
                var cardContent = '<div class="card-body">';
                cardContent += '<h5 class="card-title">' + 'Result:' + '</h5>';
                cardContent += '<p class="card-text">' + response.message + '</p>';
                cardContent += '</div>';
                // Update the card with the content
                $('#resultCard').html(cardContent);
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            BatchSamples = [];
            $('#failed-message').attr('data-toast-text', jqXHR.responseJSON.errorMessage);
            $('#failed-message').click();
        });
    });
</script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
