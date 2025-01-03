@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('css')
    <meta http-equiv="refresh" content="15">
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Car Dashboard
        @endslot
        @slot('title')
            Car Dashboard
        @endslot
    @endcomponent
    <div class="row">
        @foreach ($cars as $car)
            <div class="col-md-3 mb-4">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                <h5 class="card-title font-weight-bold text-uppercase mb-0">{{ $car['name'] }}</h5>
                                </p>
                            </div>
                        </div>

                        <p class="card-text">Vehicle IMEI: {{ $car['i'] }}</p>
                        <hr>
                        <h6 class="card-subtitle mb-2 text-muted">Profile:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="card-text">Vehicle Type: {{ $car['profile']['vehicle_type'] }}</p>
                                <p class="card-text">Plate Number: {{ $car['profile']['plate_number'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="card-text">Max Capacity: {{ $car['profile']['max_capacity'] }}</p>
                                <p class="card-text">Seats: {{ $car['profile']['seats'] }}</p>
                            </div>
                        </div>

                        <hr>
                        <h6 class="card-subtitle mb-2 text-muted">Sensors:</h6>
                        <ul>
                            @foreach ($car['sensors'] as $sensor)
                                @if ($sensor['t'] == 'temperature')
                                    <li>
                                        {{ $sensor['n'] }}: {{ $sensor['last_val']['value_full'] }}
                                        <div class="progress">
                                            <div class="progress-bar
                                                @if ($sensor['last_val']['value_full'] >= 30) bg-danger
                                                @elseif ($sensor['last_val']['value_full'] >= 20) bg-warning
                                                @else bg-success @endif"
                                                role="progressbar" style="width: {{ $sensor['last_val']['value_full'] }}%"
                                                aria-valuenow="{{ $sensor['last_val']['value_full'] }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ $sensor['last_val']['value_full'] }} &deg;C
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach




    </div>
@endsection
@section('script')
    <script type="text/javascript">
        var user = {!! auth()->user()->toJson() !!};

        function getChartColorsArray(chartId) {
            if (document.getElementById(chartId) !== null) {
                var colors = document.getElementById(chartId).getAttribute("data-colors");

                if (colors) {
                    colors = JSON.parse(colors);
                    return colors.map(function(value) {
                        var newValue = value.replace(" ", "");

                        if (newValue.indexOf(",") === -1) {
                            var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                            if (color) return color;
                            else return newValue;
                        } else {
                            var val = value.split(",");

                            if (val.length == 2) {
                                var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                                rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                                return rgbaColor;
                            } else {
                                return newValue;
                            }
                        }
                    });
                } else {
                    console.warn('data-colors atributes not found on', chartId);
                }
            }
        }


        $(document).ready(function() {

            var daterange = $('#daterange').val();
            var from = daterange.split('to')[0];
            var to = daterange.split('to')[1];
            console.log(daterange);
            console.log(from);
            $.ajax({
                type: "POST",
                url: "/api/samples/types/report",
                data: JSON.stringify({
                    'from': from,
                    'to': to,
                    'user_id': to,
                }),
                dataType: 'json',
                contentType: "application/json; charset=utf-8",
            }).done(function(data, textStatus, xhr) {
                console.log(data);
                // notify(data.message);
                if (data.status) {
                    // sample temperature types
                    var chartDonutBasicColors = getChartColorsArray("sample-source");

                    if (chartDonutBasicColors) {
                        var options = {
                            series: data.data.values,
                            labels: data.data.labels,
                            chart: {
                                height: 333,
                                type: "donut"
                            },
                            legend: {
                                position: "bottom"
                            },
                            stroke: {
                                show: false
                            },
                            dataLabels: {
                                dropShadow: {
                                    enabled: false
                                }
                            },
                            colors: chartDonutBasicColors
                        };
                        var chart = new ApexCharts(document.querySelector("#sample-source"), options);
                        chart.render();
                    }
                } else {}
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseJSON.errorMessage);

            });




        });

        $('#daterange').change(function(value) {
            var daterange = $('#daterange').val();
            var from = daterange.split('to')[0];
            var to = daterange.split('to')[1];
            console.log(daterange);
            console.log(from);
        });
    </script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
