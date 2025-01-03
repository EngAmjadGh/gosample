<?php

namespace App\Http\Controllers;

use App\Events\CurrentDriverLocationEvent;
use App\Events\DriverArrivedAtPickUpLocationEvent;
use App\Events\TaskClosedEvent;
use App\Models\Driver;
use App\Models\Car;
use App\Models\CarPhoto;
use App\Models\ScheduledTask;
use App\Models\Shipment;
use App\Models\Task;
use App\Models\Sample;
use App\Models\Notifications;
use App\Models\Term;
use App\Notifications\FirebaseNotification;
use Notification;
use App\Notifications\TaskCreated;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DateTime;
use NotificationChannels\Fcm\FcmChannel;
use Mail;

use DB;
class DriverController extends Controller
{
    // login api
    public function login(Request $request)
    {
        try {
            $data = $request->only(['username','password','language','fcmToken']);
            $rules = [
                'username'   => 'required',
                'language'   => 'required',
                'fcmToken'   => 'required',
                'password' => 'required|min:4',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $credentials = request(['username', 'password']);
                if (! $token = auth()->guard('drivers')->attempt($credentials)) {
                    return $this->response(false,'invalid username or password');
                }

                $user = Driver::where('username',$request->username)->with(['car'])->where('status',1)
                    ->first();
                if($user == null)
                {
                    return $this->response(false,'invalid username or password');
                }

                if($user->car == null)
                {
                    return $this->response(false,'Please add car to driver');
                }

                $user->fcm_token = $request->fcmToken;
                $user->language = $request->language;
                $user->save();
                $user->api_token = $token;

                // get last task of driver to be displayed in main screen
                // get name of locations
                // get driver name
                // get billing client name


                $latestTask = Task::
                leftjoin('locations AS A', 'A.id', '=', '.from_location')
                    ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                    ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                    ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                        'A.lng as from_location_lng','B.lng as to_location_lng',
                        'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                    ->where('driver_id',$user->id)
                    ->whereIn('tasks.status',array('COLLECTED','COLLECTED','NEW'))
                    ->orderBy('created_at','desc')->first();

                if($latestTask == null)
                {
                    $latestTask = Task::
                    leftjoin('locations AS A', 'A.id', '=', '.from_location')
                        ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                        ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                        ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                            'A.lng as from_location_lng','B.lng as to_location_lng',
                            'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                        ->where('driver_id',$user->id)->orderBy('created_at','desc')->first();
                }

                // parse date to change format to mobile format
                if($latestTask != null){
                    $latestTask->dateString = date('d-F-Y', strtotime($latestTask->created_at));
                    $latestTask->timeString = date('H:i:s A', strtotime($latestTask->created_at));

                }
                if($latestTask == null)
                {
                    $latestTask = Task::
                    leftjoin('locations AS A', 'A.id', '=', '.from_location')
                        ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                        ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                        ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                            'A.lng as from_location_lng','B.lng as to_location_lng',
                            'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                        ->where('tasks.status','CLOSED')->orderBy('created_at','desc')->first();
                }
                $user->latestTask = $latestTask;

                // check if driver has notifications
                $notificationCounts =  0 ;//Notifications::where('notifiable_id',$user->id)->where('notifiable_type','App\Models\Driver')->whereNull('read_at')->count();
                if($notificationCounts > 0)
                {
                    $user->hasNotificaiton = true;
                }else{
                    $user->hasNotificaiton = false;
                }
                if( $user->acceptedTerms == 0)
                {
                    $user->termAccepted = false;
                } else {
                    $user->termAccepted = true;
                }


//
                return $this->response(true,'success',$user);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function loginWithMobile(Request $request)
    {
        try {
            $data = $request->only(['mobile','password','language','fcmToken']);
            $rules = [
                'mobile'   => 'required',
                'language'   => 'required',
                'fcmToken'   => 'required',
                'password' => 'required|min:4',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                // get username of driver by mobile
                $driver = Driver::where('mobile',$request->mobile)->first();
                if($driver == null)
                {
                    return $this->response(false,'invalid mobile or password');
                }
                $request->username = $driver->username;


                $credentials = request(['username', 'password']);
                $credentials['username'] = $driver->username;
                // $credentials = request(['username', 'password']);
                if (! $token = auth()->guard('drivers')->attempt($credentials)) {
                    return $this->response(false,'invalid username or password');
                }

                $user = Driver::where('username',$request->username)->with(['car'])->where('status',1)
                    ->first();
                if($user == null)
                {
                    return $this->response(false,'invalid username or password');
                }

                if($user->car == null)
                {
                    return $this->response(false,'Please add car to driver');
                }

                $user->fcm_token = $request->fcmToken;
                $user->language = $request->language;
                $user->save();
                $user->api_token = $token;

                // get last task of driver to be displayed in main screen
                // get name of locations
                // get driver name
                // get billing client name


                $latestTask = Task::
                leftjoin('locations AS A', 'A.id', '=', '.from_location')
                    ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                    ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                    ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                        'A.lng as from_location_lng','B.lng as to_location_lng',
                        'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                    ->where('driver_id',$user->id)
                    ->whereIn('tasks.status',array('COLLECTED','COLLECTED','NEW'))
                    ->orderBy('created_at','desc')->first();

                if($latestTask == null)
                {
                    $latestTask = Task::
                    leftjoin('locations AS A', 'A.id', '=', '.from_location')
                        ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                        ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                        ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                            'A.lng as from_location_lng','B.lng as to_location_lng',
                            'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                        ->where('driver_id',$user->id)->orderBy('created_at','desc')->first();
                }

                // parse date to change format to mobile format
                if($latestTask != null){
                    $latestTask->dateString = date('d-F-Y', strtotime($latestTask->created_at));
                    $latestTask->timeString = date('H:i:s A', strtotime($latestTask->created_at));

                }
                if($latestTask == null)
                {
                    $latestTask = Task::
                    leftjoin('locations AS A', 'A.id', '=', '.from_location')
                        ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                        ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                        ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                            'A.lng as from_location_lng','B.lng as to_location_lng',
                            'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                        ->where('tasks.status','CLOSED')->orderBy('created_at','desc')->first();
                }
                $user->latestTask = $latestTask;

                // check if driver has notifications
                $notificationCounts =  0 ;//Notifications::where('notifiable_id',$user->id)->where('notifiable_type','App\Models\Driver')->whereNull('read_at')->count();
                if($notificationCounts > 0)
                {
                    $user->hasNotificaiton = true;
                }else{
                    $user->hasNotificaiton = false;
                }
                if( $user->acceptedTerms == 0)
                {
                    $user->termAccepted = false;
                } else {
                    $user->termAccepted = true;
                }


//
                return $this->response(true,'success',$user);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function profile(Request $request)
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


                $user = Driver::where('id',$request->driver_id)->with(['car'])
                    ->first();
                if($user == null)
                {
                    return $this->response(false,'invalid username or password');
                }
                $user->city = 'Riyadh';
                if( $user->acceptedTerms == 0)
                {
                    $user->termAccepted = false;
                } else {
                    $user->termAccepted = true;
                }
                return $this->response(true,'success',$user);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    // tasks api
    public function tasks(Request $request)
    {
        try {
            $data = $request->only(['driver_id','status']);
            $rules = [
                'driver_id'   => 'required',
                'status'   => 'required|in:NEW,COLLECTED,IN_FREEZER,CLOSED,NO_SAMPLES,OUT_FREEZER',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $last_week = \Carbon\Carbon::today()->subDays(7);

                // check driver id
                $driver = Driver::find($request->driver_id);
                if($driver == null)
                {
                    return $this->response(false,'driver id is not correct');
                }


                // get driver tasks
                $tasks = Task::
                leftjoin('locations AS A', 'A.id', '=', '.from_location')
                    ->leftjoin('locations AS B', 'B.id', '=', 'tasks.to_location')
                    ->leftjoin('clients AS C', 'C.id', '=', 'tasks.billing_client')
                    ->select('A.name as from_location_name','B.name as to_location_name','A.lat as from_location_lat','B.lat as to_location_lat',
                        'A.lng as from_location_lng','B.lng as to_location_lng','A.pickup_waiting_time','B.drop_off_waiting_time',
                        'A.mobile as from_mobile','B.mobile as to_location_mobile',
                        'tasks.*','C.arabic_name as client_arabic_name','C.english_name as client_english_name')
                    ->where('driver_id',$request->driver_id)
                    ->where('tasks.status',$request->status)
                    ->where('tasks.created_at','>=',$last_week)
                    ->orderBy('pickup_time','asc')->get();

                foreach ($tasks as $task) {
                    $shipment = null;
                    if($task->ayenati == 'YES' && $task->status == 'NEW')
                    {
                        $shipment = Shipment::where('task_id',$task->id)->first();
                        $task->otp = $shipment? $shipment->pickup_otp:  '';
                    } else{
                        $task->otp = '';
                    }

                    if($task != null){
                        $task->dateString = date('d-F-Y', strtotime($task->pickup_time));
                        $task->timeString = date('H:i:s A', strtotime($task->pickup_time));
                        // count bags of this task
                        $task->numberOfBags = Sample::where('task_id',$task->id)->distinct('bag_code')->count('bag_code');

//                        $task->counts = $samples = Sample::select('bag_code','temperature_type',DB::raw('count(*) as total'))->where('task_id',$request->task_id)
//                            ->where('container_id',$request->container_id)
//                            ->groupBy('bag_code','temperature_type')->get();
                        if($request->status =='NEW')
                        {
                            $task->rtCount= 0;
                            $task->refCount=0;
                            $task->frzCount=0;
                        } else{
                            // get bags of task
                            if($task->task_type == 'SAMPLE')
                            {
                                $task->counts =  DB::table('samples')
                                ->select('temperature_type', DB::raw('count(*) as count'))
                                ->where('task_id',$task->id)
                                ->groupBy('temperature_type')
                                ->get();
                            } else{
                                $task->counts =  DB::table('samples')
                                ->select('temperature_type', DB::raw('count(*) as count'))
                                ->where('task_id',$task->id)
                                ->groupBy('temperature_type')
                                ->get();

                            }


                            $task->rtCount= 0;
                            $task->refCount=0;
                            $task->frzCount=0;

                            foreach ( $task->counts as $count){
                                if($task->ayenati == 'YES') {
                                    $shipment = Shipment::where('task_id',$task->id)->first();
                                    if ($shipment && isset($shipment->id)) {
                                        $task->otp = $shipment->dropoff_otp ?? '';
                                    }
                                }
                                    if($task->task_type == 'SAMPLE')
                                    {
                                        if($count->temperature_type == 'ROOM')
                                        {
                                            $task->rtCount=$count->count;
                                        }
                                        if($count->temperature_type == 'REFRIGERATE')
                                        {
                                            $task->refCount=$count->count;
                                        }
                                        if($count->temperature_type == 'FROZEN')
                                        {
                                            $task->frzCount=$count->count;
                                        }
                                    }else{
                                        if($count->temperature_type == 'ROOM')
                                        {
                                            $task->rtCount=$task->sample_count;
                                            $task->counts[0]->count=$task->sample_count;
                                        }
                                        if($count->temperature_type == 'REFRIGERATE')
                                        {
                                            $task->refCount=$task->sample_count;
                                            $task->counts[0]->count=$task->sample_count;
                                        }
                                        if($count->temperature_type == 'FROZEN')
                                        {
                                            $task->frzCount=$task->sample_count;
                                            $task->counts[0]->count=$task->sample_count;
                                        }
                                }
                            }



                        }
                        // $task->counts =  $task->sample_count;
                    }
                }
//                $this->sendGeneralNotification($driver,$driver,'App\\Notifications\\TaskCreated');

                return $this->response(true,'success',$tasks);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function clientTask(Request $request)
    {
        try {
            $data = $request->only(['driver_id','status']);
            $rules = [
                'driver_id'   => 'required',
                'status'   => 'required|in:NEW,COLLECTED,IN_FREEZER,CLOSED,NO_SAMPLES,OUT_FREEZER',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // get all tasks according to client and bags
                $clients = Task::
                leftJoin('locations','locations.id','=','tasks.to_location')
                    ->select('tasks.to_location','locations.name','locations.arabic_name','locations.lat','locations.lng',
                    'pickup_waiting_time','drop_off_waiting_time',
                    'tasks.driver_confirm_to_location')
                    // ->select('tasks.to_location','locations.name','locations.arabic_name','takasi')
                    ->where('driver_id',$request->driver_id)
                    ->where('tasks.status',$request->status)
                    ->groupby('to_location')
                    ->get();

                // get tasks of client
                foreach ($clients as $client) {
                    $client->english_name =  $client->name;
                    $client->tasks = Task::select('id')->where('to_location',$client->to_location)->where('driver_id',$request->driver_id)
                        ->where('tasks.status',$request->status)->with('samplesSummary')->get();
                }

                // collect task ids
//                return $this->response(true,'success',$clients);

                foreach ($clients as $client) {
                    $taskIds = array();
                    $bag_codes = array();
                    $barcode_ids = array();
                    foreach ($client->tasks as $task) {

                        $taskIds[]=$task->id;
//                        return $task->id;

                        $temp = $task->samplesSummary()->get();
//                        return $this->response(true,'success',$temp);
                        if($temp != null)
                        {
                            foreach ($temp as $sample) {
                                $bag_codes[]=$sample->bag_code;
                                $barcode_ids[]=$sample->barcode_id;
                            }

                        }
                    }
                    $client->taskIds = $taskIds;
//                    $client->bag_codes = $bag_codes;
                    // remove duplication
                    $client->bag_codes = array_values(array_unique($bag_codes)) ;


                    $client->barcode_ids = $barcode_ids;
                }
                // collect barcode ids

                // collect sample ids
                return $this->response(true,'success',$clients);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function carLocation(Request $request)
    {
        try {
            $data = $request->only(['imei']);
            $rules = [
                'imei'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                // get car location
                $data = Http::get('https://api.track.tmtgps.io/latest.php?key=A49797F92E5CF1FF80ADB00C1F5B2DE8&imei='.$request->imei);
                return $this->response(true,'success',$data->json());
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function notifications(Request $request)
    {
        try {
            $data = $request->only(['driver_id']);
            $rules = [
                'driver_id' => 'required|numeric',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            }

            $driver = Driver::find($request->driver_id);
            // $list = $user->notifications;
            $date = \Carbon\Carbon::today()->subDays(100);
            // add type for driver only
            $list = Notifications::where('notifiable_id',$request->driver_id)->where('created_at','>=',$date)->orderBy('created_at','desc')->get();

            $list->each(function ($item, $key) {
                $item->agoArabic=parent::time_elapsed_stringArabic($item->created_at);
                $task = $item->data['task'];
                $item->arabicTitle= parent::getArabicNotificationTitle($item->type);
                $item->title= parent::getEnglishNotificationTitle($item->type);
                $item->description= parent::getArabicNotificationDescription($item->type,'');
                $item->arabicDescription = parent::getEnglishNotificationDescription($item->type,'');

                $item->ago=parent::time_elapsed_string($item->created_at);
                $item->data = $task;
                $item->taskType = parent::getNotificaitonType($item->type);
            });

//            $driver->unreadNotifications->markAsRead();
            return $this->response(true,'success',$list);
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function releaseCar(Request $request)
    {
        try {
            $data = $request->only(['car_id','driver_id']);
            $rules = [
                'car_id'   => 'required',
                'driver_id'   => 'required'
            ];
            $validator = Validator::make($data, $rules);


            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $task = Task::find(2438);
//                DriverArrivedAtPickUpLocationEvent::dispatch($task);
                event (new CurrentDriverLocationEvent($task));

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function sendNotificationToDriver(Request $request)
    {
        try {
            $data = $request->only(['driver_id']);
            $rules = [
                'driver_id'   => 'required'
            ];
            $validator = Validator::make($data, $rules);


            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

              $driver = Driver::find($request->driver_id);
              if($driver == null)
              {
                  return $this->response(false,'driver not found');
              }
//                $driver->notify(new FirebaseNotification('New Task', 'You have new task',null));
            //    $this->sendFirebaseNotificationWithType($driver, 'New Task', 'You have new task');
               $driver->sendNotification( 'New Task', 'You have new task',[$driver->fcm_token],$task,'no_action');


                // send notification
//                dd($result);
//
//                $to_name = 'Safwan';
//                $to_email = 'mohamadsafwan.hijazi@gmail.com';
//                $data = array('name'=>"Safwan Hijazi", 'body' => "Test Email");
//                Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
//                    $message->to($to_email, $to_name)
//                        ->subject('Laravel Test Mail');
//                $message->from('blasma.logisics@gmail.com','Test Mail');
//                });
                return $this->response(true,'success',$driver);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }


    public function updateDriverLocation(Request $request)
    {
        try {
            $data = $request->only(['driver_id','lat','lng']);
            $rules = [
                'driver_id'   => 'required',
                'lat'   => 'required',
                'lng'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $driver = Driver::find($request->driver_id);
                if($driver == null)
                {
                    return $this->response(false,'driver not found');
                }
                $driver->lat = $request->lat;
                $driver->lng = $request->lng;
                $driver->save();

                // get car of driver and update location of car
                Car::where('driver_id', $request->driver_id)->update([
                    'lat' => $request->lat, // Set the desired latitude value
                    'lng' => $request->lng, // Set the desired longitude value
                ]);

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function getTasksFromCache(Request $request)
    {
        try {
            $data = $request->only([]);
            $rules = [
//                'driver_id'   => 'required',
//                'lat'   => 'required',
//                'lng'   => 'required'
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
//                $tasks = new Task();

//                $tasks = Task::search(
//                    '',
//                    function (SearchIndex $milesearch, string $query, array $options) {
//                        $options['filters']='id=1401';
//
//                        return $milesearch->search($query, $options);
//                    }
//                )->get();
                $tasks =Task::search('')->where('id','>',1)->get();
//                dd($tasks);
                return $this->response(true,'success',$tasks);
//                return $this->response(true,'success',Task::search('BOX')->get());
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }

    }

    public function acceptTerms(Request $request)
    {
        try {
            $data = $request->only(['driver_id','signature']);
            $rules = [
                'driver_id'   => 'required',
                'signature'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {
                $driver = Driver::find($request->driver_id);
                $driver->acceptedTerms = 1;
                $driver->save();
                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function terms(Request $request)
    {
        try {
            $data = $request->only(['']);
            $rules = [
//                'driver_id'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {

                $terms = new Term;
                $terms->arabicLink = 'https://data-videos.fra1.digitaloceanspaces.com/terms/terms/policy_ar.pdf';
                $terms->englishLink = 'https://data-videos.fra1.digitaloceanspaces.com/terms/terms/POLICY%20MTC%20-%20ENGLISH%202022.pdf';
                return $this->response(true,'success',$terms);
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }


    public function uploadPhotes(Request $request)
    {
        try {
            $data = $request->only(['driver_id','images','signature','car_id']);
            $rules = [
                'driver_id'   => 'required',
                'car_id'   => 'required',
//                'deliver_signature'   => 'required',
//                'deliver_confirmationCode'   => 'required',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->response(false,$this->validationHandle($validator->messages()));
            } else {


                $carPhoto = new CarPhoto;
                if ($request->hasFile('signature')) {
                    $carPhoto->addMedia($request->signature)->toMediaCollection('downloads', 'local');

                }


//                $image = $request->file('signature');
//                $s3 = \Storage::disk('s3');
//                $file_name = uniqid() .'.'. $image->getClientOriginalExtension();
//                $s3filePath = '/assets/' . $file_name;
//                $s3->put($s3filePath, file_get_contents($image), 'public');


//                $signature = '';
//                if($request->signature != null){
//                    $media = MediaUploader::fromSource($request->file('signature'))
//                        ->toDestination('uploads', 'signature-car-images')
//                        ->useHashForFilename()
//                        ->upload();
//
//                    $signature = '/'.$media->directory .'/'.$media->filename.'.'.$media->extension;
//                }else{
//                    return $this->response(false,'driver signature is required');
//                }
//
//                if($request->images != null){
//                    $media = MediaUploader::fromSource($request->file('signature'))
//                        ->toDestination('uploads', 'car-images')
//                        ->useHashForFilename()
//                        ->upload();
//
//                    $signature = '/'.$media->directory .'/'.$media->filename.'.'.$media->extension;
//                }else{
//                    return $this->response(false,'driver signature is required');
//                }

                return $this->response(true,'success');
            }
        } catch (Exception $e) {
            return $this->response(false,'system error');
        }
    }

    public function getDriverSchedule(Request $request)
    {

        // return $this->response(false,'sucess');
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'day_of_week' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ]);

        $driverId = $request->input('driver_id');
        $dayOfWeek = $request->input('day_of_week');

        $scheduledTasks = ScheduledTask::where('driver_id', $driverId)
            ->where('status', 'enabled')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->where('day', 'like', '%' . strtolower($dayOfWeek) . '%')
            ->orderBy('start_date', 'asc')
            ->get();

        $response = [];



        foreach ($scheduledTasks as $scheduledTask) {
            $formattedDate = now()->format('F j, Y');
            // $formattedDate = $scheduledTask->start_date->format('F j, Y');
            $fromLocation = $scheduledTask->from_location;
            $toLocation = $scheduledTask->to_location;
            $driver = $scheduledTask->driver->mobile; // Adjust the driver property as needed
            $selected_hour =$scheduledTask->selected_hour; // Adjust the driver property as needed
            // $selected_hour = Carbon::parse($scheduledTask->selected_hour)->format('h:i A'); // Adjust the driver property as needed

            $scheduleInfo = [
                'date' => $formattedDate,
                'from_location' => [
                    'name' => $fromLocation->name,
                    'lat' => $fromLocation->lat,
                    'lng' => $fromLocation->lng,
                ],
                'driver' => $driver,
                'visit_hour' => $selected_hour,
                'to_location' => [
                    'name' => $toLocation->name,
                    'lat' => $toLocation->lat,
                    'lng' => $toLocation->lng,
                ],
                'client' => $scheduledTask->client->english_name, // Replace with the actual client name attribute
            ];

            $response[] = $scheduleInfo;
        }

        return $this->response(true,'sucess',$response);
        // return response()->json($response);
    }

}
