<?php

namespace App\Http\Controllers;

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
                        "error" => [
                            "code" => 400,
                            "message" => "Error in paramters"
                        ]
                    ],
                ];
            }
            $shipment = Shipment::where('shipment.id', $request->dispatchId)
                ->where('shipment.from_location', $request->senderId)
                ->where('shipment.to_location', $request->receiverId)
                ->leftJoin('locations as from_location', 'from_location.id', '=', 'shipment.from_location')
                ->leftJoin('locations as to_location', 'to_location.id', '=', 'shipment.to_location')
                ->with('task.driver') // Ensure relationships exist in the model
                ->first();

            // Ensure task and driver exist
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
            return ["tets"];
        }


        // $response = $this->logisticsService->getShipmentStatus(
        //     $validated['dispatchId'],
        //     $validated['senderId'],
        //     $validated['receiverId']
        // );
        // return $response;
    }
}
