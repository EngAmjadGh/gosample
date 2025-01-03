<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverSchedule extends Model
{
    use HasFactory;

    public $table = 'driver_schedule';

    protected $dates = [
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    protected $fillable = [
        'from_location',
        'to_location',
        'driver_id',
        'note',
        'plate_number',
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    public function from()
    {
        return $this->belongsTo(Location::class, 'from_location');
    }

    public function to()
    {
        return $this->belongsTo(Location::class, 'to_location');
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
