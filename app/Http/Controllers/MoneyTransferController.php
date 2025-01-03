<?php

namespace App\Http\Controllers;

use App\Events\DriverArrivedAtDeliveredLocationEvent;
use App\Events\DriverArrivedAtPickUpLocationEvent;
use App\Events\TaskCancelledEvent;
use App\Events\SamplesCollectedEvent;
use App\Events\TaskClosedEvent;
use App\Listeners\SendDriverArrivedAtDeliveredLocationEvent;
use App\Models\Client;
use App\Models\Container;
use App\Models\Attendance;
use App\Models\Driver;
use App\Models\Sample;
use App\Models\Location;
use App\Models\MoneyTransfer;
use App\Models\Task;
use App\Notifications\DriverAtDestinationLocation;
use App\Notifications\DriverAtSourceLocation;
use App\Notifications\SamplesCollected;
use App\Notifications\SamplesInFreezer;
use App\Notifications\SamplesOutFreezer;
use App\Notifications\TaskClosed;
use App\Notifications\TaskClosedWithoutSamples;
use App\Notifications\TaskDelayed;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use MediaUploader;
use Notification;

class MoneyTransferController extends Controller
{
    
    public function listPerDriver(Request $request)
    {
        try {
            $data = $request->only(['driver_id']);
            $rules = [
                'driver_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // $tasks = MoneyTransfer::where('driver_id',$request->driver_id)
                // ->whereIn('status',['new','confirmed'])
                // ->get();


                $tasks = MoneyTransfer::
                
                leftjoin('locations AS A', 'A.id', '=', 'money_transfers.from_location_id')
                ->leftjoin('locations AS B', 'B.id', '=', 'money_transfers.to_location_id')
                ->leftjoin('clients AS C', 'C.id', '=', 'money_transfers.client_id')
                ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                    'A.lng as from_location_lng','B.lng as to_location_lng',
                    'A.mobile as from_mobile','B.mobile as to_location_mobile',
                    'money_transfers.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                    
                ->where('driver_id',$request->driver_id)
                ->whereIn('money_transfers.status',['new','confirmed'])
                ->get();


                return $this->response(true,'success',$tasks);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function verifyFromOtp(Request $request)
    {
        try {
            $data = $request->only(['task_id','from_location_otp']);
            $rules = [
                'task_id'   => 'required',
                'from_location_otp'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $tasks = MoneyTransfer::where('id',$request->task_id)
                ->where('from_location_otp',$request->from_location_otp)
                ->get();
                if(count($tasks) >0)
                {
                    return $this->response(true,'success','CORRECT_OTP');
                }
                return $this->response(true,'success','WRONG_OTP');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function verifyToOtp(Request $request)
    {
        try {
            $data = $request->only(['task_id','to_location_otp']);
            $rules = [
                'task_id'   => 'required',
                'to_location_otp'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $tasks = MoneyTransfer::where('id',$request->task_id)
                ->where('to_location_otp',$request->to_location_otp)
                ->get();
                if(count($tasks) >0)
                {
                    return $this->response(true,'success','CORRECT_OTP');
                }
                return $this->response(true,'success','WRONG_OTP');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

}
