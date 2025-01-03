@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.moneyTransfer.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.money-transfers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="driver_id">{{ trans('cruds.moneyTransfer.fields.driver') }}</label>
                    <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}" name="driver_id"
                        id="driver_id" required>
                        @foreach ($drivers as $id => $entry)
                            <option value="{{ $id }}" {{ old('driver_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('driver'))
                        <div class="invalid-feedback">
                            {{ $errors->first('driver') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.moneyTransfer.fields.driver_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="client_id">{{ trans('cruds.moneyTransfer.fields.client') }}</label>
                    <select class="form-control select2 {{ $errors->has('client') ? 'is-invalid' : '' }}" name="client_id"
                        id="client_id" required>
                        @foreach ($clients as $id => $entry)
                            <option value="{{ $id }}" {{ old('client_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('client'))
                        <div class="invalid-feedback">
                            {{ $errors->first('client') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.moneyTransfer.fields.client_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="from_location_id">{{ trans('cruds.moneyTransfer.fields.from_location') }}</label>
                    <select class="form-control select2 {{ $errors->has('from_location') ? 'is-invalid' : '' }}"
                        name="from_location_id" id="from_location_id" required>
                        @foreach ($from_locations as $id => $entry)
                            <option value="{{ $id }}" {{ old('from_location_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('from_location'))
                        <div class="invalid-feedback">
                            {{ $errors->first('from_location') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.moneyTransfer.fields.from_location_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="to_location_id">{{ trans('cruds.moneyTransfer.fields.to_location') }}</label>
                    <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}"
                        name="to_location_id" id="to_location_id">
                        @foreach ($to_locations as $id => $entry)
                            <option value="{{ $id }}" {{ old('to_location_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('to_location'))
                        <div class="invalid-feedback">
                            {{ $errors->first('to_location') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.moneyTransfer.fields.to_location_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="amount">{{ trans('cruds.moneyTransfer.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.moneyTransfer.fields.amount_helper') }}</span>
                </div>
                {{-- <div class="form-group">
                <label class="required">{{ trans('cruds.moneyTransfer.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach (App\Models\MoneyTransfer::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'new') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moneyTransfer.fields.status_helper') }}</span>
            </div> --}}
                {{-- <div class="form-group">
                <label class="required" for="from_location_otp">{{ trans('cruds.moneyTransfer.fields.from_location_otp') }}</label>
                <input class="form-control {{ $errors->has('from_location_otp') ? 'is-invalid' : '' }}" type="text" name="from_location_otp" id="from_location_otp" value="{{ old('from_location_otp', '') }}" required>
                @if ($errors->has('from_location_otp'))
                    <div class="invalid-feedback">
                        {{ $errors->first('from_location_otp') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moneyTransfer.fields.from_location_otp_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="to_otp">{{ trans('cruds.moneyTransfer.fields.to_otp') }}</label>
                <input class="form-control {{ $errors->has('to_otp') ? 'is-invalid' : '' }}" type="text" name="to_otp" id="to_otp" value="{{ old('to_otp', '') }}" required>
                @if ($errors->has('to_otp'))
                    <div class="invalid-feedback">
                        {{ $errors->first('to_otp') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.moneyTransfer.fields.to_otp_helper') }}</span>
            </div> --}}
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
