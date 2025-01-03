<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\ApiAyenati;
use App\Models\AyenatiToken;
use Carbon\Carbon; // Import Carbon

class GenerateAtenatiTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $existingToken = AyenatiToken::orderBy('created_at', 'desc')->first();

        if ($existingToken) {
            // Check if the existing token is still valid based on issued_at and expires_in
            $issuedAt = $existingToken->issued_at;
            $expiresIn = $existingToken->expires_in;
            $expirationTime = Carbon::createFromTimestamp($issuedAt)->addSeconds($expiresIn);

            if ($expirationTime->isFuture()) {
                // Use the existing active token
                $accessToken = $existingToken->access_token;
                // Perform any necessary tasks with the existing token
                return;
            }
        }
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Basic Y1FyaGprRXNFQ3p4azhJcUc2cnpJckhNdmhObG02Z3I6c3lyR2RuNW1zc2pXQ2dHNA==',
        ])
        ->asForm()->post('https://api.lean.sa/oauth/token', [
            'grant_type' => 'client_credentials',
        ]);
       

        if ($response->successful()) {
            // $accessToken = $response->json('access_token');
            $responseData = $response->json();
            // Store the access token in the database
            ApiAyenati::create([
                'api_url' => 'https://api.lean.sa/oauth/token',
                'response_flag' => 'success',
                'response' => json_encode($responseData),
            ]);


            // Store the response data in the 'ayenati_tokens' table
            AyenatiToken::create([
                'access_token' => $responseData['access_token'],
                'token_type' => $responseData['token_type'],
                'issued_at' => $responseData['issued_at'],
                'expires_in' => $responseData['expires_in'],
                'developer_email' => $responseData['developer_email'],
                'application_name' => $responseData['application_name'],
                'api_product_list' => $responseData['api_product_list'],
            ]);
        } else {
            $errorMessage = $response->body();
            \Log::error($errorMessage);
            // Store the failed response in the database
            // ApiAyenati::create([
            //     'api_url' => 'https://api.lean.sa/oauth/token',
            //     'response_flag' => 'failed',
            //     'response' => $response->body(),
            // ]);
        }
    }
}
