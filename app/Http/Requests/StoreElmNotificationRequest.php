<?php

namespace App\Http\Requests;

use App\Models\ElmNotification;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreElmNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('elm_notification_create');
    }

    public function rules()
    {
        return [];
    }
}
