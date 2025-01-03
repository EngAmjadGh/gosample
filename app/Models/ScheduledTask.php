<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledTask extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'scheduled_tasks';

    public const TASK_TYPE_SELECT = [
        'SAMPLE' => 'SAMPLE',
        'BOX'    => 'BOX',
    ];

    // protected $casts = [
    //     'days' => 'array'
    // ];

    public const STATUS_SELECT = [
        'enabled'  => 'enabled',
        'disabled' => 'disabled',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'status',
        'start_date',
        'selected_hour',
        'day',
        'end_date',
        'driver_id',
        'selected_days',
        'from_location_id',
        'to_location_id',
        'client_id',
        'task_type',
        'added_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function from_location()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function to_location()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // public function isDue()
    // {
    //     $now = Carbon::now();

    //     if ($now < $this->start_date || $now > $this->end_date) {
    //         return false;
    //     }

    //     $days = explode(',', $this->days_of_week);

    //     if (!in_array(strtolower($now->englishDayOfWeek), $days)) {
    //         // ScheduledTask is not set to run on this day of the week
    //         return false;
    //     }
    //     return true;
    // }

    public function isDue()
    {
        $now = Carbon::now();

        // Check if the task is within the date range of the scheduled task
        if ($now < $this->start_date || $now > $this->end_date) {
            return false;
        }

        // Check if a task with the same parameters and created after the scheduled task exists
        $existingTask = Task::where('driver_id', $this->driver_id)
            ->where('from_location', $this->from_location_id)
            ->where('to_location', $this->to_location_id)
            ->where('billing_client', $this->client_id)
            ->where('created_at', '>', $this->created_at)
            ->first();

        if ($existingTask) {
            // A task with the same parameters and created later already exists
            return false;
        }

        return true;
    }

}
