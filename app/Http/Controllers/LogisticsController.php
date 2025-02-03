<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Shipment;
use App\Models\Task;
use App\Services\AyenatiLogisticsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogisticsController
{

    protected $logisticsService;

    public function __construct(AyenatiLogisticsService $logisticsService)
    {
        $this->logisticsService = $logisticsService;
    }

    public function getShipmentStatus(Request $request)
    {

        try {
            $data = $request->only([
                'dispatchId',
                'senderId',
                'receiverId'
            ]);
            $rules = [
                'dispatchId'   => 'required',
                'senderId'   => 'required',
                'receiverId'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                
                return [
                    'status' => 'error',
                    'message' => [
                        "statusCode" => 400,
                        $validator->errors(),
                    ],
                ];
            }
            $shipment = Shipment::where('shipment.id', $request->dispatchId)
                ->leftJoin('locations as from_location', 'from_location.id', '=', 'shipment.from_location')
                ->leftJoin('locations as to_location', 'to_location.id', '=', 'shipment.to_location')
                ->with('task.driver') 
                ->first();
            if(isset($shipment)) {
                
                if($shipment->from_location != $request->senderId) {
                    return [
                        'error' => [
                            "code" => 3002,
                            "message" => "Invalid sender AYENATI ID If Logistics receive an invalid sender id <PHC> (Ayenati id)",
                        ],
                        "statusCode" => 400,
                    ];
                }
                if($shipment->to_location != $request->receiverId) {
                    return [
                        'error' => [
                            "code" => 3003,
                            "message" => "Invalid receiver AYENATI ID If Logistics receive an invalid receiver id <Lab> (Ayenati id)",
                        ],
                        "statusCode" => 400,
                    ];
                }
                $task = $shipment->task;
                $driver = $task ? $task->driver : null;
    
                // Ensure from_location and to_location exist
                $fromLocation = $shipment->fromLocation;
                $toLocation = $shipment->toLocation;
    
                return response()->json([
                    'message' => 'SUCCESS',
                    'statusCode' => 200,
                    'data' => [
                        "shipmentId" => $request->dispatchId."",
                        "driverId" => $driver?->id, // Safe null check
                        "driverName" => $driver?->name,
                        "driverMobNumber" => $driver?->mobile,
                        "senderId" => $fromLocation?->id."",
                        "senderName" => $fromLocation?->name,
                        "receiverId" => $toLocation?->id."",
                        "receiverName" => $toLocation?->name,
                        "shipmentStatusCode" => $shipment->status_code
                    ]
                ]);
            } else {
                return [
                    'error' => [
                        "code" => 3001,
                        "message" => "Invalid dispatch ID If Logistics receive invalid ID",
                    ],
                    "statusCode" => 400,
                ];
            }
            
        } catch (Exception $e) {
            
            \Log::info($e->getMessage());
            return [
                'error' => [
                    "code" => 500,
                    "message" => "General Error",
                ],
                "statusCode" => 500,
            ];
        }


        // $response = $this->logisticsService->getShipmentStatus(
        //     $validated['dispatchId'],
        //     $validated['senderId'],
        //     $validated['receiverId']
        // );
        // return $response;
    }

    public function updateShipmentStatus(Request $request)
    {
        try {
            $data = $request->only([
                'shipmentId',
                'shipmentStatusCode',
                // 'driverId',
                // 'driverName',
                // 'driverMobNumber',
            ]);
            $rules = [
                'shipmentId'          => 'required|string',
                'shipmentStatusCode'  => 'required|string',
                // 'driverId'            => 'required|integer',
                // 'driverName'          => 'required|string',
                // 'driverMobNumber'     => 'required|string',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                
                return [
                    'status' => 'error',
                    'message' => [
                        "statusCode" => 400,
                        "error" => $validator->errors(),
                    ],
                ];
            }
            $shipment = Shipment::where('id', $request->shipmentId)->first();
            $task = Task::where('id', $shipment->task_id)->first();
            $data = [
                    "shipmentId" => $shipment->id."",
                    "shipmentStatusCode" => $request->shipmentStatusCode,
                    "driverId" => intval($task->driver_id),
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
    }
}
