@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.edit') }} {{ trans('cruds.carLinkHistory.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.car-link-histories.update", [$carLinkHistory->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="driver_id">{{ trans('cruds.carLinkHistory.fields.driver') }}</label>
                <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}" name="driver_id" id="driver_id">
                    @foreach($drivers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $carLinkHistory->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('driver'))
                    <div class="invalid-feedback">
                        {{ $errors->first('driver') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.carLinkHistory.fields.driver_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="car_id">{{ trans('cruds.carLinkHistory.fields.car') }}</label>
                <select class="form-control select2 {{ $errors->has('car') ? 'is-invalid' : '' }}" name="car_id" id="car_id" required>
                    @foreach($cars as $id => $entry)
                        <option value="{{ $id }}" {{ (old('car_id') ? old('car_id') : $carLinkHistory->car->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('car'))
                    <div class="invalid-feedback">
                        {{ $errors->first('car') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.carLinkHistory.fields.car_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.carLinkHistory.fields.action') }}</label>
                <select class="form-control {{ $errors->has('action') ? 'is-invalid' : '' }}" name="action" id="action" required>
                    <option value disabled {{ old('action', null) === null ? 'selected' : '' }}>{{ trans('translation.pleaseSelect') }}</option>
                    @foreach(App\Models\CarLinkHistory::ACTION_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('action', $carLinkHistory->action) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('action'))
                    <div class="invalid-feedback">
                        {{ $errors->first('action') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.carLinkHistory.fields.action_helper') }}</span>
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