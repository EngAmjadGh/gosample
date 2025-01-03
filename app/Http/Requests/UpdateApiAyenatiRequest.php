<?php

namespace App\Http\Requests;

use App\Models\ApiAyenati;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateApiAyenatiRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('api_ayenati_edit');
    }

    public function rules()
    {
        return [];
    }
}
