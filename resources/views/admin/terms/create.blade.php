@extends('layouts.master')
@section('title')
    @lang('translation.terms')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.terms')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.create') }} {{ trans('translation.term.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.terms.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="english_text">{{ trans('translation.term.fields.english_text') }}</label>
                <textarea class="form-control {{ $errors->has('english_text') ? 'is-invalid' : '' }}" name="english_text" id="english_text">{{ old('english_text') }}</textarea>
                @if($errors->has('english_text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('english_text') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.term.fields.english_text_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="arabic_text">{{ trans('translation.term.fields.arabic_text') }}</label>
                <textarea class="form-control {{ $errors->has('arabic_text') ? 'is-invalid' : '' }}" name="arabic_text" id="arabic_text">{{ old('arabic_text') }}</textarea>
                @if($errors->has('arabic_text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('arabic_text') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('translation.term.fields.arabic_text_helper') }}</span>
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