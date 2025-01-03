<?php

namespace App\Http\Requests;

use App\Models\CarLinkHistory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCarLinkHistoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('car_link_history_create');
    }

    public function rules()
    {
        return [
            'car_id' => [
                'required',
                'integer',
            ],
            'action' => [
                'required',
            ],
        ];
    }
}
