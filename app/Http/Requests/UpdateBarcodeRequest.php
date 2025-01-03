<?php

namespace App\Http\Requests;

use App\Models\Barcode;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBarcodeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('barcode_edit');
    }

    public function rules()
    {
        return [
            'type' => [
                'string',
                'required',
            ],
            'last_number' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
