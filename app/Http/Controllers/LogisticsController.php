<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Shipment;
use App\Services\AyenatiLogisticsService;
use Exception;
use Illuminate\Http\Request;
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
                ->where('shipment.from_location', $request->senderId)
                ->where('shipment.to_location', $request->receiverId)
                ->leftJoin('locations as from_location', 'from_location.id', '=', 'shipment.from_location')
                ->leftJoin('locations as to_location', 'to_location.id', '=', 'shipment.to_location')
                ->with('task.driver') 
                ->first();

            
            if(isset($shipment)) {
                $task = $shipment->task;
                $driver = $task ? $task->driver : null;
    
                // Ensure from_location and to_location exist
                $fromLocation = $shipment->fromLocation;
                $toLocation = $shipment->toLocation;
    
                return response()->json([
                    'message' => 'success',
                    'statusCode' => '200',
                    'data' => [
                        "shipmentId" => $shipment->id,
                        "driverId" => $driver?->id, // Safe null check
                        "driverName" => $driver?->name,
                        "driverMobNumber" => $driver?->mobile,
                        "senderId" => $fromLocation?->id,
                        "senderName" => $fromLocation?->name,
                        "receiverId" => $toLocation?->id,
                        "receiverName" => $toLocation?->name,
                        "shipmentStatusCode" => $shipment->status_code
                    ]
                ]);
            }
            
        } catch (Exception $e) {
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

    public function updateShipment(Request $request)
    {
        try {
            $data = $request->only([
                'shipmentId',
                'shipmentStatusCode',
                'driverId',
                'driverName',
                'driverMobNumber',
            ]);
            $rules = [
                'shipmentId'          => 'required|string',
                'shipmentStatusCode'  => 'required|string',
                'driverId'            => 'required|integer',
                'driverName'          => 'required|string',
                'driverMobNumber'     => 'required|string',
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

            $shipment = Shipment::where('shipment.id', $request->shipmentId)
                ->with('task.driver')
                ->first();

            if(isset($shipment)) {
                $shipment->status_code = $request->shipmentStatusCode;
                $shipment->save();
                $task = $shipment->task;
                if($task) {
                    $task->driver_id = $request->driverId;
                    $driver = Driver::find($request->driverId);
                    $driver->name = $request->driverName;
                    $driver->mobile = $request->driverMobNumber;
                    $driver->save();
                }
                
    
                return response()->json([
                    'message' => 'success',
                    'statusCode' => '200'
                ]);
            }
            
        } catch (Exception $e) {
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
