<?php

// Route::redirect('/welcome', '/welcome');
Route::redirect('/', '/login');
// Route::redirect('/login', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/daily-operation', [App\Http\Controllers\DailyOperationController::class, 'index'])->name('operation');
    Route::get('/car-dashboard', [App\Http\Controllers\CarDashboardController::class, 'index'])->name('cardashboard');
    Route::post('/daily-operation', [App\Http\Controllers\DailyOperationController::class, 'index'])->name('operation.search');
    Route::get('/tasks-dashboard', [App\Http\Controllers\HomeController::class, 'tasksdashboard'])->name('tasksdashboard');
    Route::post('/tasks-dashboard', [App\Http\Controllers\HomeController::class, 'tasksdashboard'])->name('tasksdashboard.search');
    Route::get('/map', [App\Http\Controllers\HomeController::class, 'map'])->name('map');
    Route::post('/map', [App\Http\Controllers\HomeController::class, 'map'])->name('map.search');
    Route::get('/delayeddashboard', [App\Http\Controllers\DelayedDashboardController::class, 'index'])->name('delayeddashboard');
    Route::get('/welcome', [App\Http\Controllers\HomeController::class, 'welcome'])->name('welcome');

});


Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Drivers
    Route::delete('drivers/destroy', 'DriversController@massDestroy')->name('drivers.massDestroy');
    Route::resource('drivers', 'DriversController');

    // Cars
    Route::delete('cars/destroy', 'CarsController@massDestroy')->name('cars.massDestroy');
    Route::resource('cars', 'CarsController');

    // Attendances
    Route::delete('attendances/destroy', 'AttendancesController@massDestroy')->name('attendances.massDestroy');
    Route::resource('attendances', 'AttendancesController');

    // Barcodes
    Route::get('barcodes/generate', 'BarcodesController@generate')->name('barcodes.generate');
    Route::post('barcodes/generate', 'BarcodesController@generateBarcodes')->name('barcodes.generateBarcodes');
    Route::delete('barcodes/destroy', 'BarcodesController@massDestroy')->name('barcodes.massDestroy');
    Route::resource('barcodes', 'BarcodesController');

    // Car Driver
    Route::delete('car-drivers/destroy', 'CarDriverController@massDestroy')->name('car-drivers.massDestroy');
    Route::resource('car-drivers', 'CarDriverController');

    // Car Link History
    Route::delete('car-link-histories/destroy', 'CarLinkHistoryController@massDestroy')->name('car-link-histories.massDestroy');
    Route::resource('car-link-histories', 'CarLinkHistoryController');

    // Clients
    Route::delete('clients/destroy', 'ClientsController@massDestroy')->name('clients.massDestroy');
    Route::post('clients/media', 'ClientsController@storeMedia')->name('clients.storeMedia');
    Route::post('clients/ckmedia', 'ClientsController@storeCKEditorImages')->name('clients.storeCKEditorImages');
    Route::resource('clients', 'ClientsController');

    // Locations
    Route::delete('locations/destroy', 'LocationsController@massDestroy')->name('locations.massDestroy');
    Route::resource('locations', 'LocationsController');

    // Containers
    Route::delete('containers/destroy', 'ContainersController@massDestroy')->name('containers.massDestroy');
    Route::resource('containers', 'ContainersController');

    // Client Location
    Route::delete('client-locations/destroy', 'ClientLocationController@massDestroy')->name('client-locations.massDestroy');
    Route::resource('client-locations', 'ClientLocationController');

    // Client Accounts
    Route::delete('client-accounts/destroy', 'ClientAccountsController@massDestroy')->name('client-accounts.massDestroy');
    Route::resource('client-accounts', 'ClientAccountsController');

    // Contacts
    Route::delete('contacts/destroy', 'ContactsController@massDestroy')->name('contacts.massDestroy');
    Route::resource('contacts', 'ContactsController');

    // Tasks
    Route::post('tasks', 'TasksController@export')->name('tasks.export');


    Route::get('tasks/scan', 'TasksController@scan')->name('tasks.scan');
    Route::get('tasks/unused', 'TasksController@unUsed')->name('tasks.unused');
    Route::get('tasks/missing', 'TasksController@missing')->name('tasks.missing');
    Route::delete('tasks/destroy', 'TasksController@massDestroy')->name('tasks.massDestroy');
    Route::resource('tasks', 'TasksController');


    Route::get('scheduled-driver', 'ScheduledTaskController@indexSchedule')->name('tasks.indexSchedule');

    Route::get('/export-excel', 'TasksController@exportExcelDetails')->name('tasks.export-excel');


    // Samples
    Route::delete('samples/destroy', 'SamplesController@massDestroy')->name('samples.massDestroy');
    Route::resource('samples', 'SamplesController');


    Route::get('lost', 'SamplesController@lost')->name('samples.lost');
    Route::get('pickupdelayed', 'TasksController@pickupdelayed')->name('tasks.pickupdelayed');
    Route::get('dropdelayed', 'TasksController@dropdelayed')->name('tasks.dropdelayed');
    Route::get('collectedDelayed', 'TasksController@collectedDelayed')->name('tasks.collectedDelayed');
    Route::get('outfreezerdelayed', 'TasksController@outfreezerdelayed')->name('tasks.outfreezerdelayed');


    // Terms
    Route::delete('terms/destroy', 'TermsController@massDestroy')->name('terms.massDestroy');
    Route::resource('terms', 'TermsController');

    // Elm Notifications
    Route::resource('elm-notifications', 'ElmNotificationsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Driver Schedule
    Route::delete('driver-schedules/destroy', 'DriverScheduleController@massDestroy')->name('driver-schedules.massDestroy');
    Route::resource('driver-schedules', 'DriverScheduleController');

    // Swaprequest
    Route::delete('swaprequests/destroy', 'SwaprequestController@massDestroy')->name('swaprequests.massDestroy');
    Route::resource('swaprequests', 'SwaprequestController');


     // Zones
     Route::delete('zones/destroy', 'ZonesController@massDestroy')->name('zones.massDestroy');
     Route::resource('zones', 'ZonesController');

     // Client Driver
     Route::delete('client-drivers/destroy', 'ClientDriverController@massDestroy')->name('client-drivers.massDestroy');
     Route::resource('client-drivers', 'ClientDriverController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

     // Notifications
     Route::resource('notifications', 'NotificationsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

     Route::resource('shipments', 'ShipmentsController', ['except' => ['edit', 'update', 'destroy']]);


     Route::get('system-calendar', 'SystemCalendarController@index')->name('systemCalendar');

     Route::post('task-report', 'TasksController@export')->name('reportExport');

      // Scheduled Task
    Route::delete('scheduled-tasks/destroy', 'ScheduledTaskController@massDestroy')->name('scheduled-tasks.massDestroy');
    Route::resource('scheduled-tasks', 'ScheduledTaskController');

    Route::get('schedule/quick', 'ScheduledTaskController@quick')->name('scheduled-tasks.quick');
    Route::post('schedule/quick', 'ScheduledTaskController@quickAction')->name('scheduled-tasks.quickAction');
    Route::get('schedule/delete/{scheduledTask}/parents', 'ScheduledTaskController@deleteBasedOnParent')->name('scheduled-tasks.deleteAllParent');

    // Money Transfer
    Route::delete('money-transfers/destroy', 'MoneyTransferController@massDestroy')->name('money-transfers.massDestroy');
    Route::resource('money-transfers', 'MoneyTransferController');

    Route::get('schedules/logs', 'ScheduleLogController@index')->name('schedules.logs');

    Route::post('shipments/{shipment}/update-notification', 'ShipmentsController@updateAyenatiNotification')->name('shipments.updateNotification');
    Route::post('shipments/{shipment}/assign-driver', 'ShipmentsController@assignDriver')->name('shipments.assignDriver');
    Route::post('shipments/{shipment}/deliver', 'ShipmentsController@deliver')->name('shipments.deliver');

    // Api Ayenati
    Route::resource('api-ayenatis', 'ApiAyenatiController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'App\Http\Controllers\Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
