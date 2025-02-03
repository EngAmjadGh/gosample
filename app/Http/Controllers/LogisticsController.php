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
            if($shipment->from_location != $request->senderId) {
                return [
                    'status' => 'error',
                    'message' => [
                        "statusCode" => 3002,
                        "error" => "Invalid sender AYENATI ID If Logistics receive an invalid sender id <PHC> (Ayenati id)",
                    ],
                ];
            }
            if($shipment->to_location != $request->receiverId) {
                return [
                    'status' => 'error',
                    'message' => [
                        "statusCode" => 3003,
                        "error" => "Invalid receiver AYENATI ID If Logistics receive an invalid receiver id <Lab> (Ayenati id)",
                    ],
                ];
            }
            
            if(isset($shipment)) {
                $task = $shipment->task;
                $driver = $task ? $task->driver : null;
    
                // Ensure from_location and to_location exist
                $fromLocation = $shipment->fromLocation;
                $toLocation = $shipment->toLocation;
    
                return response()->json([
                    'message' => 'SUCCESS',
                    'statusCode' => 200,
                    'data' => [
                        "shipmentId" => strval($shipment->id),
                        "driverId" => $driver?->id, // Safe null check
                        "driverName" => $driver?->name,
                        "driverMobNumber" => $driver?->mobile,
                        "senderId" => strval($fromLocation?->id),
                        "senderName" => $fromLocation?->name,
                        "receiverId" => strval($toLocation?->id),
                        "receiverName" => $toLocation?->name,
                        "shipmentStatusCode" => $shipment->status_code
                    ]
                ]);
            } else {
                return [
                    'status' => 'error',
                    'message' => [
                        "statusCode" => 3001,
                        "error" => "Invalid dispatch ID If Logistics receive invalid ID",
                    ],
                ];
            }
            
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
                    "shipmentId" => $shipment->id,
                    "shipmentStatusCode" => $request->shipmentStatusCode,
                    "driverId" => $task->driver_id,
                    "driverName" => $task->driver->name,
                    "driverMobNumber" => $task->driver->mobile
            ];
            $response = Http::withHeaders([
                // 'token' => 'ogpRRpkdCh8G4JhAGdFj4Q'
            ])->post('https://testelab.seha.sa/api/logistics/updateShipmentStatus', $data );
            $body = $response->body();
            
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
