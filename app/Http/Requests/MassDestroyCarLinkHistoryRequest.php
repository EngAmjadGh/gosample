<?php

namespace App\Http\Requests;

use App\Models\CarLinkHistory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCarLinkHistoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('car_link_history_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:car_link_histories,id',
        ];
    }
}
