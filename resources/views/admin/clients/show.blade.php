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
        {{ trans('translation.show') }} {{ trans('translation.client') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.clients.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.id') }}
                        </th>
                        <td>
                            {{ $client->id }}
                        </td>
                    </tr>
                    
                    
                   
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.arabic_name') }}
                        </th>
                        <td>
                            {{ $client->arabic_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.english_name') }}
                        </th>
                        <td>
                            {{ $client->english_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.email') }}
                        </th>
                        <td>
                            {{ $client->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.address') }}
                        </th>
                        <td>
                            {{ $client->address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Client::STATUS_SELECT[$client->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.client.fields.logo') }}
                        </th>
                        <td>
                            @if($client->logo)
                            <a href="{{ $client->logo }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $client->logo }}">
                                    </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.clients.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection