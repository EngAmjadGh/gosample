<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSwaprequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('swaprequest_edit');
    }

    public function rules()
    {
        return [
            'task_id' => [
                'required',
                'integer',
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
