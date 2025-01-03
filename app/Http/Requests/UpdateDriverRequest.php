<?php

namespace App\Http\Requests;

use App\Models\Driver;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDriverRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('driver_edit');
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
            'status' => [
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
            'username' => [
                'string',
                'required',
                'unique:drivers,username,' . request()->route('driver')->id,
            ],
            'mobile' => [
                'string',
                'required',
                'unique:drivers,mobile,' . request()->route('driver')->id,

            ],
            'email' => [
                'string',
                'required',
                'unique:drivers,email,' . request()->route('driver')->id,
            ],
           
        ];
    }
}
