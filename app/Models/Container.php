<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const STATUS_SELECT = [
        '1' => 'enabled',
        '2' => 'disabled',
    ];

    public const TYPE_SELECT = [
        'ROOM'        => 'ROOM',
        'REFRIGERATE' => 'REFRIGERATE',
        'FROZEN'      => 'FROZEN',
    ];

    public $table = 'containers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'car_id',
        'imei',
        'type',
        'model',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
