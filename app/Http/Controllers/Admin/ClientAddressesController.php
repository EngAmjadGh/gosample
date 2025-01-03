<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyClientAddressRequest;
use App\Http\Requests\StoreClientAddressRequest;
use App\Http\Requests\UpdateClientAddressRequest;
use App\Models\Client;
use App\Models\ClientAddress;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAddressesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('client_address_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientAddresses = ClientAddress::with(['client'])->get();

        return view('admin.clientAddresses.index', compact('clientAddresses'));
    }

    public function create()
    {
        abort_if(Gate::denies('client_address_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.clientAddresses.create', compact('clients'));
    }

    public function store(StoreClientAddressRequest $request)
    {
        $clientAddress = ClientAddress::create($request->all());

        return redirect()->route('admin.client-addresses.index');
    }

    public function edit(ClientAddress $clientAddress)
    {
        abort_if(Gate::denies('client_address_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clientAddress->load('client');

        return view('admin.clientAddresses.edit', compact('clientAddress', 'clients'));
    }

    public function update(UpdateClientAddressRequest $request, ClientAddress $clientAddress)
    {
        $clientAddress->update($request->all());

        return redirect()->route('admin.client-addresses.index');
    }

    public function show(ClientAddress $clientAddress)
    {
        abort_if(Gate::denies('client_address_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientAddress->load('client');

        return view('admin.clientAddresses.show', compact('clientAddress'));
    }

    public function destroy(ClientAddress $clientAddress)
    {
        abort_if(Gate::denies('client_address_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientAddress->delete();

        return back();
    }

    public function massDestroy(MassDestroyClientAddressRequest $request)
    {
        ClientAddress::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
