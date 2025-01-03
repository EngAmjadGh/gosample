<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarLinkHistory extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const ACTION_SELECT = [
        "linked" => "linked",
        "unlinked" => "unlinked",
    ];

    public $table = 'car_link_history';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'driver_id',
        'car_id',
        'action',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
