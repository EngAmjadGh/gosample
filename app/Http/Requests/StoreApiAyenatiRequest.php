<?php

namespace App\Http\Requests;

use App\Models\ApiAyenati;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreApiAyenatiRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('api_ayenati_create');
    }

    public function rules()
    {
        return [];
    }
}
