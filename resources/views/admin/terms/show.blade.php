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
        {{ trans('translation.show') }} {{ trans('translation.term.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.terms.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.term.fields.id') }}
                        </th>
                        <td>
                            {{ $term->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.term.fields.english_text') }}
                        </th>
                        <td>
                            {{ $term->english_text }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.term.fields.arabic_text') }}
                        </th>
                        <td>
                            {{ $term->arabic_text }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.terms.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection