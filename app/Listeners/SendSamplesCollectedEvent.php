<?php

namespace App\Listeners;

use App\Models\ElmNotification;
use App\Models\Location;
use App\Models\Sample;
use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendSamplesCollectedEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        \Log::info('SendSamplesCollectedEvent');
        if(config('app.env') === 'production') {
            $task = $event->task;
            \Log::info($task);
            if ($task->takasi == 'YES') {
//                $takasi_number = 'TAKASI-0001000066';
//                if ($task->takasi_number != null) {
//                    $takasi_number = $task->takasi_number;
//                }
                $location = Location::find($task->from_location);
                $to_location = Location::find($task->to_location);
                $samples = Sample::where('task_id',$task->id)->pluck('barcode_id')->toArray();
                $data =[
                    'code' => 'mtc',
                    'eventCode' => 'SA004',
                    'externalBookingNumber' =>  $samples[0],
                    'eventDetails' => 'PICKED UP',
                    'courierId' => $task->driver_id,
                    'eventDatetime' => Carbon::now(), //'8/11/2021 1:13:36 PM',
                    'comments' => 'Driver collected samples',
                    'bookingNo' => $task->id,
                    'destination' => $to_location->name,
                    'sourceHospital' => $location->name,
                    'sourceMainCity' => 'Riyadh',
                    'sourceSubCity' => 'الرياض',
                    'latitude' => $location->lat,
                    'longitude' => $location->lng,
                ];
//                \Log::info($data);
                // $response = Http::withHeaders([
                //     'token' => 'ogpRRpkdCh8G4JhAGdFj4Q'
                // ])->post('https://labprox.com/api/1.0/public/events', $data );
                // $body = $response->body();
                // $notification = new ElmNotification();
                // $notification->task_id = $task->id;
                // $notification->response_body = $body;
                // $notification->type = 'SendSamplesCollectedEvent';
                // $notification->save();
                // $response = Http::withHeaders([
                //     // 'token' => 'ogpRRpkdCh8G4JhAGdFj4Q'
                // ])->post('https://testelab.seha.sa/api/logistics/updateShipmentStatus', $data );
                // $body = $response->body();


                try {
                    $shipment = Shipment::where('task_id', $task->id)->first();
                    $data = [
                            "shipmentId" => $shipment->id,
                            "shipmentStatusCode" => "Dispatched",
                            "driverId" => $task->driver_id,
                            "driverName" => $task->driver->name,
                            "driverMobNumber" => $task->driver->mobile
                    ];
                    $client = new \GuzzleHttp\Client();
                    $response = $client->post('https://api.leanstg.io/oauth/token', [
                        'headers' => [
                            'Authorization' => 'Basic bUZTTk5sMUN6TzB4QUZLRXhua2IxV3NtZHZDYTZKOEQ6ampuRHJiU2M0RUlSS0lrZw==',
                            'Content-Type' => 'application/x-www-form-urlencoded'
                        ],
                        'form_params' => [
                            'grant_type' => 'client_credentials',
                        ]
                    ]);
            
                    $data2 = json_decode( $response->getBody()->getContents(), true);
        
                    \Log::info($data2['access_token']);
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$data2['access_token'],
                    ])->post('https://api.leanstg.io/p-ayenati/notifications/updateNotificationDetails', $data );
                    $body = $response->body();
                    
                    \Log::info($body);
                    return $body;
                } catch (Exception $e) {
                    \Log::info($e->getMessage());
                    return [
                        'status' => 'error',
                        'message' => [
                            "statusCode" => 500,
                            "error" => "General Error",
                        ],
                    ];
                }


                //To Implement update
            } else{
                if($task->billing_client == 42 || $task->billing_client == 33)
                {
                    \Log::info("SendSamplesCollectedEvent");
                    \Log::info("New TMS");
                    $location = Location::find($task->from_location);
                    $to_location = Location::find($task->to_location);
                    $samples = Sample::where('task_id',$task->id)->pluck('barcode_id')->toArray();
                    if($samples != null && $samples[0] != null)
                    {
                        $data =[
                            'code' => 'mtc',
                            'eventCode' => 'MTC03',
                            'externalBookingNumber' =>  $samples[0],
                            'eventDetails' => 'PICKED UP',
                            'courierId' => $task->driver_id,
                            'eventDatetime' => Carbon::now(), //'8/11/2021 1:13:36 PM',
                            'comments' => 'Driver collected samples',
                            'bookingNo' => $task->id,
                            'destination' => $to_location->name,
                            'sourceHospital' => $location->name,
                            'sourceMainCity' => 'Riyadh',
                            'sourceSubCity' => 'الرياض',
                            'latitude' => $location->lat,
                            'longitude' => $location->lng,
                        ];
                       \Log::info($data);
                        // $response = Http::withHeaders([
                        //     'token' => 'Nxg30ULHoiHqdo6oOjncAAM3KEmQl67m3vz7sj8FBL1eXfSDr7OJz7AaJpdC'
                        //     ])->post('https://labprox.com/api/1.0/public/events',$data );
                        // //     'token' => 'MGQ1NqU5ZTHqNWfwYtq0NjclLTg5MqEtMdM0ZTC3MjBhZDZk'
                        // // ])->post('https://uat.labprox.com/api/1.0/public/events', $data );
                        // $body = $response->body();
                        // $notification = new ElmNotification();
                        // $notification->task_id = $task->id;
                        // $notification->response_body = $body;
                        // $notification->type = 'SendSamplesCollectedEvent';
                        // $notification->save();
                    }
                }
                

            }
        }
    }
}
