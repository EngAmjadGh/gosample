<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiAyenati extends Model
{
    use HasFactory;

    public $table = 'api_responses';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public const RESPONSE_FLAG_SELECT = [
        'success' => 'success',
        'failed'  => 'failed',
    ];

    protected $fillable = [
        'api_url',
        'response',
        'response_flag',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
