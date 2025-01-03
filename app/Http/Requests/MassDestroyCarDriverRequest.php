<?php

namespace App\Http\Requests;

use App\Models\CarDriver;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCarDriverRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('car_driver_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:car_drivers,id',
        ];
    }
}
