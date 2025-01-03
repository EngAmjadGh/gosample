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
        {{ trans('translation.scan') }} {{ trans('translation.sample.title_singular') }}
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-6">
                <div class="mb-3">
                    <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                    <select class="form-control select2" name="to_location" id="to_location">
                        @foreach ($to_locations as $id => $entry)
                            <option value="{{ $id }}" {{ old('to_location') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('to_location'))
                        <div class="invalid-feedback">
                            {{ $errors->first('to_location') }}
                        </div>
                    @endif
                </div>
            </div>
            <!--end col-->

            <div class="col-6">
                <div class="mb-3">
                    <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                    <select class="form-control select2" name="driver_id" id="driver_id">
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
            <!--end col-->

            <div class="col-6">
                <div class="mb-3">
                    <label for="scan_way">Scan way</label>
                    <select name="scan_way" id="scan_way" class="form-control" onchange="scanWayChange()">
                        <option value="reader">Reader</option>
                        <option value="manual">Manual</option>
                        <option value="camera">Camera</option>
                    </select>
                </div>
            </div>
            <!--end col-->


            <div class="col-lg-12">
                <div class="text-start">
                    <button class="btn btn-info" id="create-batch">
                        {{ trans('translation.getSamples') }}
                    </button>
                </div>
            </div>

            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('translation.list') }} {{ trans('translation.sample.title') }}
    </div>
    <div class="card-body">
        <p id="batch-created"></p>
        @can('confirm_all')
            <div class="row justify-content-end">
                <div class="col-auto">
                    <button id="confirm-all-btn" class="btn btn-danger" onclick="confirmAll()">Confirm All</button>
                </div>
            </div>
            </br>
        @endcan


        <div class="row">
            <div class="col-4" style="text-align: center">
                <div class="row" style="height:100%">

                    <div class="col" style="background-color: #162427;overflow-y: scroll">
                        <div class="row">
                            <div class="col" style="padding-top:5px;">
                                <h4 style="color:white !important;">Batch Samples ( <span id="batch-count">0</span> )
                                </h4>
                                <div id="extracted-codes">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-8">
                <div id="scandit-barcode-picker"></div>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
.my-btn-class {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin-bottom: 10px;
}
</style>

@section('script')
<!--jquery cdn-->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> --}}
<script src="{{ asset('assets/js/onscan.min.js') }}"></script>

<!--select2 cdn-->
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

{{-- <script src="{{ URL::asset('assets/js/pages/select2.init.js') }}"></script> --}}

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/scandit-sdk@5.x"></script>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}

