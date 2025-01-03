<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @can('dashboards')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-bar-chart-line"></i> <span>@lang('translation.dashboards')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarDashboards">
                            <ul class="nav nav-sm flex-column">
                                @can('dashboards')
                                    <li class="nav-item">
                                        <a href="{{ url('dashboard') }}" class="nav-link"><i class="ri-dashboard-2-line"></i>
                                            @lang('translation.analytics')</a>
                                    </li>
                                @endcan
                                @can('delayeddashboard')
                                    <li class="nav-item">
                                        <a href="{{ url('delayeddashboard') }}" class="nav-link"><i class="ri-time-line"></i>
                                            @lang('translation.delayeddashboard')</a>
                                    </li>
                                @endcan
                                @can('car-dashboard')
                                    <li class="nav-item">
                                        <a href="{{ url('car-dashboard') }}" class="nav-link"><i class="ri-car-line"></i>
                                            @lang('translation.car-dashboard')</a>
                                    </li>
                                @endcan
                                @can('map')
                                    <li class="nav-item">
                                        <a href="{{ url('map') }}" class="nav-link"><i class="ri-map-2-line"></i>
                                            @lang('translation.map')</a>
                                    </li>
                                @endcan
                                @can('tasks-dashboard')
                                    <li class="nav-item">
                                        <a href="{{ url('tasks-dashboard') }}" class="nav-link"><i class="ri-task-line"></i>
                                            @lang('translation.tasksdashboard')</a>
                                    </li>
                                @endcan
                                @can('daily-operation')
                                    <li class="nav-item">
                                        <a href="{{ url('daily-operation') }}" class="nav-link"><i class="ri-stack-line"></i>
                                            @lang('translation.daily-operation')</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('task_access')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTasks" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarTasks">
                            <i class="ri-dashboard-2-line"></i> <span>@lang('translation.tasks')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTasks">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('admin/tasks') }}" class="nav-link"><i class="ri-task-line"></i>
                                        @lang('translation.tasks')</a>
                                </li>
                            </ul>
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('admin/scheduled-tasks') }}" class="nav-link"><i
                                            class="ri-alarm-line"></i> @lang('cruds.scheduledTask.title')</a>
                                </li>
                            </ul>
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('admin/system-calendar') }}" class="nav-link"><i
                                            class="ri-calendar-2-line"></i> @lang('cruds.task.calendar')</a>
                                </li>
                            </ul>
                            @can('task_create')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/tasks/create') }}" class="nav-link"><i class="ri-add-line"></i>
                                            @lang('translation.tasks.create')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('task_missing')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/tasks/missing') }}" class="nav-link"><i
                                                class="ri-notification-2-line"></i> @lang('translation.task.missing')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('task_missing')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/lost') }}" class="nav-link"><i
                                                class="ri-notification-2-line"></i> @lang('translation.lost_samples')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('task_scan')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/tasks/scan') }}" class="nav-link"><i
                                                class="ri-barcode-box-line"></i> @lang('translation.task.scan')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('unused_tasks')
                                @if(!auth()->user()->client_id)
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('admin/tasks/unused') }}"  class="nav-link"><i
                                            class="ri-notification-2-line"></i> Unused Tasks</a>
                                </li>
                            </ul>
                                @endif
                            @endcan
                            @can('sample_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/samples') }}" class="nav-link"><i class="ri-flask-line"></i>
                                            @lang('translation.samples')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('shipment_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/shipments') }}" class="nav-link"><i
                                                class="ri-truck-line"></i> @lang('cruds.shipment.title')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('money_transfer_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/money-transfers') }}" class="nav-link"><i
                                                class="ri-exchange-dollar-line"></i> @lang('cruds.moneyTransfer.title')</a>
                                    </li>
                                </ul>
                            @endcan
                        </div>
                    </li>
                @endcan


                @can('driver_access')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarWorkers" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarWorkers">
                            <i class="ri-steering-line"></i> <span>@lang('translation.drivers')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarWorkers">
                            @can('driver_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/drivers') }}" class="nav-link"><i class="ri-user-2-line"></i>
                                            @lang('translation.drivers')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('car_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/cars') }}" class="nav-link"><i class="ri-car-line"></i>
                                            @lang('translation.cars')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('container_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/containers') }}" class="nav-link"><i
                                                class="ri-archive-line"></i> @lang('translation.containers')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('zone_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/zones') }}" class="nav-link"><i
                                                class="ri-map-pin-user-line"></i> @lang('translation.zones')</a>
                                    </li>
                                </ul>
                            @endcan
                            @can('attendance_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/attendances') }}" class="nav-link"><i
                                                class="ri-file-user-line"></i> @lang('translation.attendances')</a>
                                    </li>
                                </ul>
                            @endcan
                            {{-- @can('driver_schedule_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/driver-schedules') }}" class="nav-link"><i
                                                class="ri-calendar-line"></i> @lang('translation.driverSchedules')</a>
                                    </li>
                                </ul>
                            @endcan --}}
                            @can('swaprequest_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/swaprequests') }}" class="nav-link"><i
                                                class="ri-exchange-line"></i> @lang('translation.swaprequests')</a>
                                    </li>
                                </ul>
                            @endcan

                        </div>
                    </li>
                @endcan

                @can('client_access')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarClients" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarClients">
                            <i class="ri-user-location-line"></i> <span>@lang('translation.clients')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarClients">
                            @can('client_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/clients') }}" class="nav-link"><i class="ri-user-3-line"></i>
                                            @lang('translation.clients')</a>
                                    </li>

                                </ul>
                            @endcan

                            @can('location_access')
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('admin/locations') }}" class="nav-link"><i
                                                class="ri-map-pin-2-line"></i> @lang('translation.locations')</a>
                                    </li>

                                </ul>
                            @endcan


                        </div>
                    </li>
                @endcan
                @can('usersMenu')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarUsers" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarUsers">
                            <i class="ri-user-line"></i> <span>@lang('translation.users')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarUsers">
                            <ul class="nav nav-sm flex-column">
                                @can('user_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/users') }}" class="nav-link"><i class="ri-user-2-line"></i>
                                            @lang('translation.users')</a>
                                    </li>
                                @endcan
                                @can('role_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/roles') }}" class="nav-link"><i
                                                class="ri-shield-user-line"></i> @lang('translation.roles')</a>
                                    </li>
                                @endcan
                                @can('permission_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/permissions') }}" class="nav-link"><i
                                                class="ri-key-2-line"></i> @lang('translation.permissions')</a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan
                @can('settingsMenu')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarSettings">
                            <i class="ri-settings-line"></i> <span>@lang('translation.settings')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSettings">
                            <ul class="nav nav-sm flex-column">
                                @can('audit_log_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/audit-logs') }}" class="nav-link"><i
                                                class="ri-history-line"></i> @lang('translation.audit-logs')</a>
                                    </li>
                                @endcan
                                @can('term_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/terms') }}" class="nav-link"><i
                                                class="ri-file-paper-line"></i> @lang('translation.terms')</a>
                                    </li>
                                @endcan
                                @can('barcode_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/barcodes') }}" class="nav-link"><i
                                                class="ri-barcode-line"></i> @lang('translation.barcodes')</a>
                                    </li>
                                @endcan
                                @can('barcode_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/barcodes/generate') }}" class="nav-link"><i
                                                class="ri-barcode-box-line"></i> @lang('translation.generate.barcodes')</a>
                                    </li>
                                @endcan
                                @can('notification_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/notifications') }}" class="nav-link"><i
                                                class="ri-notification-2-line"></i> @lang('translation.notifications')</a>
                                    </li>
                                @endcan

                                @can('api_ayenati_access')
                                    <li class="nav-item">
                                        <a href="{{ url('admin/api-ayenatis') }}" class="nav-link"><i
                                                class="ri-notification-2-line"></i> @lang('cruds.apiAyenati.title')</a>
                                    </li>
                                @endcan


                            </ul>
                        </div>
                    </li>
                @endcan

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
