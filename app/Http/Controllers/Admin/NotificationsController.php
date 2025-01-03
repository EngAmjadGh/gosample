<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('notification_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Notifications::with(['task', 'from_location', 'to_location', 'driver', 'billing_client'])->select(sprintf('notifications.*', (new Notifications())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'notification_show';
                $editGate = 'notification_edit';
                $deleteGate = 'notification_delete';
                $crudRoutePart = 'notifications';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('task_id', function ($row) {
                return $row->task ? $row->task->id : '';
            });

            $table->addColumn('from_location_name', function ($row) {
                return $row->from_location ? $row->fromLocation->name : '';
            });

            $table->addColumn('to_location_name', function ($row) {
                return $row->to_location ? $row->toLocation->name : '';
            });

            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->addColumn('billing_client_english_name', function ($row) {
                return $row->billing_client ? $row->billingClient->english_name : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : '';
            });
            $table->editColumn('notifiable_type', function ($row) {
                return $row->notifiable_type ? $row->notifiable_type : '';
            });
            $table->editColumn('notifiable', function ($row) {
                return $row->notifiable ? $row->notifiable : '';
            });
            $table->editColumn('data', function ($row) {
                return $row->data ? $row->data : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'task', 'from_location', 'to_location', 'driver', 'billing_client']);

            return $table->make(true);
        }

        return view('admin.notifications.index');
    }

    public function show(Notifications $notification)
    {
        abort_if(Gate::denies('notification_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $notification->load('task', 'from_location', 'to_location', 'driver', 'billing_client');

        return view('admin.notifications.show', compact('notification'));
    }
}
