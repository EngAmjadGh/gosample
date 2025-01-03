<?php

namespace App\Http\Requests;

use App\Models\Container;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreContainerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('container_create');
    }

    public function rules()
    {
        return [
            'imei' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'model' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
