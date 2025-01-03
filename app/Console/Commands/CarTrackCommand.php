<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;
use App\Models\CarTracking;
use App\Models\Sample;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Task;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Str;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\Afaqi;
class CarTrackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car-track:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('CarTrackCommand start');
        $token = $this->logintoAfaqi();
        $tasks = Task::with('car')->where('status','=','IN_FREEZER')->get();
        foreach ($tasks as $task) {
            $afaqi = false;
            $car_id = null;
            $car_imei = null;
            if (isset($task->driver)){
                if (isset($task->driver->car)){
                    $car_id = $task->driver->car->id;
                    $car_imei = $task->driver->car->imei;
                    $afaqi = $task->driver->car->afaqi;
                }
            }
            if($car_imei && $car_id && $afaqi) {
                $url = 'http://api.afaqy.pro/units/lists?token=' . $token;
                $response = Http::post($url, [
                    'data' => [
                        'simplify' => 1,
                        'filters' => [
                            'imei' => [
                                'value' => $car_imei
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
                $sensors = null;
                if ($response->ok()) {
                    $sensors = $response->json();
                }
                if (isset($sensors['data']) && !empty($sensors['data'])) {
                    foreach ($sensors['data'] as $vehicle) {
                        $tempSensors = array_filter($vehicle['sensors'], function ($sensor) {
                            return $sensor['t'] == 'temperature';
                        });
                        $lastUpdate = $vehicle['upat'] ?? null;
                        $lat = $vehicle['lu']['lat'] ?? null;
                        $lng = $vehicle['lu']['lng'] ?? null;
                        $dts = $vehicle['lu']['dts'] ?? null;
                        $imei = $vehicle['i'] ?? $car_imei;
                        $temp1 = null;
                        $temp2 = null;
                        $temp3 = null;
                        foreach ($tempSensors as $temp) {
                            if ($temp['param'] == 'temp5') {
                                $value = $temp['last_val']['value'];
                                $temp1 = $value;
                            } elseif ($temp['param'] == 'temp6') {
                                $value = $temp['last_val']['value'];
                                $temp2 = $value;
                            } elseif ($temp['param'] == 'temp7') {
                                $value = $temp['last_val']['value'];
                                $temp3 = $value;
                            }
                        }


                        // \Log::alert($cars);

                    }
                    // \Log::info( $res->getBody());
                    // \Log::info($sensors);
                    if (!empty($temp1) && (!empty($temp2) || !empty($temp3)) && ($imei == $car_imei)) {
                        $lastrecord = CarTracking::where('car_id', $car_id)
                            ->where('task_id', $task->id)
                            ->orderby('created_at', 'desc')->first();
                        if (isset($lastrecord->id)) {
                            if (isset($lastrecord->temp5) && $lastrecord->temp5 != "$temp1" || (!empty($temp2) && $lastrecord->temp6 != "$temp2") || (!empty($temp3) && $lastrecord->temp7 != "$temp3")) {
                                $carTracking = new CarTracking();
                                $carTracking->lat = $lat;
                                $carTracking->lng = $lng;
                                $carTracking->temp5 = $temp1;
                                $carTracking->temp6 = $temp2;
                                $carTracking->temp7 = $temp3;
                                $carTracking->imei = $imei;
                                $carTracking->afaqi_updated_at = $lastUpdate;
                                $carTracking->car_id = $car_id;
                                $carTracking->task_id = $task->id;
                                $carTracking->dts = $dts;
                                $carTracking->save();
                            }
                        }else{
                            $carTracking = new CarTracking();
                            $carTracking->lat = $lat;
                            $carTracking->lng = $lng;
                            $carTracking->temp5 = $temp1;
                            $carTracking->temp6 = $temp2;
                            $carTracking->temp7 = $temp3;
                            $carTracking->imei = $imei;
                            $carTracking->afaqi_updated_at = $lastUpdate;
                            $carTracking->car_id = $car_id;
                            $carTracking->task_id = $task->id;
                            $carTracking->dts = $dts;
                            $carTracking->save();
                        }
                    }
                }

            }
        }
        return Command::SUCCESS;
    }

    protected function logintoAfaqi()
    {
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

        $response = Http::post($url, $payload);

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
