<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Jobs\GenerateAtenatiTokenJob;
use App\Models\ApiAyenati;
use App\Models\AyenatiToken;
use App\Models\Driver;
use App\Models\Task;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;


class ShipmentsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('shipment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Shipment::with(['task'])->select(sprintf('shipment.*', (new Shipment())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'shipment_show';
                $editGate = 'shipment_edit';
                $deleteGate = 'shipment_delete';
                $crudRoutePart = 'shipments';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('carrier', function ($row) {
                return $row->carrier ? $row->carrier : '';
            });
            $table->editColumn('to_location', function ($row) {
                if (isset($row->task) && isset($row->task->to)){
                    return $row->task->to->name;
                }
                return '';
            });
            $table->editColumn('from_location', function ($row) {
                if (isset($row->task) && isset($row->task->from)){
                    return $row->task->from->name;
                }
                return '';
            });
            // $table->editColumn('sender_lat', function ($row) {
            //     return $row->sender_lat ? $row->sender_lat : '';
            // });
            // $table->editColumn('sender_mobile', function ($row) {
            //     return $row->sender_mobile ? $row->sender_mobile : '';
            // });
            // $table->editColumn('receiver_name', function ($row) {
            //     return $row->receiver_name ? $row->receiver_name : '';
            // });
            // $table->editColumn('receiver_long', function ($row) {
            //     return $row->receiver_long ? $row->receiver_long : '';
            // });
            // $table->editColumn('receiver_lat', function ($row) {
            //     return $row->receiver_lat ? $row->receiver_lat : '';
            // });
            // $table->editColumn('receiver_mobile', function ($row) {
            //     return $row->receiver_mobile ? $row->receiver_mobile : '';
            // });
            $table->editColumn('reference_number', function ($row) {
                return $row->reference_number ? $row->reference_number : '';
            });
            $table->editColumn('pickup_otp', function ($row) {
                return $row->pickup_otp ? $row->pickup_otp : '';
            });
            $table->editColumn('status_code', function ($row) {
                return $row->status_code ? $row->status_code : '';
            });
            $table->editColumn('batch', function ($row) {
                return $row->batch ? $row->batch : '';
            });
            $table->editColumn('journey_type', function ($row) {
                return $row->journey_type ? $row->journey_type : '';
            });
            $table->editColumn('sla_code', function ($row) {
                return $row->sla_code ? $row->sla_code : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'task']);

            return $table->make(true);
        }

        return view('admin.shipments.index');
    }

    public function show(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->load('task');
        $drivers = Driver::all();
        $task = $shipment->task ?? null;

        return view('admin.shipments.show', compact('shipment','drivers','task'));
    }


    public function assignDriver(Request $request, $shipmentId)
    {
        $driverId = $request->input('driver');
        $shipmentw = Shipment::find($shipmentId);
       // Find the selected driver
       $driver = Driver::findOrFail($driverId);
       // Retrieve the last active access token from the token table
       $latestToken = AyenatiToken::orderBy('created_at', 'desc')->first();

       \Log::info($latestToken);
       if (!$latestToken) {
           // Handle the case when there is no access token available
           // Log an error, throw an exception, or take appropriate action
           // generate token
           $errorMessage = 'No access token available.';
           return redirect()->back()->with('error', $errorMessage);
       }

       $accessToken = $latestToken->access_token;

       $result = $this->updateNotificationCall($shipmentw,$driver,$accessToken);
       \Log::error($result);
       if( $result)
       {
            $otp = rand(111111,900999);
            // save drop_off to table of shipment
            $shipmentw->dropoff_otp = $otp;
            $shipmentw->driver_id = $driver->id;
            $shipmentw->status_code = 'confirmed';
            $shipmentw->save();

            $task = Task::find($shipmentw->task_id);
            if (isset($task->id)){
                $task->driver_id = $driver->id;
                $task->pickup_time = now();
                $task->save();
                $driver->sendNotification( 'New Task', 'You have new task',[$driver->fcm_token],$task,'open_task');
            }

            $this->updateDropOffOTP($shipmentw->id,$otp,$accessToken);
            // $shipmentw->dropoff_otp = $otp;
            // $shipmentw->save();
       }else{
            $errorMessage = 'unable to access api.';
            return redirect()->back()->with('error', $errorMessage);
       }



        // Redirect back to the shipment show page or any other desired location
        return redirect()->back();
    }

    public function deliver(Request $request, $shipmentId)
    {

        $shipmentw = Shipment::find($shipmentId);
        \Log::info($shipmentw);
       $driver = Driver::find($shipmentw->driver_id);
       if($driver == null)
       {
            $errorMessage = 'Please assign driver first.';
            return redirect()->back()->with('error', $errorMessage);
       }
       // Retrieve the last active access token from the token table
       $latestToken = AyenatiToken::orderBy('created_at', 'desc')->first();

       if (!$latestToken) {
           // Handle the case when there is no access token available
           // Log an error, throw an exception, or take appropriate action
           // generate token
           $errorMessage = 'No access token available.';
           return redirect()->back()->with('error', $errorMessage);
       }

       $accessToken = $latestToken->access_token;

       $result = $this->deliverCall($shipmentw,$driver,$accessToken);
       if( $result)
       {
            $shipmentw->status_code = 'delivered';
            $shipmentw->save();
       }else{
            $errorMessage = 'unable to access api.';
            return redirect()->back()->with('error', $errorMessage);
       }

        // Redirect back to the shipment show page or any other desired location
        return redirect()->back();
    }

    public function updateNotificationCall($shipment, $driver,$accessToken)
    {
        // Prepare the data for the API request
        $requestData = [
            'shipment_id' => $shipment->id,
            'agent_first_name' => $driver->name,
            'agent_last_name' => $driver->name,
            'agent_national_id' => $driver->national_id,
            'agent_mobile' => $driver->mobile,
            'status_code' => 'confirmed',
            'track_url' => 'https://gosample.com',
            'timestamp' =>  now()->toTimeString(),
        ];


        \Log::error("-----");
        \Log::error("-----");
        \Log::info( $requestData);

        \Log::error("-----");
        \Log::error("-----");
        \Log::error($accessToken);
        \Log::error("-----");
        \Log::error("-----");
        // Make the API request
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.lean.sa/p-ayenati/notifications/updateNotificationDetails', $requestData);

        if ($response->successful()) {
            $responseData = $response->json();
            // Log the successful response in the ApiResponse table
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/p-ayenati/notifications/updateNotificationDetails',
                'response_flag' => 'success',
                'response' => json_encode($responseData),
            ]);
            return true;
            // Handle the successful response as needed
        } else {
            $errorMessage = $response->body();
            \Log::error( $errorMessage);
            GenerateAtenatiTokenJob::dispatch();

            // Log the failed response in the ApiResponse table
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/p-ayenati/notifications/updateNotificationDetails',
                'response_flag' => 'failed',
                'response' => $errorMessage,
            ]);
            return false;
        }
    }
    public function updateDropOffOTP($shipmentId, $otp,$accessToken)
    {

        // Prepare the data for the API request
        $requestData = [
            'shipment_id' => $shipmentId,
            'otp' => $otp,
            'status_code' => 'delivered',
        ];

        // Set the timeout value in milliseconds (e.g., 60 seconds)
        $timeoutMilliseconds = 120000;
        // Make the API request
        $response = Http::withOptions([
            'timeout' => $timeoutMilliseconds / 1000, // Convert milliseconds to seconds
        ])->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.lean.sa/p-ayenati/notifications/updateDropOffOTP', $requestData);

        // Process the API response
        if ($response->successful()) {
            $responseData = $response->json();
            // Log the successful response in the ApiResponse table
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/p-ayenati/notifications/updateDropOffOTP',
                'response_flag' => 'success',
                'response' => json_encode($responseData),
            ]);
        } else {
            $errorMessage = $response->json('message');
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/p-ayenati/notifications/updateDropOffOTP',
                'response_flag' => 'failed',
                'response' => $errorMessage,
            ]);
        }
    }

    public function deliverCall($shipment, $driver,$accessToken)
    {
        // Prepare the data for the API request
        $requestData = [
            'shipment_id' => $shipment->id,
            'agent_first_name' => $driver->name,
            'agent_last_name' => $driver->name,
            'agent_national_id' => $driver->national_id,
            'agent_mobile' => $driver->mobile,
            'status_code' => 'delivered',
            'track_url' => 'https://gosample.com',
            'timestamp' =>  now()->toTimeString(),
        ];

        // Make the API request
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.lean.sa/p-ayenati/notifications/updateNotificationDetails', $requestData);

        if ($response->successful()) {
            $responseData = $response->json();
            // Log the successful response in the ApiResponse table
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/p-ayenati/notifications/updateNotificationDetails',
                'response_flag' => 'success',
                'response' => json_encode($responseData),
            ]);
            return true;
            // Handle the successful response as needed
        } else {
            $errorMessage = $response->body();
            // Log the failed response in the ApiResponse table
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/p-ayenati/notifications/updateNotificationDetails',
                'response_flag' => 'failed',
                'response' => $errorMessage,
            ]);
            return false;
        }
    }

}
