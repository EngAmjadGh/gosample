<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Notification;
use App\Notifications\TaskCreated;
use App\Notifications\FirebaseNotification;
use DateTime;
use Str;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\Afaqi;
use Illuminate\Support\Carbon;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    function hoursandmins($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

    function ayenatiResponse($is_successful,$error_code,$error_msg,$data = ''){

        $response['is_successful'] = $is_successful;
        $response['error_code'] = $error_code;
        $response['error_msg'] = $error_msg;

        if($data == null){
            $response['response'] =  null;
        } else {
            $response['response'] = $data;

        }

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    function response($status,$message,$data = ''){

        $response['status'] = $status;
        $response['message'] = $message;
        if($data == null){
            $response['data'] =  null;
        } else {
            $response['data'] = $data;

        }

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    function responseDefaultIsArray($status,$message,$data = []){

        $response['status'] = $status;
        $response['message'] = $message;
        if($data == null){
            $response['data'] =  array();
        } else {
            $response['data'] = $data;

        }
        return response($response, 200)->header('Content-Type', 'application/json');
    }

    function validationHandle($validation){
//        return $validation;
        foreach ($validation->getMessages() as $field_name => $messages){
            if(!isset($firstError)){
                $firstError        =$messages[0];
                $error[$field_name]=$messages[0];
            }
        }
        return $firstError;
    }
    function convert($string) {
        $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
        $num = range(0, 9);
        $englishNumbersOnly = str_replace($arabic, $num, $string);
        return $englishNumbersOnly;
    }

    protected function sendGeneralNotification($driver,$object)
    {

        Notification::send($driver, new TaskCreated($object));
//        if($driver->language == 'ar')
//        {
//            Notification::send($driver, new TaskCreated($object));
////            $this->sendFirebaseNotificationWithType($driver, 'title', 'description');
//
//        } else{
//            Notification::send($driver, new TaskCreated($object));
////            $this->sendFirebaseNotificationWithType($driver, 'title','body');
//
//        }

    }
    protected function getTrackingData($unitId)
    {
        $token = $this->logintoAfaqi();

        $client = new Client();
        $headers = [
        'Content-Type' => 'application/json'
        ];
        $body = '{
        "data": {
            "unit_id": '.$unitId.',
            "color_type": "single",
            "color": "e84118",
            "line_size": "5",
            "date": {
            "interval": "for_previous",
            "from": "2023-01-08 00:00:00",
            "to": "2023-01-09 00:00:00",
            "interval_count": 1,
            "interval_unit": "days",
            "including_current": false
            }
        }
        }';
        $request = new \GuzzleHttp\Psr7\Request('POST', 'http://api.afaqy.pro/tracking/generate?token='.$token, $headers, $body);
        $res = $client->sendAsync($request)->wait();
        return $res->getBody();

    }

    protected function getVehicleDetails($afaqiVehicleId)
    {
        $token = $this->logintoAfaqi();
        $afaqiVehicleId = "627bd4b474a53c3736118ea1";
        $client = new Client();
        $headers = [
        'Content-Type' => 'application/json'
        ];
        $body = '{
        "data": {
                    "id": "'.$afaqiVehicleId.'",
                    "projection": [
                        "basic",
                        "last_update",
                        "counters",
                        "sensors"
                    ]
                }
        }';
        \Log::info($body);
        $request = new \GuzzleHttp\Psr7\Request('POST', 'http://api.afaqy.pro/units/view?token='.$token, $headers, $body);
        $res = $client->sendAsync($request)->wait();
        return $res->getBody();

    }

    protected function logintoAfaqi()
    {
        // get last token, if expired call afaqi and get new token
        $record =  Afaqi::find(1);
        if($record == null)
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
            if($response['status_code'] == 200)
            {
                // save token in db, and return to be used,
                $record = Afaqi::updateOrCreate(
                    ['id' =>  1],
                    ['token' => $response['data']['token']]
                );
                return $record->token;
            }
        } else{
            return $record->token;
        }
    }

    protected function sendFirebaseNotificationWithType($driver, $title, $body){
        try {
            $driver->notify(new FirebaseNotification($title, $body,null));
        }
        catch (Exception $e) {
            \Log::info('system error');
            // return $this->response(false,'system error');
        }
        catch (CouldNotSendNotification $e) {
            \Log::info('CouldNotSendNotification');
            // return $this->response(false,'system error');
        }
        catch (\Throwable $e) {
            \Log::info('system error..');
            \Log::info($e);
            // return $this->response(false,'system error');
        }
    }
    protected function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    protected function time_elapsed_stringArabic($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'سنة',
            'm' => 'شهر',
            'w' => 'اسبوع',
            'd' => 'يوم',
            'h' => 'ساعة',
            'i' => 'دقيقة',
            's' => 'ثانية',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                if($diff->$k > 1){
                    $v = $diff->$k . ' ' . $v ;

                    if (Str::contains($v, 'سنة')){
                        $v = Str::replaceLast("سنة", "سنوات", $v);
                    }
                    if (Str::contains($v, 'شهر')){
                        $v = Str::replaceLast("شهر", "أشهر", $v);
                    }
                    if (Str::contains($v, 'اسبوع')){
                        $v = Str::replaceLast("اسبوع", "آسابيع", $v);
                    }
                    if (Str::contains($v, 'ساعة')){
                        $v = Str::replaceLast("ساعة", "ساعات", $v);
                    }
                    if (Str::contains($v, 'دقيقة')){
                        $v = Str::replaceLast("دقيقة", "دقائق", $v);
                    }
                    if (Str::contains($v, 'ثانية')){
                        $v = Str::replaceLast("ثانية", "ثواني", $v);
                    }
                }
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ?  ' منذ '.implode(', ', $string)  : 'الآن';
    }

    protected function getEnglishNotificationTitle($type){
        switch ($type) {
            case 'App\Notifications\TaskCreated':
                return 'You have new Task';
            case 'App\Notifications\SamplesInFreezer':
                return 'Samples are added to containers successfully';
            case 'App\Notifications\TaskClosedWithoutSamples':
                return 'Task is closed without collecting samples';
            case 'App\Notifications\SamplesCollected':
                return 'Samples are collected successfully';
            case 'App\Notifications\TaskClosed':
                return 'Task is closed successfully';
            case 'App\Notifications\SamplesOutFreezer':
                return 'samples removed from freezer';
            default:
                return '';
        }
    }

    protected function getArabicNotificationTitle($type){
        switch ($type) {

            case 'App\Notifications\TaskCreated':
                return 'تم إنشاء طلب جديد';
            case 'App\Notifications\SamplesInFreezer':
                return 'تم إضافة العينات للثلاجات';
            case 'App\Notifications\TaskClosedWithoutSamples':
                return 'تم اغلاق الطلب بدون استلام عينات';
            case 'App\Notifications\SamplesCollected':
                return 'تم جمع العينات';
            case 'App\Notifications\TaskClosed':
                return 'تم إغلاق الطلب بنجاح';
            case 'App\Notifications\SamplesOutFreezer':
                return 'تم إخراج العينات بنجاح';
            default:
                return '';
        }
    }
    protected function getEnglishNotificationDescription($type, $status){
        switch ($type) {
            case 'App\Notifications\TaskCreated':
                return 'تم إنشاء طلب جديد';
            case 'App\Notifications\SamplesInFreezer':
                return 'تم إضافة العينات للثلاجات';
            case 'App\Notifications\TaskClosedWithoutSamples':
                return 'تم اغلاق الطلب بدون استلام عينات';
            case 'App\Notifications\SamplesCollected':
                return 'تم جمع العينات';
            case 'App\Notifications\TaskClosed':
                return 'تم إغلاق الطلب بنجاح';
            case 'App\Notifications\SamplesOutFreezer':
                return 'تم إخراج العينات بنجاح';
            default:
                return '';
        }
    }

    protected function getArabicNotificationDescription($type, $status){
        switch ($type) {
            case 'App\Notifications\TaskCreated':
                return 'You have new Task';
            case 'App\Notifications\SamplesInFreezer':
                return 'Samples are added to containers successfully';
            case 'App\Notifications\TaskClosedWithoutSamples':
                return 'Task is closed without collecting samples';
            case 'App\Notifications\SamplesCollected':
                return 'Samples are collected successfully';
            case 'App\Notifications\TaskClosed':
                return 'Task is closed successfully';
            case 'App\Notifications\SamplesOutFreezer':
                return 'samples removed from freezer';
            case 'App\Notifications\TaskDelayed':
                return 'Task is delayed more than required hours';
            default:
                return '';
        }
    }

    public static function sendNotification($title, $body,$tokens){
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = $tokens;//
        // $serverKey = 'AAAAbiFTUvY:APA91bGlTJ77caxTQAO6bAUw5OHDyDV9vMjLJ0Scy5OHebuv9xWEU_VOhzWsR5rNPMA8HramV-8PI5d03zwjWnm-3UmsZkYQKUMpr6lyNw1m8l4TpQTaw8P_B9StNRD82-7JAUl8iy-r';
        $serverKey = 'AAAALUm9zs0:APA91bEPvG8yI7CWfmFLKrqEJPDCVNpmDlqrPz1jY62Wq0k7lEakb36Qts8ZvNLSoo5Sh_dc47-H61y2NoZurjY0bV-wms1xk13NHEnIQq771LYXPZtJi_qVPZXmbQzELGEZE7ohTlIL';

        $data = [
            "registration_ids" => $FcmToken,
            "data" => [
                "task_id" => 1,
                "task_type" => 'task',
            ]
            // ,
            // "notification" => [
            //     "title" => $title,
            //     "body" => $body,
            // ]
        ];

        \Log::info( $data);
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        \Log::info($result);
        return  $result;
    }



    protected function getNotificaitonType($type){
        switch ($type) {
            case 'App\Notifications\TaskCreated':
                return  1;
            case 'App\Notifications\SamplesInFreezer':
                return 2;
            case 'App\Notifications\TaskClosedWithoutSamples':
                return 3;
            case 'App\Notifications\SamplesCollected':
                return 4;
            case 'App\Notifications\SamplesOutFreezer':
                return 5;
            case 'App\Notifications\TaskClosed':
                return 6;
            default:
                return 0;
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);

          if ($unit == "K") {
            return ($miles * 1.609344);
          } else if ($unit == "N") {
            return ($miles * 0.8684);
          } else {
            return $miles;
          }
        }
      }
    function updateNotification($shipment_id, $agent_first_name, $agent_last_name, $agent_national_id, $agent_mobile, $status_code) {

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

        $responseBody = $response->getBody()->getContents();
        // \Log::info();
        $data = json_decode( $responseBody, true);
        $response = $client->post('https://api-test.lean.sa/p-ayenati/notifications/updateNotificationDetails', [
            'headers' => [
                'Authorization' => 'Bearer '.$data['access_token'],
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'shipment_id' => $shipment_id,
                'agent_first_name' => $agent_first_name,
                'agent_last_name' => $agent_last_name,
                'agent_national_id' => $agent_national_id,
                'agent_mobile' => $agent_mobile,
                'status_code' => $status_code,
                'timestamp' => Carbon::now(),
                'track_url' => 'https://www.gosample.com',
            ]
        ]);
        \Log::info([
            'shipment_id' => $shipment_id,
            'agent_first_name' => $agent_first_name,
            'agent_last_name' => $agent_last_name,
            'agent_national_id' => $agent_national_id,
            'agent_mobile' => $agent_mobile,
            'status_code' => $status_code,
            'timestamp' => Carbon::now(),
            'track_url' => 'https://www.gosample.com',
        ]);
        $data = json_decode( $response->getBody()->getContents(), true);
        return $data;
      }

      function dropoff($shipment_id, $otp, $status_code) {

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
        $response = $client->post('https://api-test.lean.sa/p-ayenati/notifications/updateDropOffOTP', [
            'headers' => [
                'Authorization' => 'Bearer '.$data['access_token'],
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'shipment_id' => $shipment_id,
                'otp' => $otp,
                'status_code' => $status_code,
            ]
        ]);

        \Log::info(
            [
                'shipment_id' => $shipment_id,
                'otp' => $otp,
                'status_code' => $status_code,
            ]

        );
        $data = json_decode( $response->getBody()->getContents(), true);
        return $data;
      }



}