<script>
    function confirmAll() {
        if (confirm("Are you sure you want to confirm all samples in the batch?")) {
            $.ajax({
                type: "POST",
                url: "/api/task/sample/confirmall",
                data: JSON.stringify({
                    'driver_id': $("#driver_id option:selected").val(),
                    'to_location': $("#to_location option:selected").val(),
                    // 'to_location': 123444,
                    // 'driver_id': code
                }),
                dataType: 'json',
                contentType: "application/json; charset=utf-8",
            }).done(function(data, textStatus, xhr) {
                console.log(data);
                if (data.status) {
                    console.log('succss');
                    location.reload(); // Refresh the page
                } else {
                    console.log('failed');
                    // location.reload(); // Refresh the page

                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                // $(".preloader").fadeOut( "fast", function() {});
                $("#batch-created").prepend("<p style='color:palevioletred'>" + jqXHR.responseJSON.message +
                    "</p>");
                //$(".alert-danger").slideDown( "slow", function() {});
            });
        }
    }
    var user = {!! auth()->user()->toJson() !!};

    var username = user.username;
    var BatchSamples = [];
    // var username = ;
    var BatchTotal = 0;
    var scanditToken =
        "AcvB7K6BOkafNr8EEhzGnk0BjSadL6zs3WzHFGUMOnCnRfH4y3u5G21mmZ14cIxKOXY/znlQ+ABgeCJMm2ApWqtEX/A0a1O4jCzdQLdrzBgqYZn5cVXKSVhIbOsebE+lswu8ragddRSGNRaYngV+kMYpJcWvJ7bmd+1YofZZhXeehiabYMwbAG5pCAyQjKAMZw/wCBZ3D6C5MLfRPT5yFbYyRCjwFTCjlEWkcUFZkueSoywVBJM/M/HSUdCSmCgBlNnM9yVlB0wM+IsYiTDLGLAl5kdLc+d5wHVfQLKNz6Dvkew55fZNy/MtrHq087CIcEny/HOt0FKcNVMkJgkb7Sg476PlBWyxvMpQZvxlz4lDXawtHoB6D0PevIVw3hV0tSwIinnqP9lVW6vqyp2EE4dk2v5vZBy9L85kng6PZdnktiYPp2yuR4B9tKrtSoY+DXDRp05HyTYHNQaXONfE2fa3KQ7IxlAJjCWb6qkK+M+nUS4XiloPAOMvXWc7p49umgHkqNhHuVHZ8qZar5D/mr4BNGK/T+cTGrM96OTegYTRxF2q/ibRrd4XZtyCWCvQ+/I2f2qL0N1C3NUcq2fuhPLHqGPgKkrLeHy4BLLR+qx+qRo0xl/yU0oHrSCFlYdJ8jog8mwY9yDY6Q5TRSPwLJ0OInwal9xb+axEc5Ziw2KGTA6wC5CBFo/alYle8PcIUQzvNs2tudFAOhZNSA5ipQlahAWH7cqfmcGaZALUUoQeuiAMekb3SDG0H7altefmhxNma5wBX7zJ2wrHxVAT4h+knTA64iwktiiVufh+lat8HJlknPQooSMQsSuipdGSmAENdGwX"

    function configure() {

        ScanditSDK.configure(scanditToken, {
            engineLocation: "https://cdn.jsdelivr.net/npm/scandit-sdk@5.x/build/",
        }).then(() => {
            return ScanditSDK.BarcodePicker.create(document.getElementById("scandit-barcode-picker"), {
                playSoundOnScan: true,
                scanSettings: new ScanditSDK.ScanSettings({
                    enabledSymbologies: ["ean13"],
                    codeDuplicateFilter: 2000
                }),
            });
        }).then((barcodePicker) => {
            // barcodePicker is ready here, show a message every time a barcode is scanned
            barcodePicker.on("scan", (scanResult) => {
                // console.log(scanResult);
                var task = location.href.split('/')[6];
                code = scanResult.barcodes[0].data;
                code = code.replace(/^0+/, '');
                code = code.substring(0, code.length);
                // console.log(code);
                if (BatchSamples.includes(code)) {
                    $.ajax({
                        type: "POST",
                        url: "/api/task/sample/check",
                        data: JSON.stringify({
                            'task_id': task,
                            'sample_id': code,
                            'confirmed_by': user.username
                        }),
                        dataType: 'json',
                        contentType: "application/json; charset=utf-8",
                    }).done(function(data, textStatus, xhr) {
                        if (data.status) {
                            if (BatchSamples.includes(code)) {
                                // BatchSamples.push(code);
                                // BatchSamples = [];
                                BatchSamples.splice(BatchSamples.indexOf(code), 1);

                                $("#extracted-codes").empty();
                                $.each(BatchSamples, function(i) {
                                    $("#extracted-codes").prepend(
                                        "<p style='color:palevioletred'>" +
                                        BatchSamples[i] + "</p>");
                                });
                                $("#batch-created").html("Samples is removed successfully");

                                // $("#extracted-codes").prepend("<p style='color:#4cffb5'>The Sample ID : " + code + " is added to the batch</p>");
                                $("#batch-count").html(BatchSamples.length);

                            } else {
                                $("#batch-created").empty();
                                $("#extracted-codes").prepend(
                                    "<p style='color:palevioletred'>Sample is not in the batch</p>"
                                );
                            }
                        } else {

                            $("#batch-created").prepend("<p style='color:palevioletred'>" + data
                                .message + "</p>");

                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        $("#batch-created").prepend("<p style='color:palevioletred'>" + jqXHR
                            .responseJSON.message + "</p>");
                    });
                } else {
                    $("#batch-created").empty();
                    $("#batch-created").prepend(
                        "<p style='color:palevioletred'>Sample is not in the batch</p>");
                }
            });
        });
    }


    $("#confirm-batch").click(function() {
        alert("Handler for .click() called.");
    });

    $("#confirm-batch").on("click", function() {

        // confirm all samples at one time
        //client/samples/confirm
        $.ajax({
            type: "POST",
            url: "/api/client/samples/confirm",
            data: JSON.stringify({
                'samples': BatchSamples,
                'confirmed_by': username
            }),
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
        }).done(function(data, textStatus, xhr) {
            if (data.status) {
                $("#batch-created").html("Samples are confirmed successfully");
                BatchSamples = [];
                $("#batch-count").html(BatchSamples.length);
                $("#extracted-codes").empty();
            } else {
                $("#batch-created").html(data.message);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $("#batch-created").html(jqXHR.responseJSON.errorMessage);
        });
    });

    $("#create-batch").on("click", function() {
        console.log('create-batch');
        var task = 57165;
        BatchSamples = [];
        $("#extracted-codes").empty();
        $.ajax({
            type: "POST",
            url: "/api/driver/samples/valid/check",
            data: JSON.stringify({
                'task_id': task,
                'driver_id': $("#driver_id option:selected").val(),
                'location_id': $("#to_location option:selected").val(),
            }),
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
        }).done(function(data, textStatus, xhr) {
            // console.log(data);
            if (data.status) {
                if (data.data.length > 0) {
                    // configure();
                    var scanWay = document.getElementById("scan_way").value;
                    if (scanWay == 'manual') {
                        // destroyOnScan();
                    }


                    if (scanWay == 'reader') {
                        initOnScan();
                    }
                    if (scanWay == 'camera') {
                        // initOnScan();
                        // destroyOnScan();
                        configure(); // init camera
                    }
                    $("#batch-created").html("Samples are retrieved successfully");
                    $.each(data.data, function(i) {
                        // $("#extracted-codes").empty();
                        $("#extracted-codes").prepend("<p style='color:palevioletred'>" + data
                            .data[i].barcode_id + "</p>");
                        BatchSamples.push(data.data[i].barcode_id);
                    });
                    if (BatchSamples.length > 0) {
                        $("#confirm-batch").show();
                    }
                } else {
                    $("#batch-created").html("No Samples with selected driver");

                }


                // BatchSamples= data.data;
                $("#batch-count").html(BatchSamples.length);
            } else {
                $("#batch-created").html(data.message);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $("#batch-created").html(jqXHR.responseJSON.errorMessage);
        });
    });

    $(document).ready(function() {
        $("#confirm-batch").hide();

    });
</script>


<script type="text/javascript">
    function initOnScan() {

        var options = {
            // increase time from 100 to 200 to make honeywell barcode working well
            // delta reader value was 100
            timeBeforeScanTest: 200,
            // increase time from 30 to 60 to make honeywell barcode working well
            // delta reader value was 30
            avgTimeByChar: 60,
            minLength: 6,
            // suffixKeyCodes: suffixKeyCodes,
            // prefixKeyCodes: prefixKeyCodes,
            scanButtonLongPressTime: 500,
            // stopPropagation: document.getElementById("iStopPropagation").checked,
            // preventDefault: document.getElementById("iPreventDefault").checked,
            // reactToPaste: document.getElementById("iAcceptPasteInput").checked,
            // reactToKeyDown: document.getElementById("iAcceptKeyInput").checked,
            singleScanQty: 1,

        }

        try {
            onScan.attachTo(document, options);
            console.log("onScan Started!");
            document.addEventListener('scan', scanHandler);
            document.addEventListener('scanError', scanErrorHandler);

        } catch (e) {
            console.log(e)
            // onScan.setOptions(document, options);
            console.log("onScansettings changed!");
        }


    }

    function destroyOnScan() {
        console.log("onScan destroyed!");
        onScan.detachFrom(document);
    }

    function scanWayChange() {
        var scanWay = document.getElementById("scan_way").value;
        console.log(scanWay);
        if (scanWay == 'manual') {
            // destroyOnScan();
        }

        if (scanWay == 'reader') {
            initOnScan(); // init barcode reader
        }
        if (scanWay == 'camera') {
            // initOnScan();
            // destroyOnScan();
            configure(); // init camera
        }

    }

    function scanHandler(e) {

        console.log('scanHandler')
        console.log(e.detail)
        if (e.detail.scanCode == undefined) return;

        var task = location.href.split('/')[6];
        // code = scanResult.barcodes[0].data;
        // code = code.replace(/^0+/, '');
        // code = code.substring(0, code.length - 1);
        code = e.detail.scanCode;
        console.log('code:' + code);


        // console.log(BatchSamples);
        if (BatchSamples.includes(code)) {
            $.ajax({
                type: "POST",
                url: "/api/task/sample/check",
                data: JSON.stringify({
                    'task_id': task,
                    'sample_id': code
                }),
                dataType: 'json',
                contentType: "application/json; charset=utf-8",
            }).done(function(data, textStatus, xhr) {
                if (data.status) {
                    if (BatchSamples.includes(code)) {
                        // BatchSamples.push(code);
                        // BatchSamples = [];
                        BatchSamples.splice(BatchSamples.indexOf(code), 1);

                        $("#extracted-codes").empty();
                        $.each(BatchSamples, function(i) {
                            $("#extracted-codes").prepend("<p style='color:palevioletred'>" +
                                BatchSamples[i] + "</p>");
                        });
                        $("#batch-created").html("Samples is removed successfully");

                        // $("#extracted-codes").prepend("<p style='color:#4cffb5'>The Sample ID : " + code + " is added to the batch</p>");
                        $("#batch-count").html(BatchSamples.length);

                    } else {
                        $("#batch-created").empty();
                        $("#extracted-codes").prepend(
                            "<p style='color:palevioletred'>Sample is not in the batch</p>");
                    }
                } else {

                    $("#batch-created").prepend("<p style='color:palevioletred'>" + data.message + "</p>");

                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                // $(".preloader").fadeOut( "fast", function() {});
                $("#batch-created").prepend("<p style='color:palevioletred'>" + jqXHR.responseJSON.message +
                    "</p>");
                //$(".alert-danger").slideDown( "slow", function() {});
            });
        } else {
            console.log('barcode is not reading well..')
            $("#batch-created").empty();
            $("#batch-created").prepend("<p style='color:palevioletred'>Sample is not in the batch</p>");
        }
    }

    function scanErrorHandler(e) {
        var sFormatedErrorString = "Error Details: {\n";
        for (var i in e.detail) {
            sFormatedErrorString += '    ' + i + ': ' + e.detail[i] + ",\n";
        }
        sFormatedErrorString = sFormatedErrorString.trim().replace(/,$/, '') + "\n}";
        console.log("[scanErrorHandler]: " + sFormatedErrorString);
    }

    function getonScanSettings() {
        var sFormatedErrorString = "Scanner Settings: \n";
        var aJSONArray = JSON.stringify(onScan.getOptions(document)).split(",");
        for (prop = 0; prop < aJSONArray.length - 1; prop++) {
            if (aJSONArray[prop + 1][0] == '\"') {
                sFormatedErrorString += aJSONArray[prop] + "," + "\n";
            } else {
                sFormatedErrorString += aJSONArray[prop] + ",";
            }
        }
        sFormatedErrorString += aJSONArray[aJSONArray.length - 1];

        console.log(sFormatedErrorString);


    }
</script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
