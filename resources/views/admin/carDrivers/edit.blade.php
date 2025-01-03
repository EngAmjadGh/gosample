@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.edit') }} {{ trans('cruds.carDriver.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.car-drivers.update", [$carDriver->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="car_id">{{ trans('cruds.carDriver.fields.car') }}</label>
                <select class="form-control select2 {{ $errors->has('car') ? 'is-invalid' : '' }}" name="car_id" id="car_id" required>
                    @foreach($cars as $id => $entry)
                        <option value="{{ $id }}" {{ (old('car_id') ? old('car_id') : $carDriver->car->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('car'))
                    <div class="invalid-feedback">
                        {{ $errors->first('car') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.carDriver.fields.car_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="driver_id">{{ trans('cruds.carDriver.fields.driver') }}</label>
                <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}" name="driver_id" id="driver_id" required>
                    @foreach($drivers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $carDriver->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('driver'))
                    <div class="invalid-feedback">
                        {{ $errors->first('driver') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.carDriver.fields.driver_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="is_linked">{{ trans('cruds.carDriver.fields.is_linked') }}</label>
                <input class="form-control {{ $errors->has('is_linked') ? 'is-invalid' : '' }}" type="number" name="is_linked" id="is_linked" value="{{ old('is_linked', $carDriver->is_linked) }}" step="1" required>
                @if($errors->has('is_linked'))
                    <div class="invalid-feedback">
                        {{ $errors->first('is_linked') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.carDriver.fields.is_linked_helper') }}</span>
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