<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDeliveryTimeRequest;
use App\Http\Requests\StoreDeliveryTimeRequest;
use App\Http\Requests\UpdateDeliveryTimeRequest;
use App\Models\City;
use App\Models\DeliveryTime;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryTimesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('delivery_time_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryTimes = DeliveryTime::with(['city'])->get();

        return view('admin.deliveryTimes.index', compact('deliveryTimes'));
    }

    public function create()
    {
        abort_if(Gate::denies('delivery_time_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.deliveryTimes.create', compact('cities'));
    }

    public function store(StoreDeliveryTimeRequest $request)
    {
        $deliveryTime = DeliveryTime::create($request->all());

        return redirect()->route('admin.delivery-times.index');
    }

    public function edit(DeliveryTime $deliveryTime)
    {
        abort_if(Gate::denies('delivery_time_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $deliveryTime->load('city');

        return view('admin.deliveryTimes.edit', compact('cities', 'deliveryTime'));
    }

    public function update(UpdateDeliveryTimeRequest $request, DeliveryTime $deliveryTime)
    {
        $deliveryTime->update($request->all());

        return redirect()->route('admin.delivery-times.index');
    }

    public function show(DeliveryTime $deliveryTime)
    {
        abort_if(Gate::denies('delivery_time_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryTime->load('city');

        return view('admin.deliveryTimes.show', compact('deliveryTime'));
    }

    public function destroy(DeliveryTime $deliveryTime)
    {
        abort_if(Gate::denies('delivery_time_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryTime->delete();

        return back();
    }

    public function massDestroy(MassDestroyDeliveryTimeRequest $request)
    {
        DeliveryTime::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
