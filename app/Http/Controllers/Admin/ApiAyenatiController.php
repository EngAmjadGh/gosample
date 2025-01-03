<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiAyenati;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ApiAyenatiController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('api_ayenati_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ApiAyenati::query()->select(sprintf('%s.*', (new ApiAyenati)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'api_ayenati_show';
                $editGate      = 'api_ayenati_edit';
                $deleteGate    = 'api_ayenati_delete';
                $crudRoutePart = 'api-ayenatis';

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
            $table->editColumn('api_url', function ($row) {
                return $row->api_url ? $row->api_url : '';
            });
            $table->editColumn('response', function ($row) {
                return $row->response ? $row->response : '';
            });
            $table->editColumn('response_flag', function ($row) {
                return $row->response_flag ? ApiAyenati::RESPONSE_FLAG_SELECT[$row->response_flag] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.apiAyenatis.index');
    }

    public function show(ApiAyenati $apiAyenati)
    {
        abort_if(Gate::denies('api_ayenati_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.apiAyenatis.show', compact('apiAyenati'));
    }
}
