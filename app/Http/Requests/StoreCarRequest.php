<?php

namespace App\Http\Requests;

use App\Models\Car;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCarRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('car_create');
    }

    public function rules()
    {
        return [
            'imei' => [
                'string',
                'required',
                'unique:cars',
            ],
            'plate_number' => [
                'string',
                'required',
            ],
            'afaqi' => [
                'boolean',
                'required',
            ],
            'model' => [
                'string',
                'nullable',
            ],
            'color' => [
                'string',
                'nullable',
            ],
            'contact_person' => [
                'string',
                'required',
            ],
        ];
    }
}
