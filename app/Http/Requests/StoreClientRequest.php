<?php

namespace App\Http\Requests;

use App\Models\Client;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreClientRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('client_create');
    }

    public function rules()
    {
        return [
            'status' => [
                'required',
            ],
            'arabic_name' => [
                'string',
                'nullable',
            ],
            'english_name' => [
                'string',
                'nullable',
            ],
            'email' => [
                'string',
                'nullable',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'drivers' => [
                'required',
                'array',
            ],
            'locations' => [
                'required',
                'array',
            ],
        ];
    }
}
