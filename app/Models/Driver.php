<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hash;
use App\Traits\Auditable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable  implements JWTSubject
{
    use SoftDeletes;
    use HasFactory;
    use Notifiable;


    public const LANGUAGE_SELECT = [
        'en' => 'English',
        'ar' => 'Arabic',
    ];

    public const STATUS_SELECT = [
        1  => 'Enabled',
        2 => 'Disabled',
    ];

    public $table = 'drivers';

    protected $hidden = [
        'password',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'zone_id',
        'working_hours_start',
        'working_hours_end',
        'second_shift_working_hours_start',
        'second_shift_working_hours_end',
        'name',
        'password',
        'status',
        'username',
        'national_id',
        'mobile',
        'email',
        'language',
        'lat',
        'lng',
        'acceptedTerms',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function car()
    {
        return $this->hasOne('App\Models\Car','driver_id')->with('containers');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task','driver_id');
    }
    
    public function driverTasks()
    {
        return $this->hasMany(Task::class, 'driver_id', 'id');
    }
    public function driverActiveTasks()
    {
        return $this->driverTasks()->whereNotIn('status',['CLOSED','NO_SAMPLES']);
    }

    public function driverActiveDelayedTasks()
    {
        return $this->driverTasks()->whereNotIn('status',['CLOSED','NO_SAMPLES'])->where('delayed_reason','<>','');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function driverCarLinkHistories()
    {
        return $this->hasMany(CarLinkHistory::class, 'driver_id', 'id');
    }
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
    public function scheduledTasks()
    {
        return $this->hasMany(ScheduledTask::class);
    }

    public function sendNotification($title, $body,$tokens,$task ,$action)
    {
        \Log::info("sendNotification");
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = $tokens;//
        $serverKey = 'AAAALUm9zs0:APA91bEPvG8yI7CWfmFLKrqEJPDCVNpmDlqrPz1jY62Wq0k7lEakb36Qts8ZvNLSoo5Sh_dc47-H61y2NoZurjY0bV-wms1xk13NHEnIQq771LYXPZtJi_qVPZXmbQzELGEZE7ohTlIL';

        // $serverKey = 'AAAAbiFTUvY:APA91bGlTJ77caxTQAO6bAUw5OHDyDV9vMjLJ0Scy5OHebuv9xWEU_VOhzWsR5rNPMA8HramV-8PI5d03zwjWnm-3UmsZkYQKUMpr6lyNw1m8l4TpQTaw8P_B9StNRD82-7JAUl8iy-r';

        $from_location = $task->from;
        $to_location = $task->to;
        $task->from_location_name = $from_location->name;
        $task->to_location_name = $to_location->name;
        $data = [
            "registration_ids" => $FcmToken,
            "data" => [
                "title" => $title,
                "body" => $body,
                "task_id" => $task->id,
                "from_location_name" => $from_location->name,
                "to_location_name" => $to_location->name,
                "task_type" => 'task',
                "action" => $action,
                "task_object" => $task,
            ]
        ];
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
}
