<?php

namespace App\Http\Requests;

use App\Models\Sample;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSampleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sample_edit');
    }

    public function rules()
    {
        return [
            'barcode' => [
                'string',
                'nullable',
            ],
            'box_count' => [
                'string',
                'nullable',
            ],
            'sample_count' => [
                'string',
                'nullable',
            ],
            'confirmed_by_client' => [
                'string',
                'nullable',
            ],
            'confirmed_by' => [
                'string',
                'nullable',
            ],
            'sample_type' => [
                'string',
                'nullable',
            ],
            'temperature_type' => [
                'string',
                'nullable',
            ],
            'bag_code' => [
                'string',
                'nullable',
            ],
        ];
    }
}
