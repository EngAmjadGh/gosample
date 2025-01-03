<?php

namespace App\Http\Requests;

use App\Models\Zone;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateZoneRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('zone_edit');
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
