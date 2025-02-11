<?php

namespace App\Http\Requests;

use App\Models\Task;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreShipmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_create');
    }

    public function rules()
    {
        return [
            'from_location' => [
                'required',
            ],
            'to_location' => [
                'different:from_location',
                'required',
            ],
            'task' => [
                'required',
            ],
            'driver_id' => [
                'numeric',
                'required',
            ],
            'batch' => [
                'required',
            ],
            'sender_name' => [
                'nullable',
            ],
            'sender_long' => [
                'nullable',
            ],
            'sender_lat' => [
                'nullable',
            ],
            'sender_mobile' => [
                'nullable',
            ],
            'receiver_name' => [
                'nullable',
            ],
            'receiver_long' => [
                'nullable',
            ],
            'receiver_lat' => [
                'nullable',
            ],
            'receiver_mobile' => [
                'nullable',
            ],
            'carrier' => [
                'nullable',
            ],
            'reference_number' => [
                'nullable',
            ],
            
        ];
    }
}
