<?php

namespace App\Http\Requests;

use App\Models\MoneyTransfer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMoneyTransferRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('money_transfer_create');
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
            'from_location_id' => [
                'required',
                'integer',
            ],
            'status' => [
                'required',
            ],
            'from_location_otp' => [
                'string',
                'required',
            ],
            'to_otp' => [
                'string',
                'required',
            ],
            'amount' => [
                'numeric',
                'required',
            ],
        ];
    }
}
