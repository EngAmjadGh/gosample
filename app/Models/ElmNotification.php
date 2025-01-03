<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElmNotification extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'elm_notifications';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'task_id',
        'type',
        'response_body',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
