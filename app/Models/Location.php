<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Location extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Auditable;

    public const STATUS_SELECT = [
        '1' => 'active',
        '2' => 'inactive',
    ];

    public $table = 'locations';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'arabic_name',
        'pickup_waiting_time',
        'drop_off_waiting_time',
        'description',
        'lat',
        'lng',
        'mobile',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function locationsClients()
    {
        return $this->belongsToMany(Client::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
