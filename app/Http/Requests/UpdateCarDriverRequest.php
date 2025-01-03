<?php

namespace App\Http\Requests;

use App\Models\CarDriver;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCarDriverRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('car_driver_edit');
    }

    public function rules()
    {
        return [
            'car_id' => [
                'required',
                'integer',
            ],
            'driver_id' => [
                'required',
                'integer',
            ],
            'is_linked' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
