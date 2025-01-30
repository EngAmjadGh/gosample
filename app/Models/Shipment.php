<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    // use SoftDeletes;
    use HasFactory;

    protected $table = 'shipment';


    
    protected $dates = [
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    protected $fillable = [
        'carrier',
        'sender_name',
        'sender_long',
        'sender_lat',
        'sender_mobile',
        'receiver_name',
        'receiver_long',
        'receiver_lat',
        'receiver_mobile',
        'reference_number',
        'pickup_otp',
        'notes',
        'batch',
        'journey_type',
        'sla_code',
        'status_code',
        'dropoff_otp',
        'task_id',
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // public function task()
    // {
    //     return $this->belongsTo(Task::class, 'task_id');
    // }
}