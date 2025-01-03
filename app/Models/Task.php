<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const TAKASI_SELECT = [
        'NO'  => 'NO',
        'YES' => 'YES',
    ];

    public const AYENATI_SELECT = [
        'YES' => 'YES',
        'NO'  => 'NO',
    ];

    public const TASK_TYPE_SELECT = [
        'SAMPLE' => 'SAMPLE',
        'BOX'    => 'BOX',
    ];

    public const CONFIRMED_BY_CLIENT_SELECT = [
        'YES' => 'YES',
        'NO'  => 'NO',
    ];

    public const TYPE_SELECT = [
        'one_time'  => 'one_time',
        'scheduled' => 'scheduled',
    ];

    public const STATUS_SELECT = [
        'NEW'         => 'NEW',
        'COLLECTED'   => 'COLLECTED',
        'CLOSED'      => 'CLOSED',
        'IN_FREEZER'  => 'IN_FREEZER',
        'NO_SAMPLES'  => 'NO_SAMPLES',
        'OUT_FREEZER' => 'OUT_FREEZER',
    ];

    public $table = 'tasks';

    protected $dates = [
        'driver_start_date',
        'dropoff_time',
        'pickup_time',
        'start_date',
        'end_date',
        'close_date',
        'collection_date',
        'freezer_date',
        'freezer_out_date',
        'from_location_arrival_time',
        'to_location_arrival_time',
        'created_at',
        'updated_at',
        'deleted_at',

        'task_confirmation_timestamp',
        'from_location_confirmation_timestamp',
        'to_location_confirmation_timestamp',
    ];

    protected $fillable = [
        'from_location',
        'closed_by',
        'to_location',
        'billing_client',
        'driver_id',
        'car_id',
        'collect_lat',
        'collect_lng',
        'close_lat',
        'close_lng',
        'cost',
        'confirmation_time',
        'close_hour',
        'box_count',
        'sample_count',
        'type',
        'task_type',
        'confirmed_by_client',
        'ayenati',
        'takasi',
        'status',
        'added_by',
        'signature',
        'deliver_signature',
        'deliver_confirmation_code',
        'confirmation_code',
        'description',
        'time_of_visit',
        'start_date',
        'end_date',
        'takasi_number',
        'to_takasi_number',
        'close_date',
        'collection_date',
        'freezer_date',
        'freezer_out_date',
        'from_location_arrival_time',
        'to_location_arrival_time',
        'pickup_time',
        'dropoff_time',
        'delayed_reason',

        'dropoff_lat',
        'dropoff_lng',
        'pickup_lng',
        'pickup_lat',
        'created_at',
        'updated_at',
        'deleted_at',

        'confirmed_received_by_driver',
        'driver_confirm_to_location',
        'driver_confirm_from_location',

        'task_confirmation_timestamp',
        'from_location_confirmation_timestamp',
        'to_location_confirmation_timestamp',
        'driver_start_date',

    ];


    public function delayed_tasks_in_freezer($client_id = null)
    {
        $fourDaysAgo = Carbon::now()->subDays(4);
        $data= $this
        ->whereRaw('TIMESTAMPDIFF(MINUTE,  collection_date,NOW() ) > 10')
        ->where('status','COLLECTED')
        ->where('created_at', '>=', $fourDaysAgo);
        if ($client_id){
            $data = $data->where('billing_client',$client_id);
        }
        return $data->get();
    }

    public function delayed_tasks_delivered($client_id = null)
    {
        $fourDaysAgo = Carbon::now()->subDays(4);
        $data = $this
        ->whereRaw('TIMESTAMPDIFF(MINUTE,  freezer_out_date,NOW() ) > 5')
        ->where('status','OUT_FREEZER')
        ->where('created_at', '>=', $fourDaysAgo);
        if ($client_id){
            $data = $data->where('billing_client',$client_id);
        }
        return $data->get();
    }

    public function pickup_delayedTasks($client_id = null)
    {
        $fourDaysAgo = Carbon::now()->subDays(4);
        $data =  $this->whereRaw('pickup_time < collection_date')
        ->where('created_at', '>=', $fourDaysAgo);
        if ($client_id){
            $data = $data->where('billing_client',$client_id);
        }
        return $data->get();
    }

    public function drop_off_delayedTasks($client_id = null)
    {
        $fourDaysAgo = Carbon::now()->subDays(4);
        $data = $this->whereRaw('dropoff_time < close_date')
        ->where('created_at', '>=', $fourDaysAgo);
        if ($client_id){
            $data = $data->where('billing_client',$client_id);
        }
        return $data->get();
    }


    public function from()
    {
        return $this->belongsTo(Location::class, 'from_location');
    }

    public function to()
    {
        return $this->belongsTo(Location::class, 'to_location');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'billing_client');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function samples()
    {
        return $this->hasMany(Sample::class, 'task_id', 'id');
    }
    public function carTracking()
    {
        return $this->hasMany(CarTracking::class, 'task_id', 'id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function samplesSummary()
    {
        return $this->hasMany(Sample::class)->select('id', 'barcode_id', 'bag_code','temperature_type','sample_type','task_id','container_id');
    }
    // public function getStartDateAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setStartDateAttribute($value)
    // {
    //     $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getEndDateAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setEndDateAttribute($value)
    // {
    //     $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getCloseDateAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setCloseDateAttribute($value)
    // {
    //     $this->attributes['close_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getCollectionDateAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setCollectionDateAttribute($value)
    // {
    //     $this->attributes['collection_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getFreezerDateAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setFreezerDateAttribute($value)
    // {
    //     $this->attributes['freezer_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getFreezerOutDateAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setFreezerOutDateAttribute($value)
    // {
    //     $this->attributes['freezer_out_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getFromLocationArrivalTimeAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setFromLocationArrivalTimeAttribute($value)
    // {
    //     $this->attributes['from_location_arrival_time'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    // public function getToLocationArrivalTimeAttribute($value)
    // {
    //     return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    // }

    // public function setToLocationArrivalTimeAttribute($value)
    // {
    //     $this->attributes['to_location_arrival_time'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    // }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Custom accessor method to calculate time spent in "Received" step
    public function getTimeInReceivedStepAttribute()
    {
        if ($this->received_at) {
            return $this->received_at->diffInSeconds(now());
        }

        return null;
    }


    // Custom accessor method to calculate time spent in "Started" step
    public function getTimeInStartedStepAttribute()
    {
        if ($this->started_at) {
            return $this->started_at->diffInSeconds(now());
        }

        return null;
    }

    // Custom accessor method to calculate time spent in "Pickup" step
    public function getTimeInPickupStepAttribute()
    {
        if ($this->pickup_time) {
            return $this->pickup_time->diffInSeconds(now());
        }

        return null;
    }

    // Custom accessor method to calculate time spent in "Dropoff" step
    public function getTimeInDropoffStepAttribute()
    {
        if ($this->dropoff_time) {
            return $this->dropoff_time->diffInSeconds(now());
        }

        return null;
    }
}
