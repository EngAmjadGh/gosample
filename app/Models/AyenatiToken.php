<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AyenatiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'token_type',
        'issued_at',
        'expires_in',
        'developer_email',
        'application_name',
        'api_product_list',
    ];
}
