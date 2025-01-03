<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyMoneyTransferRequest;
use App\Http\Requests\StoreMoneyTransferRequest;
use App\Http\Requests\UpdateMoneyTransferRequest;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Location;
use App\Models\MoneyTransfer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MoneyTransferController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('money_transfer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MoneyTransfer::with(['driver', 'client', 'from_location', 'to_location'])->select(sprintf('%s.*', (new MoneyTransfer)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'money_transfer_show';
                $editGate      = 'money_transfer_edit';
                $deleteGate    = 'money_transfer_delete';
                $crudRoutePart = 'money-transfers';

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
            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->addColumn('client_english_name', function ($row) {
                return $row->client ? $row->client->english_name : '';
            });

            $table->addColumn('from_location_name', function ($row) {
                return $row->from_location ? $row->from_location->name : '';
            });

            $table->addColumn('to_location_name', function ($row) {
                return $row->to_location ? $row->to_location->name : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? MoneyTransfer::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('from_location_otp', function ($row) {
                return $row->from_location_otp ? $row->from_location_otp : '';
            });
            $table->editColumn('to_otp', function ($row) {
                return $row->to_otp ? $row->to_otp : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'driver', 'client', 'from_location', 'to_location']);

            return $table->make(true);
        }

        return view('admin.moneyTransfers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('money_transfer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.moneyTransfers.create', compact('clients', 'drivers', 'from_locations', 'to_locations'));
    }

    public function store(StoreMoneyTransferRequest $request)
    {
        $moneyTransfer = new MoneyTransfer();
        $moneyTransfer->client_id = $request->client_id;
        $moneyTransfer->driver_id = $request->driver_id;
        $moneyTransfer->from_location_id = $request->from_location_id;
        $moneyTransfer->to_location_id = $request->to_location_id;
        $moneyTransfer->amount = $request->amount;
        $moneyTransfer->status = 'new';
        $moneyTransfer->from_location_otp = $moneyTransfer->generateOtp(); // returns a 4-digit OTP as a string
        $moneyTransfer->to_location_otp = $moneyTransfer->generateOtp(); // returns a 4-digit OTP as a string
        $moneyTransfer->save();
        return redirect()->route('admin.money-transfers.index');
    }

    public function edit(MoneyTransfer $moneyTransfer)
    {
        abort_if(Gate::denies('money_transfer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $from_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $to_locations = Location::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $moneyTransfer->load('driver', 'client', 'from_location', 'to_location');

        return view('admin.moneyTransfers.edit', compact('clients', 'drivers', 'from_locations', 'moneyTransfer', 'to_locations'));
    }

    public function update(UpdateMoneyTransferRequest $request, MoneyTransfer $moneyTransfer)
    {
        // $moneyTransfer->update($request->all());

        $moneyTransfer->client_id = $request->client_id;
        $moneyTransfer->driver_id = $request->driver_id;
        $moneyTransfer->from_location_id = $request->from_location_id;
        $moneyTransfer->to_location_id = $request->to_location_id;
        $moneyTransfer->status = $request->status;
        $moneyTransfer->amount = $request->amount;

        $moneyTransfer->save();

        return redirect()->route('admin.money-transfers.index');
    }

    public function show(MoneyTransfer $moneyTransfer)
    {
        abort_if(Gate::denies('money_transfer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moneyTransfer->load('driver', 'client', 'from_location', 'to_location');

        return view('admin.moneyTransfers.show', compact('moneyTransfer'));
    }

    public function destroy(MoneyTransfer $moneyTransfer)
    {
        abort_if(Gate::denies('money_transfer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $moneyTransfer->delete();

        return back();
    }

    public function massDestroy(MassDestroyMoneyTransferRequest $request)
    {
        $moneyTransfers = MoneyTransfer::find(request('ids'));

        foreach ($moneyTransfers as $moneyTransfer) {
            $moneyTransfer->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
