<?php

namespace App\Http\Requests;

use App\Models\DriverSchedule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDriverScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('driver_schedule_create');
    }

    public function rules()
    {
        return [
            'plate_number' => [
                'string',
                'nullable',
            ],
        ];
    }
}
