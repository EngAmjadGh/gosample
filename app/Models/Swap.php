<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \DateTimeInterface;

class Swap extends Model
{
    use HasFactory;

    public const STATUS_SELECT = [
        'new'      => 'New',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
    ];


    public $table = 'swap_requests';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'task_id',
        'driver_id',
        'accepted_by_receiver',
        'driver_a',
        'status',
        'created_at',
        'updated_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    public function driverA()
    {
        return $this->belongsTo(Driver::class, 'driver_a');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
