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
    <div class="card">
        <div class="card-header">
            {{ trans('translation.show') }} {{ trans('translation.task.title_singular') }}
        </div>

        <div class="card-body" id="print_area">
            <div class="container-fluid">
                <div class="row justify-content-center pull-up  px-5">
                    <div class="col-md-12">
                        <div class="card">
                            <button onclick="printReport()" class='print_btn float-right'>
                                <i class="mdi mdi-printer " style="font-size: 20px;"></i> Print
                            </button>
                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tbody>
                                    <tr>
                                        <td>Requestor</td>
                                        <td>{{ $task->client ? $task->client->english_name : '' }}</td>
                                        Arrival of Pick Up Location
                                        <td>Billed To</td>
                                        <td>{{ $task->client ? $task->client->english_name : '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pick Up Location</td>
                                        <td>{{ $task->from->name }}</td>
                                        <td>Delivery Location</td>
                                        <td>{{ $task->to->name }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}
                        <div class="card mt-2">
                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tr>
                                        <th colspan='4' class='text-center'>
                                            <h3>Task Information</h3>
                                        </th>
                                    </tr>
                                    <tbody>

                                    <tr>
                                        <td>Task Creation Date</td>
                                        <td>{{ $task->created_at }}</td>
                                        <td>Receiving Date</td>
                                        <td>{{ $task->created_at }}</td>
                                    </tr>

                                    <tr>
                                        <td>Type of Request</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $task->type)) }}</td>
                                        <td>Driver Name</td>
                                        <td>
                                            @if (!empty($task->driver))
                                                {{ $task->driver->name }}
                                            @endif
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>Bag QTY</td>
                                        <td>{{ $bag_count }}</td>
                                        <td>Sample QTY</td>
                                        <td>{{ $sample_count }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}
                        <div class="card mt-2">
                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tr>
                                        <th colspan='4' class='text-center'>
                                            <h3>COLLECTION INFORMATION</h3>
                                        </th>
                                    </tr>
                                    <tbody>

                                    <tr>
                                        <td><strong>Arrival of Pick Up Location</strong></td>
                                        <td><strong>Departure of Pick Up Location</strong></td>
                                        <td><strong>Duration of Pick Up</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $task->from_location_arrival_time }}</td>
                                        <td>{{ $task->collection_date }}</td>
                                        <td>
                                            &nbsp;{{ round((strtotime($task->collection_date) - strtotime($task->from_location_arrival_time)) / 60) }}
                                            Minute(s)
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}
                        <div class="card mt-2">
                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tr>
                                        <th colspan='4' class='text-center'>
                                            <h3>SAMPLE PLACEMENT INFORMATION
                                            </h3>
                                        </th>
                                    </tr>
                                    <tbody>
                                    <tr>
                                        <td><strong>Sample Receiving</strong></td>
                                        <td><strong>Sample In</strong></td>
                                        <td><strong>Duration</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $task->collection_date }}</td>
                                        <td>{{ $task->freezer_date }}</td>
                                        <td>{{ round((strtotime($task->freezer_date) - strtotime($task->collection_date)) / 60) }}
                                            Minute(s)
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}
                        <div class="card mt-2">
                            <div class="card-body pt-3">
                                <table class="table table-bordered " border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tr>
                                        <th colspan='4' class='text-center text-uppercase'>
                                            <h3>Sample Delivery</h3>
                                        </th>
                                    </tr>
                                    <tbody>

                                    <tr>
                                        <td><strong>Sample Out</strong></td>
                                        <td><strong>Sample Delivery</strong></td>
                                        <td><strong>Duration</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $task->freezer_out_date }}</td>
                                        <td>{{ $task->close_date }}</td>
                                        <td> {{ round((strtotime($task->close_date) - strtotime($task->freezer_out_date)) / 60) }}
                                            Minute(s)
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}
                        {{--
                                                <div class="card mt-2">
                                                    <div class="card-body pt-3">
                                                        <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                                            width="100%">
                                                            <tr>
                                                                <th colspan='4' class='text-center'>
                                                                    <h3>DELIVERY INFORMATION </h3>
                                                                </th>
                                                            </tr>
                                                            <tbody>

                                                                <tr>
                                                                    <td><strong>Scan location time</strong></td>
                                                                    <td><strong>Sign time</strong></td>
                                                                    <td><strong>Duration</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ $task->to_location_arrival_time }}</td>
                                                                    <td>{{ $task->close_date }}</td>
                                                                    <td>
                                                                        {{ round((strtotime($task->close_date) - strtotime($task->close_date)) / 60) }}
                                                                        Minute(s)&nbsp;
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> --}}

                        {{-- end .card --}}

                        @if (count($bags) > 0)
                            <div class="card mt-2">
                                <div class="card-body pt-3">

                                    <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                           width="100%">
                                        <tr>
                                            <th>BAG CODE</th>
                                            <th>BAGS #</th>
                                            <th>SAMPLE #</th>
                                            <th>TYPE</th>
                                            <th>TEMPERATURE</th>
                                            {{--                                    <th>AVG</th> --}}
                                        </tr>
                                        <tbody>
                                        @foreach ($bags as $key => $bag)
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ count($bag) }}
                                                    @foreach ($bag as $sample)
                                                        [{{ $sample->barcode_id }}]
                                                    @endforeach
                                                </td>

                                                <td>
                                                    {{ $bag[0]->sample_type }}
                                                </td>
                                                <td>
                                                    @if ($bag[0]->temperature_type == 'ROOM')
                                                        @if(isset($carTracking->cnt) && $carTracking->cnt > 0)
                                                            @if(!empty($carTracking->total_temp_1) && ($carTracking->total_temp_1/$carTracking->cnt) >= 15 && ($carTracking->total_temp_1/$carTracking->cnt) <= 25)
                                                                {{ round($carTracking->total_temp_1/$carTracking->cnt,2).' °C' }}
                                                            @elseif(!empty($carTracking->total_temp_2) && ($carTracking->total_temp_2/$carTracking->cnt) >= 15 && ($carTracking->total_temp_2/$carTracking->cnt) <= 25)
                                                                {{ round($carTracking->total_temp_2/$carTracking->cnt,2).' °C' }}
                                                            @elseif(!empty($carTracking->total_temp_3) && ($carTracking->total_temp_3/$carTracking->cnt) >= 15 && ($carTracking->total_temp_3/$carTracking->cnt) <= 25)
                                                                {{ round($carTracking->total_temp_3/$carTracking->cnt,2).' °C' }}
                                                            @else
                                                                +15C TO +25C
                                                            @endif
                                                        @else
                                                            +15C TO +25C
                                                        @endif
                                                    @elseif ($bag[0]->temperature_type == 'REFRIGERATE')
                                                        @if(isset($carTracking->cnt) && $carTracking->cnt > 0)
                                                            @if(!empty($carTracking->total_temp_1) && ($carTracking->total_temp_1/$carTracking->cnt) >= 2 && ($carTracking->total_temp_1/$carTracking->cnt) <= 8)
                                                                {{ round($carTracking->total_temp_1/$carTracking->cnt,2).' °C' }}
                                                            @elseif(!empty($carTracking->total_temp_2) && ($carTracking->total_temp_2/$carTracking->cnt) >= 2 && ($carTracking->total_temp_2/$carTracking->cnt) <= 8)
                                                                {{ round($carTracking->total_temp_2/$carTracking->cnt,2).' °C' }}
                                                            @elseif(!empty($carTracking->total_temp_3) && ($carTracking->total_temp_3/$carTracking->cnt) >= 2 && ($carTracking->total_temp_3/$carTracking->cnt) <= 8)
                                                                {{ round($carTracking->total_temp_3/$carTracking->cnt,2).' °C' }}
                                                            @else
                                                                +2C TO +8C
                                                            @endif
                                                        @else
                                                            +2C TO +8C
                                                        @endif
                                                    @elseif ($bag[0]->temperature_type == 'FROZEN')
                                                        @if(isset($carTracking->cnt) && $carTracking->cnt > 0)
                                                            @if(!empty($carTracking->total_temp_1) && ($carTracking->total_temp_1/$carTracking->cnt) >= -18 && ($carTracking->total_temp_1/$carTracking->cnt) <= 0)
                                                                {{ round($carTracking->total_temp_1/$carTracking->cnt,2).' °C' }}
                                                            @elseif(!empty($carTracking->total_temp_2) && ($carTracking->total_temp_2/$carTracking->cnt) >= -18 && ($carTracking->total_temp_2/$carTracking->cnt) <= 0)
                                                                {{ round($carTracking->total_temp_2/$carTracking->cnt,2).' °C' }}
                                                            @elseif(!empty($carTracking->total_temp_3) && ($carTracking->total_temp_3/$carTracking->cnt) >= -18 && ($carTracking->total_temp_3/$carTracking->cnt) <= 0)
                                                                {{ round($carTracking->total_temp_3/$carTracking->cnt,2).' °C' }}
                                                            @else
                                                                0C TO -18C
                                                            @endif
                                                        @else
                                                            0C TO -18C
                                                        @endif
                                                    @endif
                                                </td>
                                                {{--                                        <td> --}}

                                                {{--                                        </td> --}}
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        @endif
                        {{-- end .card --}}


                        <div class="card mt-2">
                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tbody>

                                    <tr>
                                        <td><strong>Arrival of Pick Up Location </strong></td>
                                        <td><strong>Departure of Pick Up Location</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $task->from_location_arrival_time }}</td>
                                        <td>{{ $task->close_date }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}


                        <div class="card mt-2">
                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                       width="100%">
                                    <tr>
                                        <th colspan='2' class='text-center'>
                                            <h3>Signature </h3>
                                        </th>
                                    </tr>
                                    <tbody>

                                    <tr>
                                        <td><strong>Signature</strong></td>
                                        <td><strong>Deliver Signature</strong></td>
                                    </tr>
                                    <tr>
                                        <td style='witdth:50%;height:100px;text-align:center'>
                                            <img style='height:100%;margin:0 auto' src="{{ $task->signature }}">
                                        </td>
                                        <td style='witdth:50%;height:100px;text-align:center'>
                                            <img style='height:100%;margin:0 auto'
                                                 src="{{ $task->deliver_signature }}">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end .card --}}


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Trip</h4>
                </div>
                <div class="card-body">
                    <div id="map"></div>

                </div>
            </div>
        </div>

    </div>

