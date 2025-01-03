<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    //

    public function list(Request $request)
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

                $data = Schedule::leftJoin('locations as fromlocations','fromlocations.id','=','from_location')
                    ->leftJoin('locations as tolocations','tolocations.id','=','to_location')
                    ->where('driver_id',$request->driver_id)
                    ->select('driver_schedule.id','driver_schedule.note','driver_schedule.driver_id','tolocations.name as to_location','fromlocations.name as from_location','driver_schedule.plate_number')
                    ->get();

                foreach ($data as $row)
                {
                    //$row->name = $row->from_location .' To '.$row->to_location;
                    $row->name = $row->from_location ;
                    $row->note = 'Location: From '.$row->from_location .' To '. PHP_EOL.$row->to_location.PHP_EOL.PHP_EOL.'Notes: '. PHP_EOL.$row->note;
                }
                return $this->response(true,'success',$data);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }
}
