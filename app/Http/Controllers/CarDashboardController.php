<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Car;
use App\Models\Notifications;
use App\Models\Driver;
use App\Models\Sample;
use App\Models\Task;
use App\Models\Client;
use App\Models\Afaqi;
use App\Models\Location;
use DB;
use Illuminate\Support\Carbon;

use Akaunting\Apexcharts\Chart;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Gate;
use Symfony\Component\HttpFoundation\Response;

class CarDashboardController extends Controller
{
    //

    public function index()
    {
        abort_if(Gate::denies('car-dashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $imeis = Car::where('afaqi', true)->pluck('imei')->toArray();

        $cars = [];
        $token = $this->generateAndSaveToken();//'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwaS5hZmFxeS5zYS9hdXRoL2xvZ2luIiwiaWF0IjoxNjkxOTIyMzU3LCJleHAiOjE2OTQ1MTQzNTcsIm5iZiI6MTY5MTkyMjM1NywianRpIjoiSjVMQUpPMk50MUtQZW1XMCIsInN1YiI6IjYyNDFhNTAyYWE1MGM4NjY1NTdkZjA1YiIsInBydiI6IjI3MGVjZmM3ZWEzZWQ5MzdlYTg0OTM2MmEzYTUwOTEwYzZkOGNlNGYifQ.PnxbuNX6TOVhSUzmQRiVKVEYhsatZ8fxg_6koYBni5A';
        // $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmFmYXF5LnNhL2F1dGgvbG9naW4iLCJpYXQiOjE3MzYwMTI2OTQsImV4cCI6MTczODYwNDY5NCwibmJmIjoxNzM2MDEyNjk0LCJqdGkiOiIydURlMXF4aEhGcVNvOHNpIiwic3ViIjoiNjVmOTRiM2QxNTRmNTExYTZkMDAyZjIyIiwicHJ2IjoiMjcwZWNmYzdlYTNlZDkzN2VhODQ5MzYyYTNhNTA5MTBjNmQ4Y2U0ZiJ9.CTehf02lax9werqBjr2m4biWpQaJFXRdls_F6di7jCs";
        // dd($imeis);
        $data = $this->getVehicleDataCustom($token, $imeis);
        if($data) {
            foreach ($data['data'] as $vehicle)
            {
                // create an array for the car
                $car = [
                    'id' => $vehicle['id'],
                    'name' => $vehicle['n'],
                    'i' => $vehicle['i'],
                    'profile' => $vehicle['profile'],
                    'sensors' => []
                ];
                $tempSensors = array_filter($vehicle['sensors'], function($sensor) {
                    return $sensor['t'] == 'temperature';
                    // return $sensor['n'] == 'TEMP1' || $sensor['n'] == 'TEMP2' || $sensor['n'] == 'TEMP3';
                });
                // foreach ($tempSensors as $sensor) {
                //     // add the sensor to the car's sensors array
                //     $car['sensors'][] = $sensor;
                // }
                // iterate through each sensor in the vehicle
                $car['sensors'] = $tempSensors;
                $cars[] = $car;

                // \Log::alert($cars);

            }
        } else {
            \Log::info("failed response");
        }
        // foreach ($imeis as $imei) {
        //     // $afaqiVehicleId = '357073294755919';
        //     $afaqiVehicleId = $imei;
        //     $response = $this->getVehicleData($token, $afaqiVehicleId);
        //     // \Log::info( $response);
        //     if ($response) {
        //         // decode the JSON response
        //         $data = $response;
        //         // $data = json_decode($response, true);

        //         if ($data['status_code'] == 200) {
        //             // create an empty array to store grouped sensors

        //             // iterate through each vehicle in the response
        //             foreach ($data['data'] as $vehicle)
        //             {
        //                 // create an array for the car
        //                 $car = [
        //                     'id' => $vehicle['id'],
        //                     'name' => $vehicle['n'],
        //                     'i' => $vehicle['i'],
        //                     'profile' => $vehicle['profile'],
        //                     'sensors' => []
        //                 ];
        //                 $tempSensors = array_filter($vehicle['sensors'], function($sensor) {
        //                     return $sensor['t'] == 'temperature';
        //                     // return $sensor['n'] == 'TEMP1' || $sensor['n'] == 'TEMP2' || $sensor['n'] == 'TEMP3';
        //                 });
        //                 // foreach ($tempSensors as $sensor) {
        //                 //     // add the sensor to the car's sensors array
        //                 //     $car['sensors'][] = $sensor;
        //                 // }
        //                 // iterate through each sensor in the vehicle
        //                 $car['sensors'] = $tempSensors;
        //                 $cars[] = $car;

        //                 // \Log::alert($cars);

        //             }
        //         } else {
        //             // handle error here
        //             \Log::info("stauts code <> 200");
        //         }
        //     } else {
        //         // handle error here
        //         \Log::info("failed response");
        //     }
        // }


        return view('car-dashboard',[
            'cars' => $cars,
        ]);


    }



public function getVehicleData($token, $afaqiVehicleId) {
    $url = 'http://api.afaqy.pro/units/lists?token=' . $token;
    $response = Http::withoutVerifying()
    ->retry(3, 1000)
    ->timeout(30)
    ->post($url, [
        'data' => [
            'simplify' => 1,
            'filters' => [
                'imei' => [
                    'value' => $afaqiVehicleId
                ]
            ],
            'projection' => [
                'basic',
                'last_update',
                'sensors_last_val',
                'counters',
                'sensors'
            ]
        ]
    ]);
    if ($response->ok()) {
        return $response->json();
    } else {
        return null; // handle error here
    }
}
public function getVehicleDataCustom($token, $afaqiVehicles) {
    $url = 'https://api.afaqy.sa/units/lists?token=' . $token;
    $response = Http::withoutVerifying()
    // ->retry(3, 1000)
    // ->timeout(30)
    ->post($url, [
        'data' => [
            'simplify' => 1,
            'filters' => [
                'imei' => [
                    'value' => $afaqiVehicles,
                    'op' => 'in'
                ],
            ],
            'projection' => [
                'basic',
                'last_update',
                'sensors_last_val',
                'counters',
                'sensors'
            ]
        ]
    ]);
    if ($response->status() == 200) {
        return $response->json();
    } else {
        // dd([
        //     'status' => $response->status(),
        //     'error' => $response->body(),
        // ]);
        return null; // handle error here
    }
}

public function generateAndSaveToken()
    {


        // Check if a token already exists for today
        // $existingToken = Afaqi::where('created_at', today())->first();
        $lastRecord = Afaqi::latest()->first();

        if (!$lastRecord || Carbon::parse($lastRecord->created_at)->isToday()) {

            // get from database
            return $lastRecord->token;
        }

        // generate from API
        $url = "https://api.afaqy.sa/auth/login";
        $payload = [
            "data" => [
                "username" => "MTC1",
                "password" => "Mtc@123",
                "lang" => "en",
                "expire" => 24,
            ],
        ];

        $response = Http::withoutVerifying()
        ->retry(3, 1000)
        ->timeout(30)
        ->post($url, $payload);

        if ($response->status() == 200) {
            $data = $response->json();
            $token = $data["data"]["token"];

            $tokenModel = new Afaqi();
            $tokenModel->token = $token;
            $tokenModel->save();
            return  $token ;
        }
    }

}
