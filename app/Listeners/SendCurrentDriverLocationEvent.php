<?php

namespace App\Listeners;

use App\Models\Driver;
use App\Models\Sample;
use App\Models\ElmNotification;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Log;

class SendCurrentDriverLocationEvent
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
        \Log::info('SendCurrentDriverLocationEvent');
        if(config('app.env') === 'production') {

        $task = $event->task;
        \Log::info($task);
        if($task->takasi == 'YES')
        {

//            $takasi_number =  'TAKASI-0001000066';
//            if($task->to_takasi_number != null)
//            {
//                $takasi_number =  $task->to_takasi_number;
//            }
           $driver = Driver::with('car')->get()->find($task->driver_id);

            // check tracking system to get the lat/lng of car
            if($driver == null || $driver->car == null || $driver->car->imei == null)
            {
                return;
            }
            $carImei = $driver->car->imei;
//            $data = Http::get('https://api.track.tmtgps.io/latest.php?key=A49797F92E5CF1FF80ADB00C1F5B2DE8&imei='.$carImei);
//            $car=$data->json();
//            if($car == null)
//            {
//                \Log::info($driver);
//                return;
//            }
            $samples = Sample::where('task_id',$task->id)->pluck('barcode_id')->toArray();
            $requestBody =[
                'code' => 'mtc-coordinates',
//                'AWBNo' => [$task->id],
                'AWBNo' => $samples,
                'latitude' => $driver->lat,
                'courierId' => $task->driver_id,
                'longitude' => $driver->lng,
            ];
//            \Log::info($requestBody);
            $response = Http::withHeaders([
                'token' => 'ogpRRpkdCh8G4JhAGdFj4Q'
            ])->post('https://labprox.com/api/1.0/public/events', $requestBody);
            $body = $response->body();
            $notification = new ElmNotification();
            $notification->task_id = $task->id;
            $notification->response_body = $body;
            $notification->type = 'SendCurrentDriverLocationEvent';
            $notification->save();
        }else{
                if($task->billing_client == 42 || $task->billing_client == 33)
                {
                    \Log::info("SendCurrentDriverLocationEvent");
                    \Log::info("New TMS");
                    $driver = Driver::with('car')->get()->find($task->driver_id);

                    // check tracking system to get the lat/lng of car
                    if($driver == null || $driver->car == null || $driver->car->imei == null)
                    {
                        return;
                    }
                    $carImei = $driver->car->imei;
                    $samples = Sample::where('task_id',$task->id)->pluck('barcode_id')->toArray();
                    $requestBody =[
                        'code' => 'mtc-coordinates',
                        'AWBNo' => $samples,
                        'latitude' => $driver->lat,
                        'courierId' => $task->driver_id,
                        'longitude' => $driver->lng,
                    ];
                    \Log::error($requestBody);
                    $response = Http::withHeaders([
                        'token' => 'Nxg30ULHoiHqdo6oOjncAAM3KEmQl67m3vz7sj8FBL1eXfSDr7OJz7AaJpdC'
                        ])->post('https://labprox.com/api/1.0/public/events',$data );
                    //     'token' => 'MGQ1NqU5ZTHqNWfwYtq0NjclLTg5MqEtMdM0ZTC3MjBhZDZk'
                    // ])->post('https://uat.labprox.com/api/1.0/public/events', $requestBody);
                    $body = $response->body();
                    $notification = new ElmNotification();
                    $notification->task_id = $task->id;
                    $notification->response_body = $body;
                    $notification->type = 'SendCurrentDriverLocationEvent';
                    $notification->save();
                }
            
            }

        }
    }
}
