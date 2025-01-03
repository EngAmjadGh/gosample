<?php

namespace App\Http\Requests;

use App\Models\Location;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateLocationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('location_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'arabic_name' => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'required',
            ],
            'lat' => [
                'numeric',
            ],
            'lng' => [
                'string',
                'nullable',
            ],
            // 'mobile' => [
            //     'string',
            //     'required',
            //     'unique:locations,mobile,' . request()->route('location')->id,
            // ],
        ];
    }
}
