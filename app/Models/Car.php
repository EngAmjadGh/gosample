<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Car extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Auditable;


    public $table = 'cars';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'driver_id',
        'imei',
        'plate_number',
        'model',
        'afaqi',
        'color',
        'contact_person',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function containers()
    {
        return $this->hasMany('App\Models\Container', 'car_id', 'id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function carCarLinkHistories()
    {
        return $this->hasMany(CarLinkHistory::class, 'car_id', 'id');
    }
    public function carTracking()
    {
        return $this->hasMany(CarTracking::class, 'car_id', 'id')->latest()->take(10);
    }
    public function carTasks()
    {
        return $this->hasMany(Task::class, 'car_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
