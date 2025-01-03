<?php

namespace App\Http\Requests;

use App\Models\ClientAccount;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreClientAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('client_account_create');
    }

    public function rules()
    {
        return [
            'username' => [
                'string',
                'nullable',
            ],
            'password' => [
                'string',
                'nullable',
            ],
            'name' => [
                'string',
                'nullable',
            ],
        ];
    }
}
