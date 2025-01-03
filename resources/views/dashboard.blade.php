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
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Good Morning, {{ Auth::user()->name }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening today.</p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text" id="daterange"
                                                    class="form-control border-0 dash-filter-picker shadow"
                                                    data-provider="flatpickr" data-range-date="true"
                                                    data-date-format="d-m-Y"
                                                    data-deafult-date="01 Jan 2021 to 29 June 2021">
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">

                                            <a href="{{ url('admin/tasks/create') }}" class="btn btn-soft-success">
                                                <i class="ri-add-circle-line align-middle me-1"></i>@lang('translation.tasks.create')</a>
                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">
                                            <button type="button"
                                                class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn"><i
                                                    class="ri-pulse-line"></i></button>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            @lang('translation.tasks')</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                            +16.24 %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $tasks }}">0</span>
                                        </h4>
                                        <a href="{{ url('admin/tasks') }}" class="text-decoration-underline">
                                            @lang('translation.list') @lang('translation.tasks') </a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-success rounded fs-3">
                                            <i class="bx bx-task text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            @lang('translation.samples')</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-danger fs-14 mb-0">
                                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                                            -3.57 %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $samples }}">0</span></h4>
                                        <a href="{{ url('admin/samples') }}" class="text-decoration-underline">View all
                                            samples</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-info rounded fs-3">
                                            <i class="bx bx-shopping-bag text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            @lang('translation.clients')</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                            +29.08 %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $clients }}">0</span>
                                        </h4>
                                        <a href="{{ url('admin/clients') }}"
                                            class="text-decoration-underline">@lang('translation.list') @lang('translation.clients')</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-warning rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            @lang('translation.cars')</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-muted fs-14 mb-0">
                                            +0.00 %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $cars }}">0</span>
                                        </h4>
                                        <a href="{{ url('admin/cars') }}"
                                            class="text-decoration-underline">@lang('translation.list') @lang('translation.cars')</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-primary rounded fs-3">
                                            <i class="bx bx-wallet text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->

                <div class="row">
                    <div class="col-xl-16">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Task Chart</h4>
                                <div>
                                    <button type="button" class="btn btn-soft-secondary btn-sm">
                                        ALL
                                    </button>
                                    <button type="button" class="btn btn-soft-secondary btn-sm">
                                        1M
                                    </button>
                                    <button type="button" class="btn btn-soft-secondary btn-sm">
                                        6M
                                    </button>
                                    <button type="button" class="btn btn-soft-primary btn-sm">
                                        1Y
                                    </button>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-header p-0 border-0 bg-soft-light">
                                <div class="row g-0 text-center">
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value" data-target="7585">0</span>
                                            </h5>
                                            <p class="text-muted mb-0">Tasks</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value" data-target="2289">0</span>
                                            </h5>
                                            <p class="text-muted mb-0">Samples</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value" data-target="25">0</span></h5>
                                            <p class="text-muted mb-0">Cars</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <!-- <div class="col-6 col-sm-3">
                                        <div
                                            class="p-3 border border-dashed border-start-0 border-end-0">
                                            <h5 class="mb-1 text-success"><span class="counter-value"
                                                    data-target="18.92">0</span>%</h5>
                                            <p class="text-muted mb-0">Conversation Ratio</p>
                                        </div>
                                    </div> -->
                                    <!--end col-->
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="customer_impression_charts"
                                        data-colors='["--vz-primary", "--vz-success", "--vz-danger"]' class="apex-charts"
                                        dir="ltr"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                </div>

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Samples Temperature</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i
                                                    class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Download Report</a>
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div id="sample-source"
                                    data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div>
                        </div> <!-- .card-->
                    </div> <!-- .col-->

                    <div class="col-xl-8">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Top Drivers</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i
                                                    class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Download Report</a>
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <tbody>

                                            @foreach ($top_drivers as $record)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <!-- <div class="flex-shrink-0 me-2">
                                                            <img src="{{ URL::asset('assets/images/companies/img-1.png') }}"
                                                                alt="" class="avatar-sm p-2" />
                                                        </div> -->
                                                            <div>
                                                                <h5 class="fs-14 my-1 fw-medium"><a
                                                                        href="apps-ecommerce-seller-details"
                                                                        class="text-reset">{{ $record->name }}</a>
                                                                </h5>
                                                                <span class="text-muted">{{ $record->name }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ $record->name }}</span>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">{{ $record->total }}</p>
                                                        <span class="text-muted">Task</span>
                                                    </td>
                                                </tr><!-- end -->
                                            @endforeach


                                        </tbody>


                                    </table><!-- end table -->
                                </div>

                                <!-- <div
                                    class="align-items-center mt-4 pt-2 justify-content-between d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="text-muted">Showing <span
                                                class="fw-semibold">5</span> of <span
                                                class="fw-semibold">{{ $top_drivers->total() }}</span> Results
                                        </div>
                                    </div>
                                    <ul class="pagination pagination-separated pagination-sm mb-0">
                                        <li class="page-item disabled">
                                            <a href="#" class="page-link">←</a>
                                        </li>
                                        <li class="page-item">
                                            <a href="#" class="page-link">1</a>
                                        </li>
                                        <li class="page-item active">
                                            <a href="#" class="page-link">2</a>
                                        </li>
                                        <li class="page-item">
                                            <a href="#" class="page-link">3</a>
                                        </li>
                                        <li class="page-item">
                                            <a href="#" class="page-link">→</a>
                                        </li>
                                    </ul>
                                </div> -->

                            </div> <!-- .card-body-->
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div> <!-- end row-->
            </div> <!-- end .h-100-->

        </div> <!-- end col -->

        <div class="col-auto layout-rightside-col">
            <div class="overlay"></div>
            <div class="layout-rightside">
                <div class="card h-100 rounded-0">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <h6 class="text-muted mb-0 text-uppercase fw-semibold">Recent Activity</h6>
                        </div>
                        <div data-simplebar style="height:100%;" class="p-3 pt-0">
                            <div class="acitivity-timeline acitivity-main">
                                @foreach ($notifications as $record)
                                    <div class="acitivity-item d-flex">
                                        <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                <i class="bx bx-bell"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 lh-base">{{ $record->title }}</h6>
                                            <a href="{{ url('admin/tasks') }}"
                                                class="link-warning text-decoration-underline">Task:{{ $record->task_id }}</a>
                                            <p class="text-muted mb-1" style="word-break: break-all">
                                                {{ $record->fromLocation->name }} </p>
                                            <p class="text-muted mb-1" style="word-break: break-all">
                                                {{ $record->toLocation->name }} </p>
                                            <p class="text-muted mb-1" style="word-break: break-all">
                                                <span class="text-danger">Driver:
                                                    {{ $record->driver ? $record->driver->name : '' }}</span>
                                            </p>
                                            <small class="mb-0 text-muted">{{ $record->created_at }}</small>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div> <!-- end card-->
            </div> <!-- end .rightbar-->

        </div> <!-- end col -->
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
