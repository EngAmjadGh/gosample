<?php

namespace App\Http\Requests;

use App\Models\Barcode;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyBarcodeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('barcode_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:barcodes,id',
        ];
    }
}
