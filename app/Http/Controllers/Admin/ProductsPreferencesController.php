<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductsPreferenceRequest;
use App\Http\Requests\StoreProductsPreferenceRequest;
use App\Http\Requests\UpdateProductsPreferenceRequest;
use App\Models\Product;
use App\Models\ProductsPreference;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsPreferencesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('products_preference_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsPreferences = ProductsPreference::with(['product'])->get();

        return view('admin.productsPreferences.index', compact('productsPreferences'));
    }

    public function create()
    {
        abort_if(Gate::denies('products_preference_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productsPreferences.create', compact('products'));
    }

    public function store(StoreProductsPreferenceRequest $request)
    {
        $productsPreference = ProductsPreference::create($request->all());

        return redirect()->route('admin.products-preferences.index');
    }

    public function edit(ProductsPreference $productsPreference)
    {
        abort_if(Gate::denies('products_preference_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productsPreference->load('product');

        return view('admin.productsPreferences.edit', compact('products', 'productsPreference'));
    }

    public function update(UpdateProductsPreferenceRequest $request, ProductsPreference $productsPreference)
    {
        $productsPreference->update($request->all());

        return redirect()->route('admin.products-preferences.index');
    }

    public function show(ProductsPreference $productsPreference)
    {
        abort_if(Gate::denies('products_preference_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsPreference->load('product', 'preferenceProductsPreferencesValues');

        return view('admin.productsPreferences.show', compact('productsPreference'));
    }

    public function destroy(ProductsPreference $productsPreference)
    {
        abort_if(Gate::denies('products_preference_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsPreference->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductsPreferenceRequest $request)
    {
        ProductsPreference::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
