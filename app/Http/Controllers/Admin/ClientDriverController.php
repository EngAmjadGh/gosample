<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyClientDriverRequest;
use App\Http\Requests\StoreClientDriverRequest;
use App\Http\Requests\UpdateClientDriverRequest;
use App\Models\Client;
use App\Models\ClientDriver;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientDriverController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('client_driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientDrivers = ClientDriver::with(['driver', 'client'])->get();

        return view('admin.clientDrivers.index', compact('clientDrivers'));
    }

    public function create()
    {
        abort_if(Gate::denies('client_driver_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.clientDrivers.create', compact('clients', 'drivers'));
    }

    public function store(StoreClientDriverRequest $request)
    {
        $clientDriver = ClientDriver::create($request->all());

        return redirect()->route('admin.client-drivers.index');
    }

    public function edit(ClientDriver $clientDriver)
    {
        abort_if(Gate::denies('client_driver_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('english_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clientDriver->load('driver', 'client');

        return view('admin.clientDrivers.edit', compact('clientDriver', 'clients', 'drivers'));
    }

    public function update(UpdateClientDriverRequest $request, ClientDriver $clientDriver)
    {
        $clientDriver->update($request->all());

        return redirect()->route('admin.client-drivers.index');
    }

    public function show(ClientDriver $clientDriver)
    {
        abort_if(Gate::denies('client_driver_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientDriver->load('driver', 'client');

        return view('admin.clientDrivers.show', compact('clientDriver'));
    }

    public function destroy(ClientDriver $clientDriver)
    {
        abort_if(Gate::denies('client_driver_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientDriver->delete();

        return back();
    }

    public function massDestroy(MassDestroyClientDriverRequest $request)
    {
        ClientDriver::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
