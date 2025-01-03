<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyClientAccountRequest;
use App\Http\Requests\StoreClientAccountRequest;
use App\Http\Requests\UpdateClientAccountRequest;
use App\Models\Client;
use App\Models\ClientAccount;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAccountsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('client_account_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientAccounts = ClientAccount::with(['client'])->get();

        return view('admin.clientAccounts.index', compact('clientAccounts'));
    }

    public function create()
    {
        abort_if(Gate::denies('client_account_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('status', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.clientAccounts.create', compact('clients'));
    }

    public function store(StoreClientAccountRequest $request)
    {
        $clientAccount = ClientAccount::create($request->all());

        return redirect()->route('admin.client-accounts.index');
    }

    public function edit(ClientAccount $clientAccount)
    {
        abort_if(Gate::denies('client_account_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('status', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $clientAccount->load('client');

        return view('admin.clientAccounts.edit', compact('clientAccount', 'clients'));
    }

    public function update(UpdateClientAccountRequest $request, ClientAccount $clientAccount)
    {
        $clientAccount->update($request->all());

        return redirect()->route('admin.client-accounts.index');
    }

    public function show(ClientAccount $clientAccount)
    {
        abort_if(Gate::denies('client_account_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientAccount->load('client');

        return view('admin.clientAccounts.show', compact('clientAccount'));
    }

    public function destroy(ClientAccount $clientAccount)
    {
        abort_if(Gate::denies('client_account_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientAccount->delete();

        return back();
    }

    public function massDestroy(MassDestroyClientAccountRequest $request)
    {
        ClientAccount::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
