<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Sample;
use App\Models\Task;
use App\Models\Client;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


class ShipmentController extends Controller
{
    public function test(Request $request)
    {
        $response['Protocol'] = "jsonPTS";
        $response['Packets'] =[];

        try {
            $response['Packets'][0]['Id']= $request->Packets[0]['Id'];
         } catch (Exception $e) {
            $response['Packets'][0]['Id']= 123;

        }
        $response['Packets'][0]['Type']=  $request->Packets[0]['Type'] ;//"UploadPumpTransaction";
        $response['Packets'][0]['Message']= "OK";

        return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8')
            ->header('Content-Length', 76);
    }
    public function create(Request $request)
    {
        try {
            $data = $request->only([
                'carrier',
                'ayenati_id',
                
                'sender_name',
                'sender_long',
                'sender_lat',
                'sender_mobile',

                'receiver_lat',
                'receiver_name',
                'receiver_long',
                'receiver_mobile',

                'reference_number',
                'batch',
                'pickup_otp',
                
                // 'pickup_id','Reciever_Aynanti_ID',
                'journey_type',
                'notes',
                'sla_code',
               ]);
            $rules = [
                'ayenati_id'   => 'required',
                'carrier'   => 'required',
                // 'pickup_id'   => 'required',
                'sender_name'   => 'required',
                'sender_long'   => 'required',
                'sender_lat'   => 'required',
//                'sender_mobile'   => 'required',

                'receiver_name'   => 'required',
                'receiver_long'   => 'required',
                'receiver_lat'   => 'required',
//                'receiver_mobile'   => 'required',


                'reference_number'   => 'required',
                'batch'   => 'required',
                'pickup_otp'   => 'required',

               
                'journey_type'   => 'required',
                'sla_code'   => 'required',
//                'notes'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {


                DB::beginTransaction();
                // check api key
                $api_key = $request->header('API-KEY');
                \Log::info( $api_key);
                \Log::info( $request->headers->all());
                //e5cc3ffa-2501-45ff-80c3-41f581c0a40d
                if($api_key != '1ba6c71c-bfa3-423b-92d4-05097370eb86')
                {
                    // return $request->headers->all();
                    //c12629b6-9fc7-11ed-a8fc-0242ac120002
                    // return $this->ayenatiResponse(false,'2033','API Key is wrong');
                }

                $otp = rand(111111,900999);
                 // add locations to tables,
                // check sender location
                $from_location_id = 1;
                // check locations if already defined under Ayenti account
                $from_location = Location::where('name',$request->sender_name)->first();
                if($from_location == null)
                {
                    // create new location
                    $new_location = new Location();
                    $new_location->name = $request->sender_name;
                    $new_location->arabic_name = $request->sender_name;
                    $new_location->lat = $request->sender_lat;
                    $new_location->lng = $request->sender_long;
                    $new_location->mobile = $request->sender_mobile;
                    $new_location->status = 1;
                    $new_location->description = 'location under ayenti';
                    $new_location->save();
                    $from_location_id = $new_location->id;
                } else{
                    $from_location_id = $from_location->id;
                }

                // check receiver location
                $to_location_id = 1;
                $to_location = Location::where('name',$request->receiver_name)->first();
                if($to_location == null)
                {
                    // create new location
                    $new_location = new Location();
                    $new_location->name = $request->receiver_name;
                    $new_location->arabic_name = $request->receiver_name;
                    $new_location->lat = $request->receiver_lat;
                    $new_location->lng = $request->receiver_long;
                    $new_location->mobile = $request->receiver_mobile;
                    $new_location->status = 1;
                    $new_location->description = 'location under ayenti';
                    $new_location->save();
                    $to_location_id = $new_location->id;
                } else{
                    $to_location_id = $to_location->id;
                }


               
                // add task from location, and link shipment with task


                
                //1ba6c71c-bfa3-423b-92d4-05097370eb86
//                return $api_key.'';
                // check from location
               
                // $from_location = Location::find($from_location_id);
                // if($from_location == null)
                // {
                //     // create new location
                //     $from_location = new Location();
                //     $from_location->id =  $from_location_id;
                //     $from_location->mobile =  $request->sender_mobile;
                //     $from_location->lat = $request->sender_lat;
                //     $from_location->lng =  $request->sender_long;
                //     $from_location->name =  $request->sender_name;
                //     $from_location->description =  $request->sender_name;
                //     $from_location->save();
                // }


                // // check to location
                // $to_location_id = 1;
                // $to_location = Location::find($to_location_id);
                // if($to_location == null)
                // {
                //     // create new location
                //     $to_location = new Location();
                //     $to_location->id =  $to_location_id;
                //     $to_location->mobile =  $request->receiver_mobile;
                //     $to_location->lat = $request->receiver_lat;
                //     $to_location->lng =  $request->receiver_long;
                //     $to_location->name =  $request->receiver_name;
                //     $to_location->description =  $request->receiver_name;
                //     $to_location->save();
                // }

                // insert in database

//                check batchId
                // $oldData = Shipment::where('batch',  implode(" ",$request->batch))->first();
                // if($oldData != null)
                // {
                //     // return shipment
                //     $shipment_id= new Task();
                //     $shipment_id->shipment_id =$oldData->id;
                //     return $this->ayenatiResponse(true,'0','',$shipment_id);
                // }

                $recordShipment = new Shipment();
                $recordShipment->carrier    = $request->carrier;
//                $record->pickup_id    = $request->Pickup_id;
                // $recordShipment->sender_name    = $request->sender_name;
                // $recordShipment->sender_long    = $request->sender_long;
                // $recordShipment->sender_lat    = $request->sender_lat;
                // $recordShipment->sender_mobile    = $request->sender_mobile;
                $recordShipment->from_location    = $from_location_id;

                // $recordShipment->receiver_name    = $request->receiver_name;
                // $recordShipment->receiver_long    = $request->receiver_long;
                // $recordShipment->receiver_lat    = $request->receiver_lat;
                // $recordShipment->receiver_mobile         = $request->receiver_mobile;
                $recordShipment->to_location    = $to_location_id;
                $recordShipment->reference_number         = $request->reference_number;
                $recordShipment->pickup_otp         = $request->pickup_otp;
                $recordShipment->journey_type         = $request->journey_type;
                $recordShipment->Batch         = implode(" ",$request->batch) ;
                $recordShipment->notes       = $request->notes;
                $recordShipment->sla_code       = $request->sla_code;
                $recordShipment->dropoff_otp       = $otp;
                $recordShipment->status_code       = 'new';
//                $record->Reciever_Aynanti_ID       = $request->Reciever_Aynanti_ID;
                $recordShipment->save();


                $billing_client = Client::where('english_name','Ayenti')->first();
                // create task
                $record = new Task();
                $record->from_location    =  $from_location_id;
                $record->status         = 'NEW';
                $record->cost         = 0;
                $record->driver_id         = null;
                $record->to_location         =  $to_location_id;
                $record->billing_client       = $billing_client->id;
                $record->ayenati       = 'YES';
                $record->save();


                $recordShipment->task_id = $record->id;
                $recordShipment->save();


                // return shipment
                $shipment_id= new Task();
                $shipment_id->shipment_id = $recordShipment->id;

                DB::commit();
                return $this->ayenatiResponse(true,'0','',$shipment_id);

            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->ayenatiResponse(false,'2033','system error');
        }
    }


    public function dispatchShipment(Request $request)
    {
        try {
            $data = $request->only(['shipment_id']);
            $rules = [
                'shipment_id'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $api_key = $request->header('API_KEY');
                
                if($api_key != '1ba6c71c-bfa3-423b-92d4-05097370eb86')
                {
                   
                    return $this->ayenatiResponse(false,'2033','API Key is wrong');
                }

                $shipment = Shipment::find($request->shipment_id);
                if($shipment == null)
                {
                    return $this->ayenatiResponse(false,'2022','Illegal status update request');
                }


                return $this->ayenatiResponse(true,'0','');

            }
        } catch (Exception $e) {
            return $this->ayenatiResponse(false,'2033','system error');
        }
    }

    public function getShipmentById1(Request $request)
    {
        try {
            $data = $request->only(['shipment_id']);
            $rules = [
                'shipment_id'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // $api_key = $request->header('API_KEY');
                // if($api_key != '1ba6c71c-bfa3-423b-92d4-05097370eb86')
                // {
                //     return $this->ayenatiResponse(false,'2033','API Key is wrong');
                // }

                $shipment = Shipment::find($request->shipment_id);
                if($shipment == null)
                {
                    return $this->response(false,'2022');
                }
                return $this->response(true,'success',$shipment);

            }
        } catch (Exception $e) {
            return $this->ayenatiResponse(false,'2033','system error');
        }
    }



    public function getShipmentById(Request $request)
    {
        try {
            $data = $request->only(['status_code','shipment_id']);
            $rules = [
                // 'username'   => 'required',
                // 'password'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $shipment_id = $request->shipment_id;
                $shipment = Shipment::find($shipment_id);
                $task = $shipment->task;
                $driver = $task->driver;
                $data =  parent::updateNotification($shipment_id, $driver->name, $driver->name, $driver->national_id, $driver->mobile, $request->status_code);
               
               
               
                $data =  parent::dropoff($shipment_id, '1234', 'delivered');

                // $client = new \GuzzleHttp\Client();
                // $response = $client->post('https://api-test.lean.sa/oauth/token', [
                //     'headers' => [
                //         'Authorization' => 'Basic bUZTTk5sMUN6TzB4QUZLRXhua2IxV3NtZHZDYTZKOEQ6ampuRHJiU2M0RUlSS0lrZw==',
                //         'Content-Type' => 'application/x-www-form-urlencoded'
                //     ],
                //     'form_params' => [
                //         'grant_type' => 'client_credentials',
                //     ]
                // ]);

                // $data = json_decode( $response->getBody()->getContents(), true);
                // $response = $client->post('https://api-test.lean.sa/p-ayenati/notifications/updateNotificationDetails', [
                //     'headers' => [
                //         'Authorization' => 'Bearer '.$data['access_token'],
                //         'Content-Type' => 'application/json'
                //     ],
                //     'json' => [
                //         'shipment_id' => 15,
                //         'agent_first_name' => 'Naif',
                //         'agent_last_name' => 'Muneif',
                //         'agent_national_id' => '1000000000',
                //         'agent_mobile' => '0546059444',
                //         'status_code' => 'dispatched',
                //         'timestamp' => Carbon::now(),
                //         'track_url' => 'https://www.gosample.com',
                //     ]
                // ]);
                // $data = json_decode( $response->getBody()->getContents(), true);
                return $this->response(true,'success',$data);

            }
        } catch (Exception $e) {
            return $this->ayenatiResponse(false,'2033','system error');
        }
    }

    public function token(Request $request)
    {
        try {
            $data = $request->only(['username','password']);
            $rules = [
                // 'username'   => 'required',
                // 'password'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $client = new \GuzzleHttp\Client();
                $response = $client->post('https://api-test.lean.sa/oauth/token', [
                    'headers' => [
                        'Authorization' => 'Basic bUZTTk5sMUN6TzB4QUZLRXhua2IxV3NtZHZDYTZKOEQ6ampuRHJiU2M0RUlSS0lrZw==',
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                    ]
                ]);

                $data = json_decode( $response->getBody()->getContents(), true);
                return $this->response(true,'success',$data);

            }
        } catch (Exception $e) {
            return $this->ayenatiResponse(false,'2033','system error');
        }
    }

    public function updateNotificationDetails(Request $request)
    {
        try {
            $data = $request->only(['token','password']);
            $rules = [
                'token'   => 'required',
                // 'password'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $client = new \GuzzleHttp\Client();
                $response = $client->post('https://api-test.lean.sa/p-ayenati/notifications/updateNotificationDetails', [
                    'headers' => [
                        'Authorization' => 'Basic bUZTTk5sMUN6TzB4QUZLRXhua2IxV3NtZHZDYTZKOEQ6ampuRHJiU2M0RUlSS0lrZw==',
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'json' => [
                        'shipment_id' => 22765,
                        'agent_first_name' => 'Naif',
                        'agent_last_name' => 'Muneif',
                        'agent_national_id' => '1000000000',
                        'agent_mobile' => '0546059444',
                        'status_code' => 'confirmed',
                        'timestamp' => '2021-04-21 17:27:41',
                        'track_url' => 'https://www.gosample.com',
                    ]
                ]);

                $data = json_decode( $response->getBody()->getContents(), true);
                return $this->response(true,'success',$data);

            }
        } catch (Exception $e) {
            return $this->ayenatiResponse(false,'2033','system error');
        }
    }

    
}
