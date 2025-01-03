<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;
use App\Models\Sample;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Task;
use DateTime;
use Str;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\Afaqi;


class AfaqiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'afaqi:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'login to afaqi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $username = 'MTC1';
        $password = 'Mtc@123';
            $client = new Client();
            $headers = [
              'Content-Type' => 'application/json'
            ];
            $body = '{
              "data": {
                "username": "'.$username.'",
                "password": "'.$password.'"
              }
            }';
            $request = new Request('POST', 'http://api.afaqy.pro/auth/login', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $response = json_decode((string) $res->getBody(), true);
            // \Log::info($response);
            if($response['status_code'] == 200)
            {
                // save token in db, and return to be used,
                $record = Afaqi::updateOrCreate(
                    ['id' =>  1],
                    ['token' => $response['data']['token']]
                );
            }

        return Command::SUCCESS;
    }
}
