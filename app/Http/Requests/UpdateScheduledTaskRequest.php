<?php

namespace App\Http\Requests;

use App\Models\ScheduledTask;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateScheduledTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('scheduled_task_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'start_date' => [
                'required',
                // 'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                // 'date_format:' . config('panel.date_format'),
            ],
            'from_location_id' => [
                'required',
                'integer',
            ],
            'to_location_id' => [
                'required',
                'integer',
            ],
            'client_id' => [
                'required',
                'integer',
            ],
            'task_type' => [
                'required',
            ],
            // 'added_by' => [
            //     'string',
            //     'nullable',
            // ],
        ];
    }
}
