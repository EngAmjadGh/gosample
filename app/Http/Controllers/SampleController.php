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

class SampleController extends Controller
{
    //
    public function new(Request $request)
    {
        try {
            $data = $request->only(['task_id','barcode_id','location_id','temperature_type']);
            $rules = [
                'task_id'   => 'required',
                'location_id'   => 'required',
                'temperature_type'   => 'required',
                'barcode_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->from_location != $request->location_id)
                    {
                        return $this->response(false,'location is not found');
                    }
                }

                // check if sample is already existed
                $oldSample = Sample::where('barcode_id',$request->barcode_id)->get();
                if($oldSample != null && count($oldSample) > 0)
                {
                    return $this->response(false,'sample already added to task');
                }
                // check location
                $record = new Sample();
                $record->task_id    = $request->task_id;
                $record->location_id         = $request->location_id;
                $record->temperature_type         = $request->temperature_type;
                $record->barcode_id       = $request->barcode_id;
                $record->save();


                return $this->response(true,'success',$record);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function samples(Request $request)
    {
        try {
            $data = $request->only(['task_id','bag_code']);
            $rules = [
                'task_id'   => 'required',
//                'bag_code'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($request->bag_code != null)
                    {
                        $records = Sample::where('task_id',$request->task_id)->where('bag_code',$request->bag_code)->get();
                    } else{
                        $records = Sample::where('task_id',$request->task_id)->get();
                    }
                    return $this->response(true,'success',$records);
                }
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function createTask(Request $request)
    {
        try {
            $data = $request->only(['from_location','to_location','billing_client','cost','status','driver_id']);
            $rules = [
                'cost'   => 'required',
                'driver_id'   => 'required',
                'from_location'   => 'required',
                'to_location'   => 'required',
                'status'   => 'required',
                'billing_client'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $record = new Task();
                $record->from_location    = $request->from_location;
                $record->status         = $request->status;
                $record->cost         = $request->cost;
                $record->driver_id         = $request->driver_id;
                $record->to_location         = $request->to_location;
                $record->billing_client       = $request->billing_client;
                $record->save();


                return $this->response(true,'success',$record);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function noSamples(Request $request)
    {
        try {
            $data = $request->only(['task_id']);
            $rules = [
                'task_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    $driver = Driver::find($task->driver_id);
                    $task->status = 'NO_SAMPLES';
                    $task->save();

                    if($task->takasi == '$task' || $task->billing_client == 42 || $task->billing_client == 33)
                    {
                        event (new TaskCancelledEvent($task));
                    }
                    
                    Notification::send($driver, new TaskClosedWithoutSamples($task));
                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
    public function collect(Request $request)
    {
        try {
            $data = $request->only(['task_id','image','confirmationCode','box_count','sample_count']);
            $rules = [
                'task_id'   => 'required',
//                'image'   => 'required',
//                'confirmationCode'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->status != 'NEW' && $task->status != 'NO_SAMPLES')
                    {
                        return $this->response(false,'task status is not valid');
                    }


                    if($task->task_type == 'BOX')
                    {
                        if($request->box_count != null)
                        {
                            $task->box_count = $request->box_count;
                            // should update sample to add box_count & sample_count
                        }
                        else{
                            return $this->response(false,'box count  is required');
                        }
                        if($request->sample_count != null)
                        {
                            $task->sample_count = $request->sample_count;
                        }
                        else{
                            return $this->response(false,'sample count  is required');
                        }
                    }

                    // get driver
                    $driver = Driver::find($task->driver_id);
                    // save location of driver
                    $lat = $driver->lat;
                    $lng = $driver->lng;
                    $from_location = Location::find($task->from_location);
                    $distance = parent::distance($lat, $lng, $from_location->lat, $from_location->lng, "K");
                    // if($driver->id != 54)
                    // {
                        if($distance > 0.4 )
                        {
                            return $this->response(false,'you cannot close task in this location');
                        }
                    // }
                    

                    // save time to attendance of driver
                    $attendance =  Attendance::where('driver_id',$driver->id)
                        ->whereDate('created_at', Carbon::today())
                        ->first();
                    if($attendance == null)
                    {
                        $attendance = new Attendance();
                        $attendance->checkin_time = Carbon::now();
                        $attendance->driver_id = $driver->id;
                        $attendance->save();
                    }



                    if($task->status != 'NO_SAMPLES')
                    {
                        $task->status = 'COLLECTED';
                    }


                    // if($request->image != null){
                    //     $media = MediaUploader::fromSource($request->file('image'))
                    //         ->toDestination('uploads', 'signature-images')
                    //         ->useHashForFilename()
                    //         ->upload();

                    //     $task->signature = '/'.$media->directory .'/'.$media->filename.'.'.$media->extension;

                    // }
                    // if($request->confirmationCode != null)
                    // {
                    //     // we have to compare here
                    //     $task->confirmationCode = $request->confirmationCode;
                    // }

                    $task->collection_date = date('Y-m-d H:i:s');
                    $task->collect_lat = $lat;
                    $task->collect_lng = $lng;
                    $task->save();
                    if($task->takasi == '$task' || $task->billing_client == 42 || $task->billing_client == 33)
                    {
                        event (new SamplesCollectedEvent($task));
                    }
                   
                    Notification::send($driver, new SamplesCollected($task));
                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function freezer(Request $request)
    {
        try {
            $data = $request->only(['task_id']);
            $rules = [
                'task_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->status != 'COLLECTED')
                    {
                        return $this->response(false,'task status is not valid');
                    }
                    // check if driver added all bags to freezers
                    $sample = Sample::where('task_id',$request->task_id)
                        ->whereNull('container_id')->get();
                    if(count($sample)  > 0 ){
                        return $this->response(false,'please add all bags');
                    }


                    $driver = Driver::find($task->driver_id);
                    $task->status = 'IN_FREEZER';
                    $task->freezer_date = date('Y-m-d H:i:s');
                    $task->save();
                    Notification::send($driver, new SamplesInFreezer($task));
                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function getContainersPerBag(Request $request)
    {
        try {
            $data = $request->only(['task_id','bag_code','car_id']);
            $rules = [
                'bag_code'   => 'required',
                'task_id'   => 'required',
//                'car_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    $sample = Sample::where('task_id',$request->task_id)->where('bag_code',$request->bag_code)->first();

                    if($sample == null)
                    {
                        return $this->response(false,'bag is not found');
                    }
                    $driver = Driver::where('id',$task->driver_id)->with(['car'])
                        ->first();

                    if($driver->car == null)
                    {
                        return $this->response(false,'Please add car to driver');
                    }
                    $containers = $driver->car->containers;
                    foreach ($containers as $container){
                        if($container->type == $sample->temperature_type)
                        {
                            $container->correctContainer = true;
                        }
                        else{
                            $container->correctContainer = false;
                        }
                    }
//                    return ;
                    return $this->response(true,'success',$containers);

                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function freezerOut(Request $request)
    {
        try {
            $data = $request->only(['task_id']);
            $rules = [
                'task_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{

                    if($task->status != 'IN_FREEZER')
                    {
                        return $this->response(false,'task status is not valid');
                    }
                    // check that all bags are removed from container before changing status
                    $sample = Sample::where('task_id',$request->task_id)
                        ->whereNotNull('container_id')->get();
                   if(count($sample)  > 0 ){
                       return $this->response(false,'please remove all bags');
                   }
                    $driver = Driver::find($task->driver_id);
                    $task->status = 'OUT_FREEZER';
                    $task->freezer_out_date = date('Y-m-d H:i:s');
                    $task->save();
                    Notification::send($driver, new SamplesOutFreezer($task));
                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function freezerOutMultipleTasks(Request $request)
    {
        try {
            $data = $request->only(['tasks']);
            $rules = [
                'tasks'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $tasksParam = json_decode($request->tasks, true);
                DB::beginTransaction();
                // check task
                foreach ($tasksParam as $task_id)
                {
                    $task = Task::find($task_id);
                    if($task == null)
                    {
                        return $this->response(false,'task is not found');
                    } else{

                        if($task->status != 'IN_FREEZER')
                        {
                            DB::rollBack();
                            return $this->response(false,'task status is not valid');
                        }
                        // check that all bags are removed from container before changing status
                        $sample = Sample::where('task_id',$task_id)
                            ->whereNotNull('container_id')->get();
                        if(count($sample)  > 0 ){
                            DB::rollBack();
                            return $this->response(false,'please remove all bags');
                        }
                        $driver = Driver::find($task->driver_id);
                        $task->status = 'OUT_FREEZER';
                        $task->freezer_out_date = date('Y-m-d H:i:s');
                        $task->save();
                        Notification::send($driver, new SamplesOutFreezer($task));
                    }
                }
                DB::commit();
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false,'system error');
        }
    }

    public function close(Request $request)
    {
        try {
            $data = $request->only(['task_id','deliver_signature','deliver_confirmationCode']);
            $rules = [
                'task_id'   => 'required',
//                'deliver_signature'   => 'required',
//                'deliver_confirmationCode'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{

                    if($task->status != 'OUT_FREEZER')
                    {
                        return $this->response(false,'task status is not valid');
                    }

                    $driver = Driver::find($task->driver_id);
                    $task->status = 'CLOSED';
                    $ldate = date('Y-m-d H:i:s');
                    $task->close_date = $ldate;
                    // if($request->deliver_signature != null){
                    //     $media = MediaUploader::fromSource($request->file('deliver_signature'))
                    //         ->toDestination('uploads', 'signature-images')
                    //         ->useHashForFilename()
                    //         ->upload();

                    //     $task->deliver_signature = '/'.$media->directory .'/'.$media->filename.'.'.$media->extension;
                    // }
                    // if($request->deliver_confirmationCode != null)
                    // {
                    //     // we have to compare here
                    //     $task->deliver_confirmationCode = $request->deliver_confirmationCode;
                    // }

                    $task->save();
                     Notification::send($driver, new TaskClosed($task));
                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function closeTasks(Request $request)
    {
        try {
            $data = $request->only(['tasks','deliver_signature','deliver_confirmationCode']);
            $rules = [
                'tasks'   => 'required',
//                'deliver_signature'   => 'required',
//                'deliver_confirmationCode'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $tasksParam = json_decode($request->tasks, true);

                $deliver_signature = '';
                $deliver_confirmationCode = '';
                // if($request->deliver_signature != null){
                //     $media = MediaUploader::fromSource($request->file('deliver_signature'))
                //         ->toDestination('uploads', 'signature-images')
                //         ->useHashForFilename()
                //         ->upload();

                //     $deliver_signature = '/'.$media->directory .'/'.$media->filename.'.'.$media->extension;
                // }
                // if($request->deliver_confirmationCode != null)
                // {
                //     // we have to compare here
                //     $deliver_confirmationCode = $request->deliver_confirmationCode;
                // }
                // check task
                $tasks = Task::whereIn('id',$tasksParam)->get();
                if($tasks == null || count($tasks) == 0)
                {
                    return $this->response(false,'tasks are not found');
                } else{
                    $ldate = date('Y-m-d H:i:s');
                    foreach ($tasks as $task){
                        if($task->status != 'OUT_FREEZER')
                        {
                            return $this->response(false,'task status is not valid');
                        }
                        $task->status = 'CLOSED';
                        $task->close_date = $ldate;
                        $task->deliver_signature = $deliver_signature;
                        $task->deliver_confirmationCode = $deliver_confirmationCode;

                        // get driver location and save location in task table
                        $driver = Driver::find($task->driver_id);
                        $lat = $driver->lat;
                        $lng = $driver->lng;

                        $to_location = Location::find($task->to_location);
                        // calculate distance between driver location and to_location

                        $distance = parent::distance($lat, $lng, $to_location->lat, $to_location->lng, "K");
                        // if($driver->id != 54)
                        // {
                            if($distance > 0.2)
                            {
                                return $this->response(false,'you cannot close task in this location');
                            }
                        // }
                        

                        // save time to attendance of driver
                        $attendance =  Attendance::where('driver_id',$driver->id)
                            ->whereDate('created_at', Carbon::today())
                            ->first();
                        if($attendance != null)
                        {
                            $attendance->checkout_time = Carbon::now();
                            $attendance->save();
                        }


                        $task->close_lat = $lat;
                        $task->close_lng = $lng;
                        $task->save();
                        if($task->takasi == '$task' || $task->billing_client == 42 || $task->billing_client == 33)
                        {
                            event (new TaskClosedEvent($task));
                        }
                       
                    }
                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function addSamplesToTask(Request $request)
    {
        try {
            $data = $request->only(['task_id','barcode_ids','location_id','temperature_type','bag_code','sample_type']);
            $rules = [
                'task_id'   => 'required',
                'location_id'   => 'required',
                'temperature_type'   => 'required',
                'sample_type'   => 'required',
                'bag_code'   => 'required',
                'barcode_ids'   => 'required|array',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $location = Location::find($request->location_id);
                if($location == null)
                {
                    return $this->response(false,'location is not found');
                }

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->from_location != $request->location_id)
                    {
                        return $this->response(false,'location is not found');
                    }
                }

                // check if bag_code is already existed
                $oldSample = Sample::where('bag_code',$request->bag_code)->where('task_id',$request->task_id)->get();
                if($oldSample != null && count($oldSample) > 0)
                {
                    return $this->response(false,'bag already added to task');
                }
                DB::beginTransaction();
                $data2 = array();
                foreach ($request->barcode_ids as $barcode_id){
                    $data2[] = array(
                        'task_id'=> $request->task_id,
                        'location_id'=> $request->location_id,
                        'bag_code'=> $request->bag_code,
                        'temperature_type'=> $request->temperature_type,
                        'sample_type'=> $request->sample_type,
                        'barcode_id'=> $barcode_id);
                }
                Sample::insert($data2);
                DB::commit();
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false,'system error');
        }
    }
    public function getBagsByTaskId(Request $request)
    {
        try {
            $taskId = $request->task_id;
    
            // Retrieve bags with the given task ID
            $bags = Sample::where('task_id', $taskId)->get();
            $bagIds = Sample::where('task_id', $taskId)->pluck('barcode_id')->toArray();
    
            if ($bags->isEmpty()) {
                return $this->response(false, 'No bags found for the provided task ID');
            }
    
            $data = [
                'bags' => $bags,
                'bagIds' => $bagIds,
            ];
    
            return $this->response(true, 'success', $data);
        } catch (Exception $e) {
            return $this->response(false, 'system error');
        }
    }

    public function addSamplesToTaskWithBagsArray(Request $request)
    {
        try {
            $dataArray = $request->json()->all(); // Get the array of objects from the request

            DB::beginTransaction();

            foreach ($dataArray as $data) {
                $validator = Validator::make($data, [
                    'task_id' => 'required',
                    'location_id' => 'required',
                    'temperature_type' => 'required',
                    'sample_type' => 'required',
                    'bag_code' => 'required',
                    'barcode_ids' => 'required|array',
                ]);

                if ($validator->fails()) {
                    DB::rollBack();
                    return $this->response(false, $this->validationHandle($validator->messages()));
                }

                $location = Location::find($data['location_id']);
                if ($location == null) {
                    DB::rollBack();
                    return $this->response(false, 'location is not found');
                }

                $task = Task::find($data['task_id']);
                if ($task == null) {
                    DB::rollBack();
                    return $this->response(false, 'task is not found');
                } else {
                    if ($task->from_location != $data['location_id']) {
                        DB::rollBack();
                        return $this->response(false, 'location is not found');
                    }
                }

                $oldSample = Sample::where('bag_code', $data['bag_code'])->where('task_id', $data['task_id'])->get();
                if ($oldSample != null && count($oldSample) > 0) {
                    DB::rollBack();
                    return $this->response(false, 'bag already added to task');
                }

                // Insert the new sample data
                Sample::create([
                    'task_id' => $data['task_id'],
                    'location_id' => $data['location_id'],
                    'bag_code' => $data['bag_code'],
                    'temperature_type' => $data['temperature_type'],
                    'sample_type' => $data['sample_type'],
                    'barcode_id' => $data['barcode_ids'][0], // Assuming only one barcode_id for each object
                ]);
            }

            DB::commit();
            return $this->response(true, 'success');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false, 'system error');
        }
    }


    public function addSamplesToPerBoxTask(Request $request)
    {
        try {
            $data = $request->only(['task_id','barcode_ids','location_id','temperature_type','sample_type','box_count','sample_count']);
            $rules = [
                'task_id'   => 'required',
                'location_id'   => 'required',
                'temperature_type'   => 'required',
                'sample_type'   => 'required',
//                'bag_code'   => 'required',
                'barcode_ids'   => 'required|array',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->from_location != $request->location_id)
                    {
                        return $this->response(false,'location is not found');
                    }

                    if($task->task_type != 'BOX')
                    {
                        return $this->response(false,'task type is not correct');
                    }
                }

                // check if bag_code is already existed
                $oldSample = Sample::whereIn('bag_code',$request->barcode_ids)->where('task_id',$request->task_id)->get();
                if($oldSample != null && count($oldSample) > 0)
                {
                    return $this->response(false,'bag already added to task');
                }
                DB::beginTransaction();
                $data2 = array();
                foreach ($request->barcode_ids as $barcode_id){
                    $data2[] = array(
                        'task_id'=> $request->task_id,
                        'location_id'=> $request->location_id,
                        'bag_code'=> $barcode_id,
                        'temperature_type'=> $request->temperature_type,
                        'sample_type'=> $request->sample_type,
                        'box_count'=> $request->box_count,
                        'sample_count'=> $request->sample_count,
                        'barcode_id'=> $barcode_id);
                }
                Sample::insert($data2);
                DB::commit();
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false,'system error');
        }
    }

    public function getBagSamples(Request $request)
    {
        try {
            $data = $request->only(['task_id','bag_code']);
            $rules = [
                'task_id'   => 'required',
                'bag_code'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }
                $samples = Sample::where('bag_code',$request->bag_code)->where('task_id',$request->task_id)->get();
                return $this->response(true,'success',$samples);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
    public function getBagSamplesWithType(Request $request)
    {
        try {
            $data = $request->only(['task_id','bag_code','container_type']);
            $rules = [
                'task_id'   => 'required',
                'bag_code'   => 'required',
                'container_type'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }
                $samples = Sample::where('bag_code',$request->bag_code)->where('task_id',$request->task_id)->get();
//                return count($samples);
//                if($samples->first() == null )
//                {
//                    $this->response(true,'no samples in this bag');
//                }
//                if( $samples[0]->type != $request->container_type)
//                {
//                    $this->response(false,'container type is not correct');
//                }
                return $this->response(true,'success',$samples);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function addSampleToContainer(Request $request)
    {
        try {
            $data = $request->only(['task_id','barcode_id','container_id']);
            $rules = [
                'task_id'   => 'required',
                'barcode_id'   => 'required',
                'container_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }


                // check task
                $container = Container::find($request->container_id);
                if($container == null)
                {
                    return $this->response(false,'container is not found');
                }

                // check if sample is already existed
                $oldSample = Sample::where('barcode_id',$request->barcode_id)->where('task_id',$request->task_id)->first();
                if($oldSample == null )
                {
                    return $this->response(false,'sample is not existed');
                } else{
                    if($container->type != $oldSample->type)
                    {
                        return $this->response(false,'sample type is not equal to container type');
                    }

                }
                // check location
                $record = $oldSample;
                $record->container_id    = $request->container_id;
                $record->save();
                return $this->response(true,'success',$record);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }


    public function addSamplesToContainer(Request $request)
    {
        try {
            $data = $request->only(['task_id','bag_code','container_id']);
            $rules = [
                'task_id'   => 'required',
                'bag_code'   => 'required',
                'container_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }


                // check task
                $container = Container::find($request->container_id);
                if($container == null)
                {
                    return $this->response(false,'container is not found');
                }

                // check if sample is already existed
                $samples = Sample::where('bag_code',$request->bag_code)->where('task_id',$request->task_id)->get();
//                    ->update(['container_id'=>$request->container_id]);

                // check type of bag and container
                if($container->type != $samples[0]->temperature_type)
                {
                    return $this->response(false,'container type is not same of sample type');
                }

                foreach ($samples as $sample){
                    $sample->container_id = $request->container_id;
                    $sample->save();
                }


                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function addSamplesToContainerWithMultipleBags(Request $request)
    {
        try {
          
          
            
            $dataArray = $request->json()->all(); // Get the array of objects from the request

            foreach ($dataArray as $data) {
                $validator = Validator::make($data, [
                    'task_id' => 'required',
                    'bag_code' => 'required',
                    'container_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->response(false, $this->validationHandle($validator->messages()));
                }

                $task = Task::find($data['task_id']);
                if ($task == null) {
                    return $this->response(false, 'task is not found');
                }

                $driver = Driver::find($task->driver_id);
                $car = $driver->car;
                $driver_containers = $car->containers->pluck('id')->toArray();
                \Log::info($driver);
                \Log::info($car);
                \Log::info($driver_containers);
              
               // Check container
               $container_id = preg_replace('/[^0-9]/', '', $data['container_id']);

               $container = Container::find($container_id);
               \Log::info($container_id);
               if ($container == null) {
                   return $this->response(false, 'container is not found');
               }

               if (!in_array($container_id, $driver_containers)) {
                    return $this->response(false, 'container is not related to driver');
                }
                

                // Check if sample is already existed
                $samples = Sample::where('bag_code', $data['bag_code'])
                    ->where('task_id', $data['task_id'])
                    ->get();

                // Check type of bag and container
                foreach ($samples as $sample) {
                    if ($container->type != $sample->temperature_type) {
                        return $this->response(false, 'container type is not the same as sample type');
                    }
                    $sample->container_id =$container_id;
                    $sample->save();
                }
            }

            return $this->response(true, 'success');
        } catch (Exception $e) {
            return $this->response(false, 'system error');
        }
    }


    public function removeSampleToContainer(Request $request)
    {
        try {
            $data = $request->only(['task_id','barcode_id','container_id']);
            $rules = [
                'task_id'   => 'required',
                'barcode_id'   => 'required',
                'container_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }

                // check if sample is already existed
                $oldSample = Sample::where('barcode_id',$request->barcode_id)->where('task_id',$request->task_id)->first();
                if($oldSample == null )
                {
                    return $this->response(false,'sample is not existed');
                }
                // check location
                $record = $oldSample;
                $record->container_id    = null;
                $record->save();
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function removeBagFromContainer(Request $request)
    {
        try {
            $data = $request->only(['task_id','bag_code','container_id']);
            $rules = [
                'task_id'   => 'required',
                'bag_code'   => 'required',
                'container_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }

                Sample::where('task_id',$request->task_id)->where('container_id',$request->container_id)->where('bag_code',$request->bag_code)->update(['container_id'=> null]);
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function removeSampleFromTask(Request $request)
    {
        try {
            $data = $request->only(['task_id','barcode_id']);
            $rules = [
                'task_id'   => 'required',
                'barcode_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }
                // check if sample is already existed
                $oldSample = Sample::where('barcode_id',$request->barcode_id)->delete();
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function checkLocationBarcode(Request $request)
    {
        try {
            $data = $request->only(['task_id','location_type','location_id','takasi_number']);
            $rules = [
                'task_id'   => 'required',
                'location_type'   => 'required',
                'location_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }
                // return $this->response(false,'cannot collect in this location');

                $driver = Driver::find($task->driver_id);
                $lat = $driver->lat;
                $lng = $driver->lng;
                $from_location = Location::find($task->from_location);

                $distance = parent::distance($lat, $lng, $from_location->lat, $from_location->lng, "K");
                
                // if($driver->id != 54)
                // {
                    if($distance > 0.5)
                    {
                        \Log::info($driver);
                        \Log::info('--------');
                        \Log::info($distance);

                        \Log::info($from_location->lat);
                        \Log::info($from_location->lng);
                        \Log::info('--------');
                        return $this->response(false,'cannot collect in this location');
                    }
                // }

                
                if($request->location_type =='FROM' && $request->location_id == $task->from_location)
                {
                    $task->from_location_arrival_time = now();
                    $task->takasi_number = $request->takasi_number;
                    $task->save();
                    if($task->takasi == 'YES' || $task->billing_client == 42 || $task->billing_client == 33 )
                    {
                        event (new DriverArrivedAtPickUpLocationEvent($task));
                    } 

                    $driver = Driver::find( $task->driver_id);
                    // send notification to system that driver reach source location (fromLocation)
                    Notification::send($driver, new DriverAtSourceLocation($task));
                    return $this->response(true,'success');
                } else{
                    if($request->location_type =='TO' && $request->location_id == $task->to_location)
                    {
                        $task->to_location_arrival_time = now();
                        $task->to_takasi_number = $request->takasi_number;
                        $task->save();
                        if($task->takasi == 'YES' || $task->billing_client == 42 || $task->billing_client == 33)
                        {
                            event (new SendDriverArrivedAtDeliveredLocationEvent($task));
                        }
                        $driver = Driver::find( $task->driver_id);
                        // send notification to system that driver reach source location (fromLocation)
                        Notification::send($driver, new DriverAtDestinationLocation($task));
                        return $this->response(true,'success');
                    } else{
                        return $this->response(false,'failed');
                    }
                }


            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function checkLocationBarcodeMultipleTasks(Request $request)
    {
        try {
            $data = $request->only(['tasks','to_location','takasi_number']);
            $rules = [
                'tasks'   => 'required',
                'to_location'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // handle received tasks as array or string
                if(is_array($request->tasks))
                {
                    $tasksParam = $request->tasks;
                }else{
                    $tasksParam = json_decode($request->tasks, true);
                }
                if(sizeof($tasksParam) == 0)
                {
                    return $this->response(false,'task is not found');
                }


                $location= Location::find($request->to_location);
                if($location == null)
                {
                    return $this->response(false,'location is not found');
                }


                // check if location of task is equal to sent location
                $firstTask = Task::find($tasksParam[0]);
                if($firstTask->to_location != $request->to_location){
                    return $this->response(false,'location is not valid');
                }


                Task::whereIn('id',$tasksParam)
                    ->update([
                        'to_location_arrival_time' => now(),
                        'to_takasi_number' =>  $request->takasi_number
                    ]);

                // get tasks
                $tasks = Task::whereIn('id',$tasksParam)->get();
                foreach ($tasks as $task)
                {
                    // if($task->takasi == '$task' || $task->billing_client == 42 || $task->billing_client == 33)
                    // {
                    //     event (new DriverArrivedAtDeliveredLocationEvent($task));
                    // }
                    $driver = Driver::find( $task->driver_id);
                    // send notification to system that driver reach source location (fromLocation)
                    Notification::send($driver, new DriverAtSourceLocation($task));
//                    $task->to_takasi_number = $request->takasi_number;

                }
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function getBagsOfContainer(Request $request)
    {
        try {
            $data = $request->only(['task_id','container_id']);
            $rules = [
                'task_id'   => 'required',
                'container_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                }

                $container = Container::find($request->container_id);
                if($container == null)
                {
                    return $this->response(false,'container is not found');
                }

                $samples = Sample::select('bag_code','temperature_type')->where('task_id',$request->task_id)
                    ->where('container_id',$request->container_id)
                    ->where('temperature_type',$container->type)
                    ->groupBy('bag_code','temperature_type')->get();

                return $this->response(true,'success',$samples);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
    public function checkSample(Request $request)
    {
        try {
            $data = $request->only(['task_id','sample_id','confirmed_by']);
            $rules = [
                // 'task_id'   => 'required',
                'sample_id'   => 'required',
//                'confirmed_by'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
//                $task = Task::find($request->task_id);
//                if($task == null)
//                {
//                    return $this->response(false,'task is not found');
//                } else{
//                    if($task->status != 'CLOSED')
//                    {
//                        return $this->response(false,'task status is not closed from driver');
//                    }
//
//
//                }

                // check sample
                $sample = Sample::where('barcode_id',$request->sample_id)->get();
                if(count($sample) > 0)
                {
                    foreach ($sample as $row){
                        $row->confirmed_by_client = 'YES';
                        $row->confirmed_by = $request->confirmed_by;
                        $row->save();
                    }

                    Task::where('id',$sample[0]->task_id)
                        ->update(
                            [
                                'confirmed_by_client' => 'YES',
                                'confirmation_time' => Carbon::now()->toDateTimeString(),
                            ]);
                    return $this->response(true,'success');
                }
                else{
                    return $this->response(false,'sample is not under this task');
                }
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function checkSamples(Request $request)
    {
        try {
            $data = $request->only(['task_id','sample_ids']);
            $rules = [
                'task_id'   => 'required',
                'sample_ids'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->status != 'CLOSED')
                    {
                        return $this->response(false,'task status is not closed from driver');
                    }

                    // check sample
                    $samples = Sample::where('task_id',$request->task_id)->pluck('barcode_id');
                    // compare 2 arrays, received and from db
                    $results=array_diff($samples->toArray(),$request->sample_ids);

                    if(count($results) == 0)
                    {
                        //  mark task as confirmed by client
                        $task->confirmed_by_client = 'YES';
                        $task->confirmation_time = Carbon::now()->toDateTimeString();
                        $task->save();

                        return $this->response(true,'success');
                    }
                    else{
                        return $this->response(false,'sample is not under this task');
                    }
                }
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
    public function getConfirmedSamples(Request $request)
    {
        try {
            $data = $request->only(['task_id']);
            $rules = [
                'task_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // check task
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
                    if($task->status != 'CLOSED')
                    {
                        return $this->response(false,'task status is not closed from driver');
                    }

                    // check sample
                    $samples = Sample::leftJoin('tasks','tasks.id','=','samples.task_id')
                        ->where('samples.confirmed_by_client','NO')
                        ->where('tasks.driver_id',$task->driver_id)
                        ->where('tasks.billing_client',$task->billing_client)->get();
                    return $this->response(true,'success',$samples);
                }

            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function getConfirmedSamplesPerDriverId(Request $request)
    {
        try {
            $data = $request->only(['driver_id','task_id','location_id']);
            $rules = [
                'task_id'   => 'required',
                'driver_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // return $request;
                // check task
                $task = Task::find($request->task_id);
                \Log::info($task);
                if($task == null)
                {
                    return $this->response(false,'task is not found');
                } else{
////                    return $task;
//                    if($task->status != 'CLOSED')
//                    {
//                        return $this->response(false,'task status is not closed from driver');
//                    }

                    // get last task of this dirver

                    $lastestTask = Task::where('driver_id',$request->driver_id)
                        ->where('status','CLOSED')

                        ->orderBy('id','desc')->first();

                    \Log::info($lastestTask);
                    $to_location = null;
                    if($lastestTask  == null)
                    {
                        return $this->response(false,'no task available');
                    }
                    $billing_client= $lastestTask->billing_client;
                    if($request->location_id == null)
                    {
                        $to_location = $lastestTask->to_location;
                    } else {
                        $billing_client = DB::table('client_location')
                            ->select('client_id')
                            ->where('location_id',  $request->location_id)
                            ->first()->client_id;
                        $to_location = $request->location_id;
                    }
                    \Log::info($to_location);
                    \Log::info($billing_client);
                    \Log::info($request->driver_id);
                    if($lastestTask != null)
                    {
                        // check sample
                        $samples = Sample::leftJoin('tasks','tasks.id','=','samples.task_id')
                            ->where('samples.confirmed_by_client','NO')
                            ->where('tasks.driver_id',$request->driver_id)
                            ->where('tasks.to_location',$to_location)
                            ->where('tasks.billing_client',$billing_client)
                            ->select('samples.id','barcode_id')->get();

                        \Log::info(  Sample::leftJoin('tasks','tasks.id','=','samples.task_id')
                        ->where('samples.confirmed_by_client','NO')
                        ->where('tasks.driver_id',$request->driver_id)
                        ->where('tasks.to_location',$to_location)
                        ->where('tasks.billing_client',$billing_client)
                        ->select('samples.id','barcode_id')->toSql());
                        return $this->response(true,'success',$samples);
                    }



                    // check sample
//                    $samples = Sample::leftJoin('tasks','tasks.id','=','samples.task_id')
//                        ->where('samples.confirmed_by_client','NO')
//                        ->where('tasks.driver_id',$request->driver_id)
//                        ->where('tasks.to_location',$task->to_location)
//                        ->where('tasks.billing_client',$task->billing_client)->get();
//                    return $this->response(true,'success',$samples);
                }



//                    $samples = Sample::where('task_id',$request->task_id)->where('confirmed_by_client','NO')->get();

            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }


    public function confirmSamples(Request $request)
    {
        try {
            $data = $request->only(['samples','confirmed_by']);
            $rules = [
                'samples'   => 'required',
//                'confirmed_by'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                   // mark task as confirmed
                   $sample = $request->samples[0];
                   $taskId = Sample::where('barcode_id',$sample)->select('task_id')->first();


           

             
                if($taskId != null)
                {
                    Sample::whereIn('barcode_id',$request->samples)
                    ->update([
                        'confirmed_by_client' => 'YES',
                        'confirmed_by' => $request->confirmed_by ]);

                    Task::where('id',$taskId->task_id)
                    ->update(
                        [
                            'confirmed_by_client' => 'YES',
                            'confirmation_time' => Carbon::now()->toDateTimeString(),
                        ]);
                        return $this->response(true,'success');
                } else{

                    return $this->response(false,'sample not found');
                }
               
               
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function getSampleDetails(Request $request)
    {
        try {
            $data = $request->only(['sample','username']);
            $rules = [
                'sample'   => 'required',
                'username'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $sample = Sample::where('barcode_id',$request->sample)->get();
                if($sample == null || count($sample) == 0   )
                {
                    return $this->response(false,'success','sample is not found');
                }
                else{
                    \Log::info($sample);
                    return $this->response(true,'success',$sample[0]);
                }
//                return $this->response(true,'success',$sample);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function markSamplesAsLost(Request $request)
    {
        try {
            $data = $request->only(['sample','marked_by']);
            $rules = [
                'sample'   => 'required',
                'marked_by'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $sample = Sample::where('barcode_id',$request->sample)->first();
                if($sample == null)
                {
                    return $this->response(false,'sample not found');
                } else{
                    if($sample->confirmed_by_client == 'YES')
                    {
                        return $this->response(false,'sample already confirmed by '.$sample->confirmed_by);
                    }
                    $sample->confirmed_by_client = 'LOST';
                    $sample->confirmed_by = $request->marked_by;
                    $sample->save();
                    return $this->response(true,'success');
                }

            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
    public function confirmAll(Request $request)
    {
        try {
            // return $request;
            $data = $request->only(['driver_id','to_location']);
            $rules = [
                'driver_id'   => 'required',
                'to_location'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $location = Location::find($request->to_location);
                if($location == null)
                {
                    return $this->response(false,'location is not correct');
                }

                $driver = Driver::find($request->driver_id);
                if($driver == null)
                {
                    return $this->response(false,'driver is not correct');
                }

                $billing_client = DB::table('client_location')
                ->select('client_id')
                ->where('location_id',  $request->to_location)
                ->first()->client_id;
                $to_location = $request->to_location;
                // check sample
                $result= $samples = Sample::leftJoin('tasks','tasks.id','=','samples.task_id')
                    ->where('samples.confirmed_by_client','NO')
                    ->where('tasks.driver_id',$request->driver_id)
                    ->where('tasks.to_location',$to_location)
                    // ->where('tasks.billing_client',$billing_client)
                    ->update(['samples.confirmed_by_client' => 'YES']);
                // return $this->response(true,'success',$samples);


            //    $result= Sample::whereHas('task', function ($query) use ($driver, $location) {
            //         $query->where('driver_id', $driver->id)
            //               ->where('to_location', $location->id)
            //               ->where('confirmed_by_client', 'NO');
            //     })->update(['confirmed_by_client' => 'YES']);

                return $this->response(true,'success', $result);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }



    public function generateReport(Request $request){



//        if($this->billing_client){
//            $clint = Client::find($this->billing_client);
//            $client_logo = $clint->logo;
//            // dd($client_logo );
//        }else{
//            $client_logo = null;
//        }
        $query = 'select  tasks.id as id  ,  from_location.name as "from_organization_name",  tasks.from_location_arrival_time as "from_location_arrival_time",freezer_date,
                                    close_date,TIMESTAMPDIFF(Minute, tasks.from_location_arrival_time,  tasks.collection_date) as "from_stay_time",
                                    to_location.name as "to_organization_name",
                                    drivers.name as "driverName",
                                    tasks.created_at,
                                    clients.english_name as "clientName",
                                    tasks.to_location_arrival_time as "to_location_arrival_time",
                                    TIMESTAMPDIFF(Minute, tasks.collection_date, tasks.to_location_arrival_time) as "to_stay_time",
                                    TIMESTAMPDIFF(Minute,  tasks.from_location_arrival_time, tasks.close_date) as "trip_duration",
                                    GROUP_CONCAT(samples.bag_code) as "bag_code",
                                    GROUP_CONCAT(samples.barcode_id) as "sample_code",
                                    GROUP_CONCAT(samples.sample_type) as "sample_types",

                                    GROUP_CONCAT(samples.temperature_type) as "temperature_type",
                                    count(samples.id) as "bags_count"
                                    from tasks
                                    left join drivers on drivers.ID = tasks.driver_id
                                    left join clients on clients.ID = tasks.billing_client
                                    left join locations as from_location on from_location.ID = tasks.from_location
                                    left join locations as to_location on to_location.ID = tasks.to_location
                                    left join samples as samples on samples.task_id = tasks.id
                                    WHERE  DATE(tasks.created_at) = SUBDATE(CURDATE(),1) ';

//        if($this->billing_client !=null)
//        {
//            $query =  $query.' and tasks.billing_client= '.$this->billing_client;
//        }
//
//        if($this->from !=null && $this->to !=null )
//        {
//            $query =  $query." and tasks.created_at BETWEEN '".date('Y-m-d H:i:s', strtotime( $from))."' and '".date('Y-m-d H:i:s', strtotime( $to))." '";
//        }
//
//        if($this->status !=null)
//        {
//            $query =  $query." and tasks.status= '".$this->status."'";
//        }

        $tasks = DB::select($query.' group by tasks.id;');
        $roomBags = 0;
        $refBags = 0;
        $frozenBags = 0;

        $roomSamples = 0;
        $refSamples = 0;
        $frozenSamples = 0;

        $summaryReport = collect($tasks)
            ->groupBy('from_organization_name')
            ->map(function ($task) {
                return [
                    'trip_duration' => $task->sum('trip_duration'),
                    'count' => $task->count(),
                ];
            });
//return json_decode($summaryReport, true);

        foreach ($tasks as $task)
        {
            $task->box_count = 0;
            $task->from_stay_time = floor($task->from_stay_time / 60).'H:'.($task->from_stay_time -   floor($task->from_stay_time / 60) * 60).'M';
            $task->to_stay_time = floor($task->to_stay_time / 60).'H:'.($task->to_stay_time -   floor($task->to_stay_time / 60) * 60).'M';
            if($task->bag_code == null)
            {
                $task->temperature_types2 = array();
                $task->data = array();
                $task->sample_codes = array();
                $task->bags = array();
            }else{
                $task->sample_codes = explode(',', $task->sample_code);
                $task->bags2 = array_count_values (explode(',', $task->bag_code));
                $task->bags = array_unique (explode(',', $task->bag_code));
                $task->temperature_types2 = array_count_values (explode(',', $task->temperature_type));
                $task->temperature_types = array_unique (explode(',', $task->temperature_type));
                $tempVar = json_decode(json_encode($task->temperature_types2),true);
                $bagVar = json_decode(json_encode($task->bags2),true);
                $task->data = array();
                foreach ($tempVar as $key => $value){

                    $temp = new Task();
                    $temp->temperature  =$key;
                    $temp->count  =$value;
                    foreach ($bagVar as $key1 => $value1) {
                        if($value == $value1)
                        {
                            $temp->bag  =$key1;
                            break;
                        }
                    }
                    $task->data[]=$temp;
                    switch ($key)
                    {
                        case 'ROOM':
                            $roomBags += 1;
                            $roomSamples += $value;
                            break;
                        case 'REFRIGERATE':
                            $refSamples += $value;
                            $refBags += 1;
                            break;
                        case 'FROZEN':
                            $frozenSamples += $value;
                            $frozenBags += 1;
                            break;
                    }
                }
            }

        }

        return $tasks;

    }

    public function report(Request $request)
    {
        try {
            $data = $request->only(['from','to']);
            $rules = [
                'from' => 'required',
                'to' => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $from = date($request->from);
                $to = date($request->to);

                \Log::alert($from);
                \Log::alert($to);
                $data = Sample::select('samples.temperature_type',DB::raw('count(*) as total'))
                // ->whereBetween('created_at', [$from, $to])
                ->groupby('temperature_type')
                ->orderBy('total','desc')
                ->get();
                $results = new Sample();
                $labels = $data->pluck('temperature_type');
                $values = $data->pluck('total');
                $results->labels = $labels;
                $results->values = $values;
                return $this->response(true,'success',$results);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
            
    }

    public function confirmTasksByDriver(Request $request)
{
    $taskIds = $request->input('task_ids');

    if (!is_array($taskIds) || empty($taskIds)) {
        return $this->response(false, 'No task IDs provided');
    }

    // Find and confirm each task
    foreach ($taskIds as $taskId) {
        $task = Task::find($taskId);

        if ($task == null) {
            return $this->response(false, 'Task not found');
        }

        // Perform the task confirmation logic here
        $task->confirmed_received_by_driver = true;
        $task->task_confirmation_timestamp = now();
        $task->save();
    }

    return $this->response(true, 'Tasks confirmed successfully');
}


    public function confirmTaskByDriver(Request $request)
    {
        $task = Task::find($request->task_id);
        if($task == null)
        {
            return $this->response(false,'task not found');
        }
        // Perform the task confirmation logic here
        $task->confirmed_received_by_driver = true;
        $task->task_confirmation_timestamp = now();
        $task->save();

        $driver = Driver::find($task->driver_id);
        $driver->sendNotification( 'Task acceptence', 'Task accepted successfully',[$driver->fcm_token],$task,'no_action');

        return $this->response(true,'success');
    }

    public function startTaskByDriver(Request $request)
    {
        $task = Task::find($request->task_id);
        if($task == null)
        {
            return $this->response(false,'task not found');
        }
        $task->driver_start_date = now();
        $task->save();

        $driver = Driver::find($task->driver_id);
        $driver->sendNotification( 'Task started', 'Task started successfully',[$driver->fcm_token],$task,'no_action');

        return $this->response(true,'success');
    }

    public function confirmFromLocation(Request $request)
    {

        try {
            $data = $request->only(['task_id','driver_id','from_location','lat','lng']);
            $rules = [
                'task_id'   => 'required',
                'driver_id'   => 'required',
                'from_location'   => 'required',
                'lat'   => 'required',
                'lng'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'task not found');
                }
                // Perform the task confirmation logic here
                $task->driver_confirm_from_location = true;
                $task->from_location_confirmation_timestamp = now();
                $task->save();

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function confirmToLocation(Request $request)
    {
        try {
            $data = $request->only(['task_ids', 'driver_id', 'to_location', 'lat', 'lng']);
            $rules = [
                'task_ids'    => 'required|array',
                'driver_id'   => 'required',
                'to_location' => 'required',
                'lat'         => 'required',
                'lng'         => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false, $this->validationHandle($validator->messages()));
            } else {
                // Iterate through the task_ids array
                foreach ($request->task_ids as $task_id) {
                    $task = Task::find($task_id);
                    if ($task == null) {
                        return $this->response(false, 'Task not found for ID ' . $task_id);
                    }
                    // Perform the task confirmation logic here for each task
                    $task->driver_confirm_to_location = true;
                    $task->to_location_confirmation_timestamp = now();
                    $task->save();
                }
                return $this->response(true, 'Success');
            }
        } catch (Exception $e) {
            return $this->response(false, 'System error');
        }
    }

}
