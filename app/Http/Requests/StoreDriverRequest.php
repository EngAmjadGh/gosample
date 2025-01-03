<?php

namespace App\Http\Requests;

use App\Models\Driver;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDriverRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('driver_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'min:1',
                'max:50',
                'required',
            ],
            'password' => [
                'required',
            ],
            'working_hours_start' => [
                'required',
                // 'date_format:H:i',

            ],
            'working_hours_end' => [
                'required',
                // 'date_format:H:i',
                'after:working_hours_start',

            ],
            'status' => [
                'required',
            ],
            'username' => [
                'string',
                'required',
                'unique:drivers',
            ],
            'mobile' => [
                'string',
                'required',
                'unique:drivers',
            ],
            'email' => [
                'string',
                'required',
                'unique:drivers',
            ],
           
        ];
    }
}
