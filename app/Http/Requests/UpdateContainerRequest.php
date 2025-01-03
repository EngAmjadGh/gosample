<?php

namespace App\Http\Requests;

use App\Models\Container;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateContainerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('container_edit');
    }

    public function rules()
    {
        return [
            'imei' => [
                'string',
                'required',
            ],
            'model' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
