<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyClientLocationRequest;
use App\Http\Requests\StoreClientLocationRequest;
use App\Http\Requests\UpdateClientLocationRequest;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Location;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientLocationController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('client_location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientLocations = ClientLocation::with(['client', 'location'])->get();

        return view('admin.clientLocations.index', compact('clientLocations'));
    }

    public function create()
    {
        abort_if(Gate::denies('client_location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('status', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.clientLocations.create', compact('clients', 'locations'));
    }

    public function store(StoreClientLocationRequest $request)
    {
        $clientLocation = ClientLocation::create($request->all());

        return redirect()->route('admin.client-locations.index');
    }

    public function edit(ClientLocation $clientLocation)
    {
        abort_if(Gate::denies('client_location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('status', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $locations = Location::pluck('name', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $clientLocation->load('client', 'location');

        return view('admin.clientLocations.edit', compact('clientLocation', 'clients', 'locations'));
    }

    public function update(UpdateClientLocationRequest $request, ClientLocation $clientLocation)
    {
        $clientLocation->update($request->all());

        return redirect()->route('admin.client-locations.index');
    }

    public function show(ClientLocation $clientLocation)
    {
        abort_if(Gate::denies('client_location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientLocation->load('client', 'location');

        return view('admin.clientLocations.show', compact('clientLocation'));
    }

    public function destroy(ClientLocation $clientLocation)
    {
        abort_if(Gate::denies('client_location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientLocation->delete();

        return back();
    }

    public function massDestroy(MassDestroyClientLocationRequest $request)
    {
        ClientLocation::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
