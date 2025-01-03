<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyBankRequest;
use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;
use App\Models\Bank;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class BankController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('bank_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $banks = Bank::with(['media'])->get();

        return view('admin.banks.index', compact('banks'));
    }

    public function create()
    {
        abort_if(Gate::denies('bank_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.banks.create');
    }

    public function store(StoreBankRequest $request)
    {
        $bank = Bank::create($request->all());

        if ($request->input('icon', false)) {
            $bank->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $bank->id]);
        }

        return redirect()->route('admin.banks.index');
    }

    public function edit(Bank $bank)
    {
        abort_if(Gate::denies('bank_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.banks.edit', compact('bank'));
    }

    public function update(UpdateBankRequest $request, Bank $bank)
    {
        $bank->update($request->all());

        if ($request->input('icon', false)) {
            if (!$bank->icon || $request->input('icon') !== $bank->icon->file_name) {
                if ($bank->icon) {
                    $bank->icon->delete();
                }
                $bank->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
            }
        } elseif ($bank->icon) {
            $bank->icon->delete();
        }

        return redirect()->route('admin.banks.index');
    }

    public function show(Bank $bank)
    {
        abort_if(Gate::denies('bank_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.banks.show', compact('bank'));
    }

    public function destroy(Bank $bank)
    {
        abort_if(Gate::denies('bank_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bank->delete();

        return back();
    }

    public function massDestroy(MassDestroyBankRequest $request)
    {
        Bank::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('bank_create') && Gate::denies('bank_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Bank();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
