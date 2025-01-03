<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/permissions*") ? "c-show" : "" }} {{ request()->is("admin/roles*") ? "c-show" : "" }} {{ request()->is("admin/users*") ? "c-show" : "" }} {{ request()->is("admin/audit-logs*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('audit_log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.audit-logs.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.auditLog.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('driver_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.drivers.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/drivers") || request()->is("admin/drivers/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-car c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.driver.title') }}
                </a>
            </li>
        @endcan
        @can('car_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.cars.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/cars") || request()->is("admin/cars/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.car.title') }}
                </a>
            </li>
        @endcan
        @can('attendance_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.attendances.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/attendances") || request()->is("admin/attendances/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.attendance.title') }}
                </a>
            </li>
        @endcan
        @can('barcode_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.barcodes.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/barcodes") || request()->is("admin/barcodes/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.barcode.title') }}
                </a>
            </li>
        @endcan
        @can('car_driver_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.car-drivers.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/car-drivers") || request()->is("admin/car-drivers/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.carDriver.title') }}
                </a>
            </li>
        @endcan
        @can('car_link_history_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.car-link-histories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/car-link-histories") || request()->is("admin/car-link-histories/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.carLinkHistory.title') }}
                </a>
            </li>
        @endcan
        @can('client_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.clients.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/clients") || request()->is("admin/clients/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-user-tie c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.client.title') }}
                </a>
            </li>
        @endcan
        @can('location_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.locations.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/locations") || request()->is("admin/locations/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.location.title') }}
                </a>
            </li>
        @endcan
        @can('container_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.containers.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/containers") || request()->is("admin/containers/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.container.title') }}
                </a>
            </li>
        @endcan
        @can('client_location_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.client-locations.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/client-locations") || request()->is("admin/client-locations/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.clientLocation.title') }}
                </a>
            </li>
        @endcan
        @can('client_account_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.client-accounts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/client-accounts") || request()->is("admin/client-accounts/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.clientAccount.title') }}
                </a>
            </li>
        @endcan
        @can('contact_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.contacts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/contacts") || request()->is("admin/contacts/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.contact.title') }}
                </a>
            </li>
        @endcan
        @can('task_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.tasks.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/tasks") || request()->is("admin/tasks/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.task.title') }}
                </a>
            </li>
        @endcan
        @can('sample_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.samples.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/samples") || request()->is("admin/samples/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.sample.title') }}
                </a>
            </li>
        @endcan
        @can('term_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.terms.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/terms") || request()->is("admin/terms/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.term.title') }}
                </a>
            </li>
        @endcan
        @can('elm_notification_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.elm-notifications.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/elm-notifications") || request()->is("admin/elm-notifications/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.elmNotification.title') }}
                </a>
            </li>
        @endcan
        @can('driver_schedule_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.driver-schedules.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/driver-schedules") || request()->is("admin/driver-schedules/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.driverSchedule.title') }}
                </a>
            </li>
        @endcan
        @can('swaprequest_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.swaprequests.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/swaprequests") || request()->is("admin/swaprequests/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.swaprequest.title') }}
                </a>
            </li>
        @endcan
        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>