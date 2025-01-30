<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\AyenatiLogisticsService;
use Illuminate\Http\Request;

class LogisticsController
{
    
    protected $logisticsService;

    public function __construct(AyenatiLogisticsService $logisticsService)
    {
        $this->logisticsService = $logisticsService;
    }

    public function getShipmentStatus(Request $request) {
        $validated = [
            'dispatchId' => $request->dispatchId,
            'senderId' => $request->senderId,
            'receiverId' => $request->receiverId,
        ];
        $shipment = Shipment::where('dispatchId', 'id')->first();
        return $shipment;
        // $response = $this->logisticsService->getShipmentStatus(
        //     $validated['dispatchId'],
        //     $validated['senderId'],
        //     $validated['receiverId']
        // );
        // return $response;
    }
}
