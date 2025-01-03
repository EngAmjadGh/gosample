<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductsPreferencesValueRequest;
use App\Http\Requests\StoreProductsPreferencesValueRequest;
use App\Http\Requests\UpdateProductsPreferencesValueRequest;
use App\Models\ProductsPreference;
use App\Models\ProductsPreferencesValue;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsPreferencesValuesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('products_preferences_value_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsPreferencesValues = ProductsPreferencesValue::with(['preference'])->get();

        return view('admin.productsPreferencesValues.index', compact('productsPreferencesValues'));
    }

    public function create()
    {
        abort_if(Gate::denies('products_preferences_value_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $preferences = ProductsPreference::pluck('title_en', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productsPreferencesValues.create', compact('preferences'));
    }

    public function store(StoreProductsPreferencesValueRequest $request)
    {
        $productsPreferencesValue = ProductsPreferencesValue::create($request->all());

        return redirect()->route('admin.products-preferences-values.index');
    }

    public function edit(ProductsPreferencesValue $productsPreferencesValue)
    {
        abort_if(Gate::denies('products_preferences_value_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $preferences = ProductsPreference::pluck('title_en', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productsPreferencesValue->load('preference');

        return view('admin.productsPreferencesValues.edit', compact('preferences', 'productsPreferencesValue'));
    }

    public function update(UpdateProductsPreferencesValueRequest $request, ProductsPreferencesValue $productsPreferencesValue)
    {
        $productsPreferencesValue->update($request->all());

        return redirect()->route('admin.products-preferences-values.index');
    }

    public function show(ProductsPreferencesValue $productsPreferencesValue)
    {
        abort_if(Gate::denies('products_preferences_value_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsPreferencesValue->load('preference');

        return view('admin.productsPreferencesValues.show', compact('productsPreferencesValue'));
    }

    public function destroy(ProductsPreferencesValue $productsPreferencesValue)
    {
        abort_if(Gate::denies('products_preferences_value_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productsPreferencesValue->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductsPreferencesValueRequest $request)
    {
        ProductsPreferencesValue::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
