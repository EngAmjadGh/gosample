@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboards
        @endslot
        @slot('title')
            Dashboard
        @endslot
    @endcomponent
    <style type="text/css">
        #mymap {
            border: 1px solid red;
            width: 100%;
            height: 800px;
        }

        #map {
            height: 800px;
            /* The height is 400 pixels */
            width: 100%;
            /* The width is the width of the web page */
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Filters</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('map') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="driver_id">{{ trans('translation.car.fields.driver') }}</label>
                                <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                    name="driver_id" id="driver_id">
                                    <option value="">Select Driver</option>
                                    @foreach ($drivers as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label class="required" for="imei">{{ trans('translation.car.fields.imei') }}</label>
                                <input class="form-control {{ $errors->has('imei') ? 'is-invalid' : '' }}" type="text"
                                    name="imei" id="imei" value="{{ old('imei', '') }}">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="required"
                                    for="plate_number">{{ trans('translation.car.fields.plate_number') }}</label>
                                <input class="form-control {{ $errors->has('plate_number') ? 'is-invalid' : '' }}"
                                    type="text" name="plate_number" id="plate_number"
                                    value="{{ old('plate_number', '') }}">
                            </div>
                            <div class="col-lg-6">
                                <p></p>
                                <button class="btn btn-danger" type="submit">
                                    {{ trans('translation.search') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Drivers</h4>
                </div>
                <div class="card-body">
                    <div id="map"></div>

                </div>
            </div>
        </div>

    </div>

    <!-- Grids in modals -->
    <button type="button" hidden class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalgrid"
        id="driver_pin">
    </button>

    <div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalgridLabel">Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="border">
                                <ul class="nav nav-pills custom-hover-nav-tabs">
                                    <li class="nav-item">
                                        <a href="#custom-hover-customere" data-bs-toggle="tab" aria-expanded="false"
                                            class="nav-link active">
                                            <i class="ri-user-fill nav-icon nav-tab-position"></i>
                                            <h5 class="nav-titl nav-tab-position m-0">Driver</h5>
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item">
                                                                                                                                                                                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#nav-badge-messages" role="tab" aria-selected="false">
                                                                                                                                                                                                                        Messages
                                                                                                                                                                                                                    </a>
                                                                                                                                                                                                                </li> -->
                                    <li class="nav-item">

                                        <a href="#custom-hover-description" data-bs-toggle="tab" aria-expanded="true"
                                            class="nav-link">
                                            <i class="ri-file-text-line nav-icon nav-tab-position">
                                            </i>
                                            <h5 class="nav-titl nav-tab-position m-0">Tasks</h5>

                                            {{-- <span class="badge bg-danger rounded-circle" id="badge_tasks"></span> --}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#car-details" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                            <i class="ri-car-line nav-icon nav-tab-position"></i>
                                            <h5 class="nav-titl nav-tab-position m-0">Car Details</h5>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#car_tracking_table" data-bs-toggle="tab" aria-expanded="false"
                                            class="nav-link">
                                            <i class="ri-map-pin-fill nav-icon nav-tab-position"></i>
                                            <h5 class="nav-titl nav-tab-position m-0">Car Tracking</h5>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content text-muted">
                                    <div class="tab-pane show active" id="custom-hover-customere">
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Full Name :</th>
                                                        <td class="text-muted" id="driver_name"></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Mobile :</th>
                                                        <td class="text-muted" id="driver_mobile"></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">E-mail :</th>
                                                        <td class="text-muted" id="driver_email"></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Car Plate Number:</th>
                                                        <td class="text-muted" id="plate_number_result">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">IMEI</th>
                                                        <td class="text-muted" id="imei_result"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="custom-hover-description">
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="tasks_table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">From</th>
                                                        <th scope="col">To</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Samples</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="car-details">
                                        <h6>Car Details</h6>
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="car_table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Plate Number</th>
                                                        <th scope="col">Model</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>1111 ewq</td>
                                                        <td>Test</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="car_tracking_table">
                                        <h6>Car Tracking</h6>
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="car_tracking_table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Address</th>
                                                        <th scope="col">Temp 5</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td></td>
                                                        <td>11</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card-body -->
                        </div>
                    </div>
                    <!--end col-->

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDf1ht01vFyWcfWS33mmdfd30qm5-uyWhM&libraries=drawing&callback=initMap"
        async defer></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDf1ht01vFyWcfWS33mmdfd30qm5-uyWhM&callback=initMap"
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.24/gmaps.js"></script>
    <script type="text/javascript">
        var locations = <?php print_r(json_encode($locations)); ?>;

        function initMap() {


            var myStyles = [{
                featureType: "poi",
                elementType: "labels",
                stylers: [{
                    visibility: "off"
                }]
            }];
            const mtc = {
                lat: 24.7597608,
                lng: 46.7141881
            };
            // The map, centered at MTC
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: mtc,
                styles: myStyles
            });


            $.each(locations, function(index, value) {
                if (value.driver_active_delayed_tasks.length > 0) {
                    value.icon = " {{ URL::asset('assets/images/pin-delayed.png') }}";
                    value.tasks_count = value.driver_active_tasks.length;
                } else {
                    if (value.driver_active_tasks.length > 0) {
                        value.icon = " {{ URL::asset('assets/images/pin-active.png') }}";
                        value.tasks_count = value.driver_active_tasks.length;
                    } else {
                        value.icon = "{{ URL::asset('assets/images/pin-no-task.png') }}";
                        value.tasks_count = value.driver_active_tasks.length;
                    }
                }


                if (value.lat == null || value.lng == null) {
                    // no need to display marker in this case
                } else {
                    var location = {
                        lat: value.lat,
                        lng: value.lng
                    };
                    const marker = new google.maps.Marker({
                        position: {
                            lat: Number(value.lat),
                            lng: Number(value.lng)
                        },
                        icon: value.icon,
                        label: {
                            text: value.tasks_count + '',
                            fontWeight: 'bold',
                            fontSize: '16px',
                        },
                        // icon: 'http://google-maps-icons.googlecode.com/files/sailboat-tourism.png',
                        map: map,
                        data: value

                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        console.log(value);
                        $('#driver_pin').click();
                        $('#badge_tasks').html(value.driver_active_tasks.length);
                        $('#driver_name').html(value.name);
                        $('#driver_mobile').html(value.mobile);
                        $('#driver_email').html(value.email);
                        $('#plate_number_result').html(value.plate_number);
                        $('#imei_result').html(value.imei);

                        $("#tasks_table tr").remove();

                        $('#tasks_table > tbody:last-child').append(
                            '<tr>' +
                            '<td>Id</td>' +
                            '<td>From</td>' +
                            '<td>To</td>' +
                            '<td>Status</td>' +
                            '<td>Samples</td>' +
                            '</tr>');
                        $.each(value.driver_active_tasks, function(index, task) {
                            $('#tasks_table > tbody:last-child').append(
                                '<tr>' +
                                '<td>' + task.id + '</td>' +
                                '<td>' + task.from.name + '</td>' +
                                '<td>' + task.to.name + '</td>' +
                                '<td>' + task.status + '</td>' +
                                '<td>' + task.samples.length + '</td>' +
                                '</tr>');
                        });
                        $("#car_table tr").remove();

                        $('#car_table > tbody:last-child').append(
                            '<tr>' +
                            '<td>Id</td>' +
                            '<td>Plate Number</td>' +
                            '<td>Model</td>' +
                            // '<td>Status</td>' +
                            // '<td>Samples</td>' +
                            '</tr>');
                        // $.each(value.car, function(index, car) {
                        $('#car_table > tbody:last-child').append(
                            '<tr>' +
                            '<td>' + value.car.id + '</td>' +
                            '<td>' + value.car.plate_number + '</td>' +
                            '<td>' + value.car.model + '</td>' +
                            // '<td>' + task.to.name + '</td>' +
                            // '<td>' + task.status + '</td>' +
                            // '<td>' + task.samples.length + '</td>' +
                            '</tr>');
                        // });


                        $("#car_tracking_table tr").remove();

                        $('#car_tracking_table > tbody:last-child').append(
                            '<tr>' +
                            '<td>Id</td>' +
                            '<td>Address</td>' +
                            '<td>Temp5</td>' +
                            '<td>Temp6</td>' +
                            '<td>Temp7</td>' +
                            '<td>Temp8</td>' +
                            '</tr>');
                        $('#car_tracking_table > tfoot').append(
                            '<tr>' +
                            '<td colspan="6">Total</td>' +
                            '<td>$947.55</td>' +
                            '</tr>');
                        $.each(value.car.car_tracking, function(index, record) {
                            $('#car_tracking_table > tbody:last-child').append(
                                '<tr>' +
                                '<td>' + record.id + '</td>' +
                                '<td>' + record.lat + record.lng + '</td>' +
                                '<td>' + record.temp5 + '</td>' +
                                '<td>' + record.temp6 + '</td>' +
                                '<td>' + record.temp7 + '</td>' +
                                '<td>' + record.temp8 + '</td>' +
                                '</tr>');
                        });




                        // var start = new google.maps.LatLng(value.driver_active_tasks[0].from.lat,value.driver_active_tasks[0].from.lng);
                        // var end = new google.maps.LatLng(value.driver_active_tasks[0].to.lat,value.driver_active_tasks[0].to.lng);
                        // var display = new google.maps.DirectionsRenderer();
                        // var services = new google.maps.DirectionsService();

                        // display.setMap(null);
                        // display.setMap(map);
                        // var request ={
                        //     origin : start,
                        //     destination:end,
                        //     travelMode: 'DRIVING'
                        // };
                        // services.route(request,function(result,status){
                        //     if(status =='OK'){
                        //         display.setDirections(result);
                        //     }
                        // });

                    });

                    google.maps.event.addListener(marker, "mouseover", function(evt) {
                        var label = value.name;
                        label.color = "black";
                        this.setLabel(label);
                    });
                    google.maps.event.addListener(marker, "mouseout", function(evt) {
                        var label = value.tasks_count + '';
                        label.color = "white";
                        this.setLabel(label);
                    });
                }

            });


        }

        window.initMap = initMap;





        // var mymap = new GMaps({
        //   el: '#mymap',
        //   lat: 24.7597608,
        //   lng: 46.7141881,
        //   zoom:12
        // });

        //     mymap.addMarker({
        //   lat: -12.043333,
        //   lng: -77.028333,
        //   title: 'Lima',
        //   click: function(e) {
        //     alert('You clicked in this marker');
        //   }
        // });

        // mymap.drawRoute({
        //   origin: [24.7597608, 46.7141881],
        //   destination: [24.6272971, 46.5548332],
        //   travelMode: 'driving',
        //   strokeColor: '#131540',
        //   strokeOpacity: 0.6,
        //   strokeWeight: 6
        // });

        //     $.each( locations, function( index, value ){
        //       if(value.lat == null || value.lng == null)
        //       {

        //       }else{
        //         mymap.addMarker({
        // 	      lat: value.lat,
        // 	      lng: value.lng,
        // 	      title: value.name,
        // 	      click: function(e) {
        //             $('#driver_pin').click();
        //             $('#driver_name').html(value.name);
        //             $('#driver_mobile').html(value.mobile);
        //             $('#driver_email').html(value.email);
        // 	      },
        // 	    });
        //       }

        //    });
    </script>

    <!-- <script src="{{ URL::asset('assets/libs/prismjs/prismjs.min.js') }}"></script>
                                                                                                                                                                <script src="https://maps.google.com/maps/api/js?key=AIzaSyCtSAR45TFgZjOs4nBFFZnII-6mMHLfSYI"></script>

                                                                                                                                                                <script src="{{ URL::asset('assets/libs/gmaps/gmaps.min.js') }}"></script>
                                                                                                                                                                <script src="{{ URL::asset('assets/js/pages/gmaps.init.js') }}"></script>
                                                                                                                                                                <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script> -->
@endsection
