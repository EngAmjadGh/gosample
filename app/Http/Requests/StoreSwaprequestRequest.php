<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSwaprequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('swaprequest_create');
    }

    public function rules()
    {
        return [
            'task_id' => [
                'required',
                'array', // Change 'integer' to 'array'
            ],
            'task_id.*' => [
                'integer', // This rule applies to each element within the 'task_id' array
            ],
            'driver_id' => [
                'required',
                'integer',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
