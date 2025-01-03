<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarDriver extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'car_drivers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'car_id',
        'driver_id',
        'is_linked',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
