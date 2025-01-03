<?php

namespace App\Http\Requests;

use App\Models\ClientDriver;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreClientDriverRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('client_driver_create');
    }

    public function rules()
    {
        return [
            'driver_id' => [
                'required',
                'integer',
            ],
            'client_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
