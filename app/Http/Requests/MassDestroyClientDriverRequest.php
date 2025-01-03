<?php

namespace App\Http\Requests;

use App\Models\ClientDriver;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyClientDriverRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('client_driver_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:client_drivers,id',
        ];
    }
}
