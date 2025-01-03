<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'driver_schedule';

    public $timestamps = true;

    protected $fillable = ['driver_id','location_id','note','created_at','updated_at','plate_number'];

}