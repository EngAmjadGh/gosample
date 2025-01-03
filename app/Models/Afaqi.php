<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afaqi extends Model
{
    use HasFactory;

    public $table = 'afaqi';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'token',
        'created_at',
        'updated_at',
    ];
}
