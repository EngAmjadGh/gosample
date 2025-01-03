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
            Delayed Tasks
        @endslot
        @slot('title')
            Delayed Tasks
        @endslot
    @endcomponent



    @if ($play_sound == 1)
        <audio autoplay loop="loop" controls="controls" style="display:none">
            <source src="emergency-alarm.mp3" type="audio/mp3">
            Your browser does not support the audio element.
        </audio>
    @endif


    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Hi, {{ Auth::user()->name }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening today.</p>
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
                                            @lang('translation.lost_samples')</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="">{{ count($lost_samples) }}</span>
                                        </h4>
                                        <a href="{{ url('admin/lost') }}" class="text-decoration-underline">
                                            @lang('translation.view') @lang('translation.lost_samples') </a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-danger rounded fs-3">
                                            <i class="bx bx-calendar-x text-danger"></i>
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
                                            @lang('translation.pickup_delayedTasks')</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="">{{ count($pickup_delayedTasks) }}</span>
                                        </h4>
                                        <a href="{{ url('admin/pickupdelayed') }}" class="text-decoration-underline">
                                            @lang('translation.view') @lang('translation.pickup_delayedTasks') </a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-danger rounded fs-3">
                                            <i class="bx bx-calendar-x text-danger"></i>
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
                                            @lang('translation.delayed_tasks_in_freezer')</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="">{{ count($delayed_tasks_delivered) }}</span>
                                        </h4>
                                        <a href="{{ url('admin/collectedDelayed') }}"
                                            class="text-decoration-underline">@lang('translation.list') @lang('translation.delayed_tasks_in_freezer')</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-danger rounded fs-3">
                                            <i class="bx bx-x-circle text-danger"></i>
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
                                            @lang('translation.delayed_tasks_delivered')</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="">{{ count($delayed_tasks_in_freezer) }}</span>
                                        </h4>
                                        <a href="{{ url('admin/outfreezerdelayed') }}"
                                            class="text-decoration-underline">@lang('translation.list') @lang('translation.delayed_tasks_delivered')</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-danger rounded fs-3">
                                            <i class="bx bx-angry text-danger"></i>
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
                                            @lang('translation.drop_off_delayedTasks')</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="">{{ count($drop_off_delayedTasks) }}</span></h4>
                                        <a href="{{ url('admin/dropdelayed') }}" class="text-decoration-underline">
                                            @lang('translation.view') @lang('translation.drop_off_delayedTasks') </a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-danger rounded fs-3">
                                            <i class="bx bx-x-circle text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->
            </div> <!-- end .h-100-->
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Lost Samples {{ count($lost_samples) }}</h4>

                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed">

                        @foreach ($lost_samples as $sample)
                            <li class="list-group-item ps-0">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-lg p-1 py-2 h-auto bg-light rounded-3">
                                            <div class="text-center">
                                                <h5 class="mb-0">{{ $sample->updated_at->format('d') }}</h5>
                                                <div class="text-muted">{{ $sample->updated_at->format('l') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">Update At:
                                            {{ $sample->updated_at->format('H:i:s') }} </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">Barcode:
                                            {{ $sample->barcode_id }}</a>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">BagCode:
                                        </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">
                                            {{ $sample->bag_code }}</a>
                                    </div>

                                    <div class="col">
                                        <h5 class="link-info mt-0 mb-1 fs-13">Task Id:
                                            <a href="admin/tasks/{{ $sample->task_id }}"
                                                class="text-reset fs-14 mb-0">{{ $sample->task_id }}</a>

                                        </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">Confirmed By:
                                            {{ $sample->confirmed_by }}</a>
                                    </div>

                                </div>
                                <!-- end row -->
                            </li><!-- end -->
                        @endforeach
                    </ul><!-- end -->

                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Pickup Delayed Tasks {{ count($pickup_delayedTasks) }}</h4>

                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed">

                        @foreach ($pickup_delayedTasks as $task)
                            <li class="list-group-item ps-0">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-lg p-1 py-2 h-auto bg-light rounded-3">
                                            <div class="text-center">
                                                <h5 class="mb-0">{{ $task->pickup_time->format('d') }}</h5>
                                                <div class="text-muted">{{ $task->pickup_time->format('l') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">Pickup Time:
                                            {{ $task->pickup_time->format('H:i:s') }} </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">{{ $task->to->name }}</a>
                                    </div>

                                    <div class="col">
                                        <h5 class="link-info mt-0 mb-1 fs-13">Task Id:
                                            <a href="admin/tasks/{{ $task->id }}"
                                                class="text-reset fs-14 mb-0">{{ $task->id }}</a>

                                        </h5>
                                        <a href="admin/drivers/{{ $task->driver->id }}"
                                            class="text-reset fs-14 mb-0">{{ $task->driver->name }}</a>
                                    </div>

                                </div>
                                <!-- end row -->
                            </li><!-- end -->
                        @endforeach
                    </ul><!-- end -->

                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
    </div>


    <div class="row">

        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1"> @lang('translation.drop_off_delayedTasks') {{ count($drop_off_delayedTasks) }}
                    </h4>

                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed">

                        @foreach ($drop_off_delayedTasks as $task)
                            <li class="list-group-item ps-0">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-l p-1 py-2 h-auto bg-light rounded-3">
                                            <div class="text-center">
                                                <h5 class="mb-0">{{ $task->dropoff_time->format('d') }}</h5>
                                                <div class="text-muted">{{ $task->dropoff_time->format('l') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">Drop off Time:
                                            {{ $task->dropoff_time->format('H:i:s') }} </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">{{ $task->to->name }}</a>
                                    </div>

                                    <div class="col">
                                        <h5 class="link-info mt-0 mb-1 fs-13">Task Id:
                                            <a href="admin/tasks/{{ $task->id }}"
                                                class="text-reset fs-14 mb-0">{{ $task->id }}</a>

                                        </h5>
                                        <a href="admin/drivers/{{ $task->driver->id }}"
                                            class="text-reset fs-14 mb-0">{{ $task->driver->name }}</a>
                                    </div>

                                </div>
                                <!-- end row -->
                            </li><!-- end -->
                        @endforeach
                    </ul><!-- end -->

                </div><!-- end card body -->
            </div><!-- end card -->
        </div>

        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Collected Delayed Tasks {{ count($delayed_tasks_in_freezer) }}
                    </h4>

                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed">

                        @foreach ($delayed_tasks_in_freezer as $task)
                            <li class="list-group-item ps-0">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-lg p-1 py-2 h-auto bg-light rounded-3">
                                            <div class="text-center">
                                                <h5 class="mb-0">{{ $task->collection_date->format('d') }}</h5>
                                                <div class="text-muted">{{ $task->collection_date->format('l') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">Collection Time:
                                            {{ $task->collection_date->format('H:i:s') }} </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">{{ $task->to->name }}</a>
                                    </div>

                                    <div class="col">
                                        <h5 class="link-info mt-0 mb-1 fs-13">Task Id:
                                            <a href="admin/tasks/{{ $task->id }}"
                                                class="text-reset fs-14 mb-0">{{ $task->id }}</a>

                                        </h5>
                                        <a href="admin/drivers/{{ $task->driver->id }}"
                                            class="text-reset fs-14 mb-0">{{ $task->driver->name }}</a>
                                    </div>

                                </div>
                                <!-- end row -->
                            </li><!-- end -->
                        @endforeach
                    </ul><!-- end -->

                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
    </div>


    <div class="row">

        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Closed Delayed Tasks {{ count($delayed_tasks_delivered) }}
                    </h4>

                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed">

                        @foreach ($delayed_tasks_delivered as $task)
                            <li class="list-group-item ps-0">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-l p-1 py-2 h-auto bg-light rounded-3">
                                            <div class="text-center">
                                                <h5 class="mb-0">{{ $task->freezer_out_date->format('d') }}</h5>
                                                <div class="text-muted">{{ $task->freezer_out_date->format('l') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">Freezer out Time:
                                            {{ $task->freezer_out_date->format('H:i:s') }} </h5>
                                        <a href="#" class="text-reset fs-14 mb-0">{{ $task->to->name }}</a>
                                    </div>

                                    <div class="col">
                                        <h5 class="link-info mt-0 mb-1 fs-13">Task Id:
                                            <a href="admin/tasks/{{ $task->id }}"
                                                class="text-reset fs-14 mb-0">{{ $task->id }}</a>

                                        </h5>
                                        <a href="admin/drivers/{{ $task->driver->id }}"
                                            class="text-reset fs-14 mb-0">{{ $task->driver->name }}</a>
                                    </div>

                                </div>
                                <!-- end row -->
                            </li><!-- end -->
                        @endforeach
                    </ul><!-- end -->

                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
    </div>
    <!-- end row-->
@endsection
@section('script')
    <script type="text/javascript"></script>
@endsection
