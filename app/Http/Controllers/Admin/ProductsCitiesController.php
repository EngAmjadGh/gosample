<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductsCityRequest;
use App\Http\Requests\StoreProductsCityRequest;
use App\Http\Requests\UpdateProductsCityRequest;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductsCity;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsCitiesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('products_city_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsCities = ProductsCity::with(['city', 'product'])->get();

        return view('admin.productsCities.index', compact('productsCities'));
    }

    public function create()
    {
        abort_if(Gate::denies('products_city_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = Product::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productsCities.create', compact('cities', 'products'));
    }

    public function store(StoreProductsCityRequest $request)
    {
        $productsCity = ProductsCity::create($request->all());

        return redirect()->route('admin.products-cities.index');
    }

    public function edit(ProductsCity $productsCity)
    {
        abort_if(Gate::denies('products_city_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = Product::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productsCity->load('city', 'product');

        return view('admin.productsCities.edit', compact('cities', 'products', 'productsCity'));
    }

    public function update(UpdateProductsCityRequest $request, ProductsCity $productsCity)
    {
        $productsCity->update($request->all());

        return redirect()->route('admin.products-cities.index');
    }

    public function show(ProductsCity $productsCity)
    {
        abort_if(Gate::denies('products_city_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsCity->load('city', 'product');

        return view('admin.productsCities.show', compact('productsCity'));
    }

    public function destroy(ProductsCity $productsCity)
    {
        abort_if(Gate::denies('products_city_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsCity->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductsCityRequest $request)
    {
        ProductsCity::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
