<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBarcodeRequest;
use App\Http\Requests\StoreBarcodeRequest;
use App\Http\Requests\UpdateBarcodeRequest;
use App\Models\Barcode;
use Gate;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarcodesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('barcode_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $barcodes = Barcode::all();

        return view('admin.barcodes.index', compact('barcodes'));
    }

    public function create()
    {
        abort_if(Gate::denies('barcode_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.barcodes.create');
    }

    public function generate()
    {
        abort_if(Gate::denies('barcode_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $show = false;
        $type = 'bag';
        $start = DB::table('barcodes')->where('type','sample')->max('last_number') + 1;
        $sequence = 10;
        return view('admin.barcodes.generate',compact('type','start','sequence','show')
        );
    }
    public function generateBarcodes(Request $request)
    {
        abort_if(Gate::denies('barcode_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $show = true;
        $type = $request->type;
        $start = DB::table('barcodes')->where('type',$type)->max('last_number') + 1;
        $sequence = $request->range? $request->range :10;

        // update last number 
        $record = Barcode::where('type',$request->type)->first();
        $record->last_number =  $record->last_number +  $request->range;
        $record->save();

        return view('admin.barcodes.generate',compact('type','start','sequence','show')
        
        
        );
    }

    public function store(StoreBarcodeRequest $request)
    {
        $barcode = Barcode::create($request->all());

        return redirect()->route('admin.barcodes.index');
    }

    public function edit(Barcode $barcode)
    {
        abort_if(Gate::denies('barcode_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.barcodes.edit', compact('barcode'));
    }

    public function update(UpdateBarcodeRequest $request, Barcode $barcode)
    {
        $barcode->update($request->all());

        return redirect()->route('admin.barcodes.index');
    }

    public function show(Barcode $barcode)
    {
        abort_if(Gate::denies('barcode_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.barcodes.show', compact('barcode'));
    }

    public function destroy(Barcode $barcode)
    {
        abort_if(Gate::denies('barcode_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $barcode->delete();

        return back();
    }

    public function massDestroy(MassDestroyBarcodeRequest $request)
    {
        Barcode::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
