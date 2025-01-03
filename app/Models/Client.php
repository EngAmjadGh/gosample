<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Client extends Model 
{
    use SoftDeletes;
    use HasFactory;
    use Auditable;

    public const STATUS_SELECT = [
        '1'  => 'Enabled',
        '2' => 'Disabled',
    ];

    public $table = 'clients';

    // protected $appends = [
    //     'logo',
    // ];

    protected $hidden = [
        'password',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'status',
        'arabic_name',
        'english_name',
        'email',
        'address',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'client_location', 'client_id', 'location_id');
    }
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'client_driver', 'client_id', 'driver_id');
    }
}
