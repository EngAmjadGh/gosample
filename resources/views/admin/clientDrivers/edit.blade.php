
@extends('layouts.master')
@section('title')
    @lang('translation.drivers')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.drivers')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.clientDriver.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.client-drivers.update", [$clientDriver->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="driver_id">{{ trans('cruds.clientDriver.fields.driver') }}</label>
                <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}" name="driver_id" id="driver_id" required>
                    @foreach($drivers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $clientDriver->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('driver'))
                    <div class="invalid-feedback">
                        {{ $errors->first('driver') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientDriver.fields.driver_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="client_id">{{ trans('cruds.clientDriver.fields.client') }}</label>
                <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id" id="client_id" required>
                    @foreach($clients as $id => $entry)
                        <option value="{{ $id }}" {{ (old('client_id') ? old('client_id') : $clientDriver->client->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('client'))
                    <div class="invalid-feedback">
                        {{ $errors->first('client') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.clientDriver.fields.client_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection