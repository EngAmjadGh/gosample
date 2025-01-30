<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/samples/types/report','App\Http\Controllers\SampleController@report');

Route::post('task/create','App\Http\Controllers\SampleController@createTask');
// Route::post('car/create','App\Http\Controllers\CarController@create');
Route::post('driver/login','App\Http\Controllers\DriverController@login');
Route::post('driver/loginWithMobile','App\Http\Controllers\DriverController@loginWithMobile');
Route::post('driver/profile','App\Http\Controllers\DriverController@profile');
Route::post('driver/tasks','App\Http\Controllers\DriverController@tasks');
Route::post('driver/location','App\Http\Controllers\DriverController@carLocation');
Route::post('sample/new','App\Http\Controllers\SampleController@new');
Route::post('task/nosamples','App\Http\Controllers\SampleController@noSamples');
Route::post('task/collect','App\Http\Controllers\SampleController@collect');
Route::post('task/freezer','App\Http\Controllers\SampleController@freezer');
Route::post('task/freezer/out','App\Http\Controllers\SampleController@freezerOut');
Route::post('tasks/freezer/out','App\Http\Controllers\SampleController@freezerOutMultipleTasks');
Route::post('task/containers/bag','App\Http\Controllers\SampleController@getContainersPerBag');
Route::post('task/close','App\Http\Controllers\SampleController@close');
Route::post('tasks/close','App\Http\Controllers\SampleController@closeTasks');
Route::post('samples/add','App\Http\Controllers\SampleController@addSamplesToTask');
Route::post('samples/bags/add','App\Http\Controllers\SampleController@addSamplesToTaskWithBagsArray');
Route::post('task/bags/get','App\Http\Controllers\SampleController@getBagsByTaskId');
Route::post('samples/box/add','App\Http\Controllers\SampleController@addSamplesToPerBoxTask');
Route::post('sample/container/add','App\Http\Controllers\SampleController@addSampleToContainer');
Route::post('samples/container/add','App\Http\Controllers\SampleController@addSamplesToContainer');
Route::post('samples/container/bags/add','App\Http\Controllers\SampleController@addSamplesToContainerWithMultipleBags');
Route::post('sample/container/remove','App\Http\Controllers\SampleController@removeSampleToContainer');
Route::post('sample/remove','App\Http\Controllers\SampleController@removeSampleFromTask');
Route::post('samples/bag','App\Http\Controllers\SampleController@getBagSamples');
Route::post('samples/bag/type','App\Http\Controllers\SampleController@getBagSamplesWithType');
Route::post('samples/list','App\Http\Controllers\SampleController@samples');
Route::post('bag/container/remove','App\Http\Controllers\SampleController@removeBagFromContainer');
Route::post('task/location/check','App\Http\Controllers\SampleController@checkLocationBarcode');
Route::post('tasks/location/check','App\Http\Controllers\SampleController@checkLocationBarcodeMultipleTasks');
Route::post('container/bags','App\Http\Controllers\SampleController@getBagsOfContainer');
Route::post('driver/notifications','App\Http\Controllers\DriverController@notifications');
Route::post('car/release','App\Http\Controllers\DriverController@releaseCar');
Route::post('driver/client/tasks','App\Http\Controllers\DriverController@clientTask');
Route::post('driver/location','App\Http\Controllers\DriverController@updateDriverLocation');
Route::post('driver/notification/new','App\Http\Controllers\DriverController@sendNotificationToDriver');
Route::post('task/sample/check','App\Http\Controllers\SampleController@checkSample');
Route::post('task/samples/check','App\Http\Controllers\SampleController@checkSamples');
Route::post('task/sample/confirmall','App\Http\Controllers\SampleController@confirmAll');
Route::post('task/samples/valid/check','App\Http\Controllers\SampleController@getConfirmedSamples');
Route::post('driver/samples/valid/check','App\Http\Controllers\SampleController@getConfirmedSamplesPerDriverId');

Route::post('client/samples/confirm','App\Http\Controllers\SampleController@confirmSamples');
Route::post('client/samples/details','App\Http\Controllers\SampleController@getSampleDetails');
Route::post('client/samples/report','App\Http\Controllers\SampleController@generateReport');
Route::post('client/samples/lost','App\Http\Controllers\SampleController@markSamplesAsLost');
Route::post('shipments/create','App\Http\Controllers\ShipmentController@create');
Route::post('shipments/dispatch','App\Http\Controllers\ShipmentController@dispatchShipment');
Route::post('shipments/details','App\Http\Controllers\ShipmentController@getShipmentById');

Route::post('test','App\Http\Controllers\ShipmentController@test');

Route::any('tasks/cache','App\Http\Controllers\DriverController@getTasksFromCache');
Route::any('driver/car/images','App\Http\Controllers\DriverController@uploadPhotes');
Route::any('driver/terms/accept','App\Http\Controllers\DriverController@acceptTerms');
Route::any('driver/terms/get','App\Http\Controllers\DriverController@terms');
Route::any('driver/schedule','App\Http\Controllers\ScheduleController@list');
// Route::any('client/account/role','App\Http\Controllers\ClientAccountController@createRole');


Route::any('swap/create','App\Http\Controllers\SwapController@create');
Route::any('swap/list','App\Http\Controllers\SwapController@listPerDriver');
Route::any('swap/tasks/list','App\Http\Controllers\SwapController@listTasksPerDriver');
Route::any('swap/accept','App\Http\Controllers\SwapController@accept');
Route::any('swap/reject','App\Http\Controllers\SwapController@reject');
Route::any('task/report/export','App\Http\Controllers\DailyOperationController@export');
Route::any('swap/receive','App\Http\Controllers\SwapController@receive');



Route::post('money/transfer/list','App\Http\Controllers\MoneyTransferController@listPerDriver');
Route::post('money/transfer/otp/from/verifiy','App\Http\Controllers\MoneyTransferController@verifyFromOtp');
Route::post('money/transfer/otp/to/verifiy','App\Http\Controllers\MoneyTransferController@verifyToOtp');

Route::post('driver/task/start','App\Http\Controllers\SampleController@startTaskByDriver');
Route::post('driver/task/confirm','App\Http\Controllers\SampleController@confirmTaskByDriver');
Route::post('driver/tasks/confirm','App\Http\Controllers\SampleController@confirmTasksByDriver');
Route::post('driver/task/fromlocation/confirm','App\Http\Controllers\SampleController@confirmFromLocation');
Route::post('driver/task/tolocation/confirm','App\Http\Controllers\SampleController@confirmToLocation');

Route::post('driver-schedule','App\Http\Controllers\DriverController@getDriverSchedule');
Route::post('drivers/schedule','App\Http\Controllers\DriverController@getDriverSchedule');

Route::post('swap/list/acceptall','App\Http\Controllers\SwapController@acceptall');

Route::post('shipments/status-shipment', 'App\Http\Controllers\LogisticsController@getShipmentStatus');