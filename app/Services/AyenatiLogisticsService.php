<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AyenatiLogisticsService
{
    /**
     * Get shipment status from Ayenati Logistics API.
     *
     * @param string $carrierId
     * @param string $dispatchId
     * @param string $senderId
     * @param string $receiverId
     * @return array
     */
    public function getShipmentStatus(string $dispatchId, string $senderId, string $receiverId): array
    {
        // Get the base URL from the environment variables
        $baseURL = config('services.ayenati.base_url');
        $carrierId = config('services.ayenati.carrierId');
        $response = Http::withoutVerifying()->withHeaders([
            'carrierId' => $carrierId,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->post("$baseURL/ayenati-logistics/getShipmentStatus", [
            'dispatchId' => $dispatchId,
            'senderId' => $senderId,
            'receiverId' => $receiverId,
        ]);

        if ($response->successful()) {
            return [
                'statusCode' => $response->json()->statusCode,
                'data' => $response->json()->data
            ];
        }

        // Handle errors
        return [
            'status' => 'error',
            'message' => $response->body(),
        ];
        
        return [
            'statusCode' => $response->json()->statusCode,
            'error' => [
                'code' => $response->json()->error->code,
                'message' => $response->json()->error->message
            ]
        ];
    }
}
