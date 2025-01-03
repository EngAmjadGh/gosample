<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Tasks Report </title>
    {{-- <link rel="stylesheet" type="text/css" href="https://gosample.com/assets/css/export.css"> --}}

    <style>
        .container {
            /* text-align: center;
                max-width: 799px;
                margin: 0 auto;
                padding-top: 50px; */
        }

        .table-container {
            /* margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 0px solid #ddd; */
        }

        /* table {
                margin: 0 auto;
                width: 100%;
                border-color: #000;
            } */

        td,
        th {
            text-align: center;
            border: 1px solid #ddd;
            margin: 0;
            padding: 0;
        }

        img {
            max-width: 400px;
        }

        .bg_green {
            background-color: #005A60;
            color: #fff;
        }

        .bg_red {
            background-color: #8D363B;
            color: #fff;
        }

        .bg_red th {
            font-size: 15px;
        }

        .small_table td {
            font-size: 15px;
        }

        .main_table td {
            font-size: 15px;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="wrapper">
            <div class="container">
                <div id='footer_logo' style='position: absolute;top: 0; left: 0;'>
                    <p>
                        <img src="https://gosample.com/assets/img/mtc_logo.jpg" style="height: 113px;width:200px">
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="wrapper">
            <div class="container">
                <div id='footer_logo' style='position: absolute;top: 0; right: 0;'>
                    <p>
                        @if ($client_logo)
                            <img src="https://gosample.com/{{ $client_logo }}" style="height: 113px;width:200px">
                            {{-- <img src="https://gosample.com/assets/img/mtc_logo.jpg" style="height: 113px;width:200px"> --}}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>


    <div style="text-align:center">
        <h4>
            LAB SAMPLES TRANSPORTATION
        </h4>
        <h6>
            DAILY REPORT | {{ $reportDate }}
        </h6>
    </div>
    </br>


    <div style="text-align:center">

        @if ($billing_client == 26)
            <div style="display: inline-block; width: 30%; margin: 0 auto;">
                <table style="width:100%">
                    <thead>
                        <tr class="bg_green">
                            <th>Count </th>
                            <th>Pickup From</th>
                            <th>Time (minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (json_decode($summaryReport, true) as $key => $value)
                            <tr>
                                <td>{{ $value['count'] }}</td>
                                <td> {{ $key }}</td>
                                <td>{{ floor($value['trip_duration'] / 60) . 'H:' . ($value['trip_duration'] - floor($value['trip_duration'] / 60) * 60) . 'M' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </br>
            </br>
            </br>
        @endif
    </div>



    <div style="text-align:center">


        <div style="display: inline-block; width: 30%;">
            <table style="width:100%">
                <tr class="bg_green">
                    <td colspan="2">Pickup Summary</td>
                </tr>
                <tr class="bg_green">
                    <td>Type </td>
                    <td>Qty.</td>
                </tr>
                <tr>
                    <td>Served Organization</td>
                    <td>{{ $served_orginization }}</td>
                </tr>
                <tr>
                    <td> Bag Collection</td>
                    <td>{{ $pick_sum_data[0] }}</td>
                </tr>
                <tr>

                    <td>Samples Collection</td>
                    <td>{{ $pick_sum_data[1] }}</td>
                </tr>
                <tr>
                    <td>No Samples Shipments</td>
                    <td>{{ $visited_orginization }}</td>
                </tr>
            </table>

        </div>

        <div style="display: inline-block; width: 30%;">
            <table style="width:100%">
                <tr>
                    <td rowspan="6" class="bg_green">
                        <img src="https://gosample.com/assets/icons/temp_blue_bg-03.jpg">
                    </td>
                    <td colspan="6" class="bg_green">COLLECTION INFORMATION SUMMARY</td>
                </tr>

                <tr>
                    <td colspan='2' class="bg_green" style='width:700px'>TEMPREATURE</td>
                    <td colspan='2' class="bg_green">BOX</td>
                    <td colspan='2' class="bg_green">SAMPLES</td>
                </tr>
                <tr>
                    <td><img src="https://gosample.com/assets/icons/light.jpg"></td>
                    {{-- <td><img src="/assets/icons/light.jpg"></td> --}}
                    <td>
                        Room
                    </td>
                    <td>
                        {{ $roomBags }}
                    </td>
                    <td rowspan="4">
                        {{--                    @if (count($temperatures) > 0 && isset($temperatures[0]) && isset($temperatures[1]) && isset($temperatures[2])) --}}
                        {{--                        {{$temperatures[0]->bcode + $temperatures[1]->bcode + $temperatures[2]->bcode}} --}}
                        {{--                    @endif --}}
                        {{ $roomBags + $refBags + $frozenBags }}
                    </td>
                    <td>
                        {{ $roomSamples }}
                    </td>
                    <td rowspan="4">
                        {{--                    @if (count($temperatures) > 0 && isset($temperatures[0]) && isset($temperatures[1]) && isset($temperatures[2])) --}}
                        {{ $roomSamples + $refSamples + $frozenSamples }}

                        {{--                    @endif --}}
                    </td>
                </tr>
                <tr>
                    <td><img src="https://gosample.com/assets/icons/temp-03.jpg"></td>
                    <td>
                        REFRIGERATE
                    </td>
                    <td>
                        {{--                    @if (count($temperatures) > 0 && isset($temperatures[1])) --}}
                        {{ $refBags }}
                        {{--                    @endif --}}
                    </td>
                    {{-- <td>empty data 3</td> --}}
                    <td>
                        {{--                    @if (count($temperatures) > 0 && isset($temperatures[1])) --}}
                        {{--                        {{$temperatures[1]->total}} --}}
                        {{--                    @endif --}}
                        {{ $refSamples }}
                    </td>
                    {{-- <td>empty data 5</td> --}}
                </tr>
                <tr>
                    <td><img src="https://gosample.com/assets/icons/snow-03.jpg"></td>
                    <td>
                        FROZEN
                    </td>
                    <td>
                        {{ $frozenBags }}
                    </td>
                    {{-- <td>empty data 3</td> --}}
                    <td>
                        {{--                    @if (count($temperatures) > 0 && isset($temperatures[0])) --}}
                        {{--                        {{$temperatures[0]->total}} --}}
                        {{--                    @endif --}}
                        {{ $frozenSamples }}
                    </td>
                    {{-- <td>empty data 5</td> --}}
                </tr>
                <tr>
                    <td><img src="https://gosample.com/assets/icons/circle-03.jpg"></td>
                    <td> OTHER </td>
                    <td>0</td>
                    {{-- <td>empty data 3</td> --}}
                    <td>0</td>
                    {{-- <td>empty data 5</td> --}}
                </tr>
            </table>
        </div>

    </div>


    <div class="table-container">
        <div class="col-lg-12">
            {{-- <div class="table-responsive table-card"> --}}
            <!-- Bordered Tables -->
            <div class="row small_table" style="width:100%;;display:inline-block">

                {{-- <table> --}}
                <table cellspacing="0" cellpadding="0" style=" margin: 0 auto; width: 100%;">
                    <thead class='bg_red'>
                        <tr class='bg_red'>
                            <th rowspan="2" class='bg_red'>#</th>
                            <th rowspan="2"class='bg_red'>Task Id</th>
                            <th colspan="3" class='bg_red'>FROM</th>

                            {{-- head for collection time --}}
                            <th colspan="3" class='bg_red'> To </th>

                            <th rowspan="2" class='bg_red'> Trip Dur.</th>
                            @if ($billing_client == 25)
                                <th rowspan="2" class='bg_red'> Receiving Time.</th>
                            @endif
                            <th rowspan="2" class='bg_red'> CONTAINER BARCODE</th>

                        </tr>
                        <tr class='bg_red'>
                            <th class='bg_red'>
                                ORGANIZATION
                            </th>
                            <th class='bg_red'>Arrived Time</th>
                            <th class='bg_red'>Stay Time</th>

                            <th class='bg_red'> Organizations </th>

                            <th class='bg_red'> Arrived <br> Time </th>

                            <th class='bg_red'> Stay Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr style='text-align:center !important;'>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $task->id }}</td>
                                <td>
                                    {{ $task->from_organization_name }}
                                </td>
                                <td>
                                    {{ $task->from_location_arrival_time }}
                                </td>
                                <td>
                                    {{ $task->from_stay_time }}
                                </td>
                                <td>
                                    {{ $task->to_organization_name }}
                                </td>
                                {{-- delivery time --}}
                                <td>
                                    {{ $task->to_location_arrival_time }}
                                </td>
                                <td>
                                    {{ $task->to_stay_time }}
                                </td>
                                {{-- trip dur. --}}
                                <td>
                                    {{ $task->trip_duration }}
                                </td>
                                @if ($billing_client == 25)
                                    <td>
                                        {{ $task->confirmation_time }}
                                    </td>
                                @endif
                                {{-- CONTAINER BARCODE --}}
                                <td>

                                    <table class="GeneratedTable">
                                        @if (property_exists($task, 'temperature_types2') && $task->temperature_types2 != null)
                                            <tr>
                                                <th style="font-style: normal">Bag Barcode</th>
                                                <th>Temperatures</th>
                                                <th>Samples Count</th>
                                            </tr>
                                        @endif

                                        @if (property_exists($task, 'data') && $task->data != null)
                                            @foreach ($task->data as $item)
                                                <tr>
                                                    <th> {{ $item->bag }}</th>
                                                    <th>
                                                        {{--                                    {{$item->temperature}} -  --}}
                                                        {{ $item->temperature_label }}

                                                    </th>
                                                    <th> {{ $item->count }}</th>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>


                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>




</body>

</html>


<style>
    table.GeneratedTable {
        width: 100%;
        background-color: #e9e9e9;
        border-collapse: collapse;
        border-width: 1px;
        font-weight: normal;

    }

    table.GeneratedTable td,
    table.GeneratedTable th {
        border-width: 1px;
        font-weight: normal;
        padding: 3px;
    }

    table.GeneratedTable thead {
        font-weight: normal;
        background-color: #ff0000;
    }
</style>
