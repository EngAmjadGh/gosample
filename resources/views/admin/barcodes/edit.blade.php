@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.edit') }} {{ trans('cruds.barcode.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.barcodes.update", [$barcode->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="type">{{ trans('cruds.barcode.fields.type') }}</label>
                <input class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" type="text" name="type" id="type" value="{{ old('type', $barcode->type) }}" required>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.barcode.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="last_number">{{ trans('cruds.barcode.fields.last_number') }}</label>
                <input class="form-control {{ $errors->has('last_number') ? 'is-invalid' : '' }}" type="number" name="last_number" id="last_number" value="{{ old('last_number', $barcode->last_number) }}" step="1">
                @if($errors->has('last_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('last_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.barcode.fields.last_number_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('translation.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection