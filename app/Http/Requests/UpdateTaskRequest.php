<?php

namespace App\Http\Requests;

use App\Models\Task;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_edit');
    }

    public function rules()
    {
        return [
            'from_location' => [
                // 'numeric',
                'required',
            ],
            'to_location' => [
                // 'numeric',
                'different:from_location',
                'required',
            ],
            'billing_client' => [
                // 'numeric',
                'required',
            ],
            'driver_id' => [
                'numeric',
                'nullable',
            ],
            // 'time_of_visit' => [
            //     'numeric',
            //     'required',
            //     'gt:0',
            //     'lt:50',
            // ],
            // 'dropoff_time' => [
            //     'date',
            //     'required',
            //     'after:pickup_time',
            // ],
            // 'pickup_time' => [
            //     'date',
            //     'required',
            //     'after:now',
            // ],
            'task_type' => [
                'required',
            ],
           
        ];
    }
}
