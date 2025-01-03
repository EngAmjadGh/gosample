<?php

namespace App\Http\Requests;

use App\Models\ClientLocation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateClientLocationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('client_location_edit');
    }

    public function rules()
    {
        return [
            'is_linked' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
