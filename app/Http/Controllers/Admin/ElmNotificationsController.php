<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElmNotification;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ElmNotificationsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('elm_notification_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $elmNotifications = ElmNotification::with(['task'])->get();

        return view('admin.elmNotifications.index', compact('elmNotifications'));
    }

    public function show(ElmNotification $elmNotification)
    {
        abort_if(Gate::denies('elm_notification_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $elmNotification->load('task');

        return view('admin.elmNotifications.show', compact('elmNotification'));
    }
}