@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDf1ht01vFyWcfWS33mmdfd30qm5-uyWhM&callback=initMap"
            defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.24/gmaps.js"></script>

    <script>
        var task = <?php print_r(json_encode($task)); ?>;
        console.log(task);

        function printReport() {
            var prtContent = document.getElementById("print_area");
            var WinPrint = window.open();
            WinPrint.document.write(
                `<div id='barcode_area' style='width:100%;margin-top:50px;margin:0 auto; text-align:center'>
                <style>@page {   size:auto; margin:20mm; margin-top:5mm ; } .print_btn{display:none} table{margin-bottom:20px; page-break-inside: avoid;}</style>`

                +
                prtContent.innerHTML +
                `</div>`
            );
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();

        };

        function initMap() {
            const positionA = {
                lat: Number(task.collect_lat),
                lng: Number(task.collect_lng)
            };
            const position = {
                lat: Number(task.close_lat),
                lng: Number(task.close_lng)
            };

            var myStyles = [{
                featureType: "poi",
                elementType: "labels",
                stylers: [{
                    visibility: "off"
                }]
            }];

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: position,
                styles: myStyles

            });


            const contentString =
                `<div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="ps-0" scope="row">Task ID :</th>
                                <td class="text-muted" id="driver_name">` + task.id + ` </td>
                            </tr>


                            </tbody>
                    </table>
                </div>`;
            const infowindow = new google.maps.InfoWindow({
                content: contentString,
                ariaLabel: task.from.name,
            });

            // for (let index = 0; index < task.car_tracking.length; index++) {
            //     const element = task.car_tracking[index];
            //     console.log(element);
            //     const markerA = new google.maps.Marker({
            //     position: {
            //         lat:Number(element.lat),
            //         lng:Number(element.lng)
            //     },
            //     label: {text:'P' + index, fontWeight: 'bold', fontSize: '16px',},
            //     map: map,
            // });

            // }

            const markerA = new google.maps.Marker({
                position: positionA,
                label: {
                    text: 'A',
                    fontWeight: 'bold',
                    fontSize: '16px',
                },

                map: map,
            });
            const markerB = new google.maps.Marker({
                position: position,
                label: {
                    text: 'B',
                    fontWeight: 'bold',
                    fontSize: '16px',
                },
                map: map,
            });

            // google.maps.event.addListener(marker, 'click', function() {
            //     $('#driver_pin').click();
            //     $('#driver_name').html(value.name);
            //     $('#driver_mobile').html(value.mobile);
            //     $('#driver_email').html(value.email);
            //     $('#plate_number_result').html(value.plate_number);
            //     $('#imei_result').html(value.imei);
            //     $("#tasks_table tr").remove();
            // });

            // google.maps.event.addListener(markerA, "mouseover", function(evt) {
            //     var label = task.from.name;
            //     label.color="black";
            //     this.setLabel(label);
            // });
            // google.maps.event.addListener(markerA, "mouseout", function(evt) {
            //     var label = 'A';
            //     label.color="white";
            //     this.setLabel(label);
            // });
            // google.maps.event.addListener(markerB, "mouseover", function(evt) {
            //     var label = task.to.name;
            //     label.color="black";
            //     this.setLabel(label);
            // });
            // google.maps.event.addListener(markerB, "mouseout", function(evt) {
            //     var label = 'B';
            //     label.color="white";
            //     this.setLabel(label);
            // });

            // markerA.addListener("click", () => {
            //     infowindow.open({
            //     anchor: markerA,
            //     map,
            //     });
            // });

            // var start = new google.maps.LatLng(task.collect_lat,task.collect_lng);
            // var end = new google.maps.LatLng(task.close_lat,task.close_lng);
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

        }
    </script>
@endsection
