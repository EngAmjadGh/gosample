<?php

namespace App\Http\Requests;

use App\Models\Zone;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreZoneRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('zone_create');
    }

    public function rules()
    {
        return [
            'area' => [
                'string',
                'required',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
