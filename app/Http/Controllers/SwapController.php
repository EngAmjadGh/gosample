<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Sample;
use App\Models\Driver;
use App\Models\Task;
use App\Models\Swap;
use App\Models\Shipment;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
class SwapController extends Controller
{
    public function create(Request $request)
    {
        try {
            $data = $request->only(['driver_id','task_id']);
            $rules = [
                'task_id'   => 'required',
                'driver_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {


                $user = Driver::find($request->driver_id);
                if($user == null)
                {
                    return $this->response(false,'invalid driver');
                }

                $task = Task::find($request->task_id);
                if($task == null)
                {
                    return $this->response(false,'invalid task');
                }
                if($task->status == 'closed')
                {
                    return $this->response(false,'invalid task status');
                }

                $swap = new Swap();
                $swap->task_id = $request->task_id;
                $swap->driver_a = $request->driver_id;
                $swap->status = 'new';
                $swap->save();
                // send notification of swap request
                return $this->response(true,'success',$swap);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function listTasksPerDriver(Request $request){
        $tasks = Task::with('from')->where('status','<>','NO_SAMPLES')
        ->where('driver_id',$request->driver_id)
        ->where('status','<>','CLOSED')->get();

         return $this->response(true,'success',$tasks);

    }
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


                $user = Driver::find($request->driver_id);
                if($user == null)
                {
                    return $this->response(false,'invalid driver');
                }

                 // Calculate the start and end of the current day
                $startOfDay = now()->startOfDay();
                $endOfDay = now()->endOfDay();

                $swaps = Swap::where('driver_id',$request->driver_id)
                ->where('status','new')
                ->with('task')
                ->with('task.driver')
                ->with('task.samples')
                ->whereHas('task', function ($query) use ($startOfDay, $endOfDay) {
                    $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
                })
                ->get();


                foreach ($swaps as $swap) {
                    if($swap->task != null){
                        $swap->task->dateString = date('d-F-Y', strtotime($swap->created_at));
                        $swap->task->timeString = date('H:i:s A', strtotime($swap->created_at));
                        // $swap->task->box_count = 1;
                        // $swap->task->sample_count = 1;
                        $swap->task->from_location_name = Location::find($swap->task->from_location)->name;
                        $swap->task->to_location_name = Location::find($swap->task->to_location)->name;
                    }
                }



                return $this->response(true,'success',$swaps);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function accept(Request $request)
    {
        try {
            $data = $request->only(['swap_id']);
            $rules = [
                'swap_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $swap = Swap::find($request->swap_id);
                if($swap == null)
                {
                    return $this->response(false,'invalid swap');
                }
                $task = Task::find($swap->task_id);
                if($task->status != 'OUT_FREEZER')
                {
                    return $this->response(false,'please release samples from freezer');
                }

                $car = Car::where('driver_id',$swap->driver_id)->first();
                if($car == null)
                {
                    return $this->response(false,'car');
                }


                DB::beginTransaction();
                $swap->status ='accepted';
                $swap->save();

                // update driver and car
                $task->driver_id = $swap->driver_id;
                $task->car_id = $car->id;
                $task->status = 'COLLECTED';
                $task->save();

                DB::commit();
                return $this->response(true,'success',$swap);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false,'system error');
        }
    }
    public function receive(Request $request)
    {
        try {
            $data = $request->only(['swap_id','lat','lng']);
            $rules = [
                'swap_id'   => 'required',
                'lat'   => 'required',
                'lng'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $swap = Swap::find($request->swap_id);
                if($swap == null)
                {
                    return $this->response(false,'invalid swap');
                }
                $task = Task::find($swap->task_id);
                if($task->status != 'OUT_FREEZER')
                {
                    return $this->response(false,'please release samples from freezer');
                }

                $car = Car::where('driver_id',$swap->driver_id)->first();
                if($car == null)
                {
                    return $this->response(false,'car');
                }


                DB::beginTransaction();
                $swap->accepted_by_receiver =true;
                $swap->save();

                DB::commit();
                return $this->response(true,'success',$swap);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false,'system error');
        }
    }

    public function acceptall(Request $request)
    {
        try {
            $data = $request->only(['swap_tasks', 'lat', 'lng']);
            $rules = [
                'swap_tasks' => 'required|array', // Expect an array of swap_id values
                // 'lat' => 'required',
                // 'lng' => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false, $this->validationHandle($validator->messages()));
            } else {
                $swapIds = $request->input('swap_tasks'); // Get an array of swap_id values from the request
    
                // Initialize an array to store the results
                $results = [];
    
                foreach ($swapIds as $swapId) {
                    $swap = Swap::find($swapId);
                    if ($swap == null) {
                        $results[] = 'Invalid swap for swap_id: ' . $swapId;
                        continue; // Skip to the next swap_id
                    }
                    $task = Task::find($swap->task_id);
                    // if ($task->status != 'OUT_FREEZER') {
                    //     $results[] = 'Please release samples from freezer for swap_id: ' . $swapId;
                    //     continue; // Skip to the next swap_id
                    // }
    
                    $car = Car::where('driver_id', $swap->driver_id)->first();
                    if ($car == null) {
                        $results[] = 'Car not found for swap_id: ' . $swapId;
                        continue; // Skip to the next swap_id
                    }
    
                    DB::beginTransaction();
                    $swap->accepted_by_receiver = true;
                    $swap->save();
                    DB::commit();
    
                    $results[] = 'Swap accepted for swap_id: ' . $swapId;
                }
    
                // Return the results as a response
                return $this->response(true, 'success', ['results' => $results]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(false, 'system error');
        }
    }
    public function reject(Request $request)
    {
        try {
            $data = $request->only(['swap_id']);
            $rules = [
                'swap_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $swap = Swap::find($request->swap_id);
                if($swap == null)
                {
                    return $this->response(false,'invalid swap');
                }
                $swap->status ='rejected';
                $swap->save();
                return $this->response(true,'success',$swap);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
}
