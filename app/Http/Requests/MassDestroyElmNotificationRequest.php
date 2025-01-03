<?php

namespace App\Http\Requests;

use App\Models\ElmNotification;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyElmNotificationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('elm_notification_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:elm_notifications,id',
        ];
    }
}
