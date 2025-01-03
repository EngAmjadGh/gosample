<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyContainerRequest;
use App\Http\Requests\StoreContainerRequest;
use App\Http\Requests\UpdateContainerRequest;
use App\Models\Car;
use App\Models\Container;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContainersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('container_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $containers = Container::with(['car'])->get();

        return view('admin.containers.index', compact('containers'));
    }

    public function create()
    {
        abort_if(Gate::denies('container_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cars = Car::pluck('plate_number', 'id')->prepend(trans('translation.pleaseSelect'), '');

        return view('admin.containers.create', compact('cars'));
    }

    public function store(StoreContainerRequest $request)
    {
        $container = Container::create($request->all());

        return redirect()->route('admin.containers.index');
    }

    public function edit(Container $container)
    {
        abort_if(Gate::denies('container_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cars = Car::pluck('plate_number', 'id')->prepend(trans('translation.pleaseSelect'), '');

        $container->load('car');

        return view('admin.containers.edit', compact('cars', 'container'));
    }

    public function update(UpdateContainerRequest $request, Container $container)
    {
        $container->update($request->all());

        return redirect()->route('admin.containers.index');
    }

    public function show(Container $container)
    {
        abort_if(Gate::denies('container_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $container->load('car');

        return view('admin.containers.show', compact('container'));
    }

    public function destroy(Container $container)
    {
        abort_if(Gate::denies('container_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $container->delete();

        return back();
    }

    public function massDestroy(MassDestroyContainerRequest $request)
    {
        Container::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
