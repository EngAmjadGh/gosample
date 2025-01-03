<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MoneyTransfer extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'money_transfers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'new'             => 'New',
        'cancelled'          => 'Cancelled',
        'closed'          => 'Closed',
        'confirmed'       => 'Confirmed',
        'amount_received' => 'Amount Received',
    ];

    protected $fillable = [
        'driver_id',
        'amount',
        'client_id',
        'from_location_id',
        'to_location_id',
        'status',
        'from_location_otp',
        'to_location_otp',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function generateOtp()
    {
        $min = 1000; // minimum value of the 4-digit OTP
        $max = 9999; // maximum value of the 4-digit OTP
        return strval(rand($min, $max));
        // return Str::random(4);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function client()

    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function from_location()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function to_location()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
