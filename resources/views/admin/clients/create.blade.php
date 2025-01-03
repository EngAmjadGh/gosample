@extends('layouts.master')
@section('title')
    @lang('translation.clients')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.clients')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.create') }} {{ trans('translation.client') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.clients.store') }}" enctype="multipart/form-data">
                @csrf


                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="arabic_name">{{ trans('translation.client.fields.arabic_name') }}</label>
                            <input class="form-control {{ $errors->has('arabic_name') ? 'is-invalid' : '' }}" type="text"
                                name="arabic_name" id="arabic_name" value="{{ old('arabic_name', '') }}">
                            @if ($errors->has('arabic_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('arabic_name') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="english_name">{{ trans('translation.client.fields.english_name') }}</label>
                            <input class="form-control {{ $errors->has('english_name') ? 'is-invalid' : '' }}"
                                type="text" name="english_name" id="english_name" value="{{ old('english_name', '') }}">
                            @if ($errors->has('english_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('english_name') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="email">{{ trans('translation.client.fields.email') }}</label>
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text"
                                name="email" id="email" value="{{ old('email', '') }}">
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="address">{{ trans('translation.client.fields.address') }}</label>
                            <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text"
                                name="address" id="address" value="{{ old('address', '') }}">
                            @if ($errors->has('address'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('address') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">{{ trans('translation.client.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Client::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', 1) === (string) $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="logo">{{ trans('translation.client.fields.logo') }}</label>
                            <input type="file" name="logo" id="inputImage"
                                class="form-control @error('logo') is-invalid @enderror" required>
                            @if ($errors->has('logo'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('logo') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="drivers">{{ trans('cruds.driver.title_singular') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all"
                                    style="border-radius: 0">{{ trans('translation.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all"
                                    style="border-radius: 0">{{ trans('translation.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2 {{ $errors->has('drivers') ? 'is-invalid' : '' }}"
                                name="drivers[]" id="drivers" multiple required>
                                @foreach ($drivers as $id => $driver)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('drivers', [])) ? 'selected' : '' }}>
                                        {{ $driver }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('drivers'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('drivers') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">

                        <div class="form-group">
                            <label class="required" for="locations">{{ trans('cruds.sample.fields.location') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all"
                                    style="border-radius: 0">{{ trans('translation.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all"
                                    style="border-radius: 0">{{ trans('translation.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2 {{ $errors->has('locations') ? 'is-invalid' : '' }}"
                                name="locations[]" id="locations" multiple required>
                                @foreach ($locations as $id => $location)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('locations', [])) ? 'selected' : '' }}>{{ $location }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('locations'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('locations') }}
                                </div>
                            @endif
                        </div>
                    </div>
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

@section('scripts')
    <script>
        Dropzone.options.logoDropzone = {
            url: '{{ route('admin.clients.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').find('input[name="logo"]').remove()
                $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="logo"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($client) && $client->logo)
                    var file = {!! json_encode($client->logo) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
