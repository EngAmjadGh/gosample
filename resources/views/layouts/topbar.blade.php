<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">




            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('assets/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('assets/images/logo-light.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>




                <div class="dropdown topbar-head-dropdown ms-1 header-item">

                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary"
                        id="page-header-action-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <h6 class="dropdown-header me-2">Actions</h6>
                            <i class="bx bx-plus fs-22"></i>
                        </div>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="page-header-action-dropdown">

                        <a class="dropdown-item" href="{{ route('admin.scheduled-tasks.quick') }}">
                            <i class="bx bx-plus-circle me-2"></i>Add Quick Schedule Task
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.tasks.create') }}">
                            <i class="bx bx-plus-circle me-2"></i>Add Task
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.tasks.index') }}">
                            <i class="bx bx-list-check me-2"></i>List Tasks (New Tasks)
                            @if ($newTasksCount > 0)
                                <span class="badge bg-danger">{{ $newTasksCount }}</span>
                            @endif
                        </a>

                        <a class="dropdown-item" href="{{ route('admin.swaprequests.index') }}">
                            <i class="bx bx-transfer me-2"></i>Swap Request
                            @if ($newSwapTasksCount > 0)
                                <span class="badge bg-danger">{{ $newSwapTasksCount }}</span>
                            @endif
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.swaprequests.create') }}">
                            <i class="bx bx-transfer me-2"></i>New Swap Request
                        </a>
                    </div>
                </div>


            </div>





            <div class="d-flex align-items-center">

                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..."
                                        aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i
                                            class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>




                <div class="ms-1 header-item d-none d-sm-flex">
                    <a href="{{ route('admin.samples.lost') }}"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class="ri-flask-line fs-22"></i>
                        <span
                            class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                            {{ count($lost_samples) }}</span>
                        </button>
                    </a>
                </div>


                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        <span
                            class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                            {{ $delayed_count }}<span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge badge-soft-light fs-13"> {{$delayed_count}} New</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                    id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab"
                                            role="tab" aria-selected="true">
                                            Lost Samples ({{ count($lost_samples) }})
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab"
                                            aria-selected="false">
                                            Tasks ({{ $delayed_count - count($lost_samples) }})
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                                            aria-selected="false">
                                            Alerts
                                        </a>
                                    </li> --}}
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    @foreach ($lost_samples as $sample)
                                        <div
                                            class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">

                                                <div class="flex-1">
                                                    <a href="{{ url('admin/samples/' . $sample->id) }}"
                                                        class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">The <b>Sample
                                                            </b>is lost!, please
                                                            check
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i>
                                                            {{ $sample->barcode_id }}</span>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- <div class="my-3 text-center">
                                        <a href="{{ url('admin/swaprequests') }}" class="btn btn-soft-success">
                                            <i class="ri-arrow-right-line align-middle"></i>View All Notifications</a>

                                        <!-- <button type="button" class="btn btn-soft-success waves-effect waves-light">View
                                            All Notifications <i class="ri-arrow-right-line align-middle"></i></button> -->
                                    </div> --}}
                                </div>

                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel"
                                aria-labelledby="messages-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">

                                    @foreach ($pickup_delayedTasks as $record)
                                        <div
                                            class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">

                                                <div class="flex-1">
                                                    <a href="{{ url('admin/tasks/' . $record->id) }}"
                                                        class="stretched-link">
                                                        <p class="mt-0 mb-2 lh-base">The <b>Task
                                                                {{ $record->id }}</b> is delayed!, please check
                                                        </p>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i>
                                                            {{ $record->created_at }}</span>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach


                                    @foreach ($drop_off_delayedTasks as $record)
                                        <div
                                            class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">

                                                <div class="flex-1">
                                                    <a href="{{ url('admin/tasks/' . $record->id) }}"
                                                        class="stretched-link">
                                                        <p class="mt-0 mb-2 lh-base">The <b>Task
                                                                {{ $record->id }}</b> is delayed!, please check
                                                        </p>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i>
                                                            {{ $record->created_at }}</span>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach


                                    @foreach ($delayed_tasks_in_freezer as $record)
                                        <div
                                            class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">

                                                <div class="flex-1">
                                                    <a href="{{ url('admin/tasks/' . $record->id) }}"
                                                        class="stretched-link">
                                                        <p class="mt-0 mb-2 lh-base">The <b>Task
                                                                {{ $record->id }}</b>is delayed!, please check
                                                        </p>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i>
                                                            {{ $record->created_at }}</span>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                    @foreach ($delayed_tasks_delivered as $record)
                                        <div
                                            class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">

                                                <div class="flex-1">
                                                    <a href="{{ url('admin/tasks/' . $record->id) }}"
                                                        class="stretched-link">
                                                        <p class="mt-0 mb-2 lh-base">The <b>Task
                                                                {{ $record->id }}</b> is delayed!, please check
                                                        </p>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i>
                                                            {{ $record->created_at }}</span>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach


                                    <div class="my-3 text-center">

                                        <a href="{{ url('admin/tasks') }}" class="btn btn-soft-success">
                                            <i class="ri-arrow-right-line align-middle"></i>View All Tasks</a>
                                        <!-- <button type="button" class="btn btn-soft-success waves-effect waves-light">View
                                            All Messages <i class="ri-arrow-right-line align-middle"></i></button> -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel"
                                aria-labelledby="alerts-tab">
                                <div class="w-25 w-sm-50 pt-3 mx-auto">
                                    <img src="{{ URL::asset('assets/images/svg/bell.svg') }}" class="img-fluid"
                                        alt="user-pic">
                                </div>
                                <div class="text-center pb-5 mt-2">
                                    <h6 class="fs-18 fw-semibold lh-base">Hey! You have no any notifications </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="@if (Auth::user()->avatar != '') {{ URL::asset('images/' . Auth::user()->avatar) }}@else{{ URL::asset('assets/images/users/avatar-1.jpg') }} @endif"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name }}</span>
                                <span
                                    class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{ Auth::user()->roles[0]->name }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome {{ Auth::user()->name }}!</h6>
                        <a class="dropdown-item" href="pages-profile"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Profile</span></a>

                        <a class="dropdown-item" href="pages-faqs"><i
                                class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Help</span></a>
                        <div class="dropdown-divider"></div>
                        <!-- <a class="dropdown-item" href="pages-profile"><i
                                class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Balance : <b>$5971.67</b></span></a> -->
                        <!-- <a class="dropdown-item" href="pages-profile-settings"><span
                                class="badge bg-soft-success text-success mt-1 float-end">New</span><i
                                class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Settings</span></a> -->
                        <!-- <a class="dropdown-item" href="auth-lockscreen-basic"><i
                                class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a> -->
                        <a class="dropdown-item " href="javascript:void();"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="bx bx-power-off font-size-16 align-middle me-1"></i> <span
                                key="t-logout">@lang('translation.logout')</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
