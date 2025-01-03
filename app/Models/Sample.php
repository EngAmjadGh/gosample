<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sample extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const STATUS_SELECT = [
        '1' => 'enabled',
        '2' => 'disabled',
    ];

    public const RECEIVING_STATUS_SELECT = [
        'YES' => 'RECEIVED',
        'NO' => 'PENDING',
        'LOST' => 'LOST',
    ];

    public $table = 'samples';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'barcode',
        'barcode_id',
        'location_id',
        'task_id',
        'container_id',
        'box_count',
        'sample_count',
        'confirmed_by_client',
        'confirmed_by',
        'status',
        'sample_type',
        'temperature_type',
        'bag_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
