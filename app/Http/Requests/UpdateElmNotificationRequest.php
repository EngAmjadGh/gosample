<?php

namespace App\Http\Requests;

use App\Models\ElmNotification;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateElmNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('elm_notification_edit');
    }

    public function rules()
    {
        return [];
    }
}
