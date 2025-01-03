@extends('layouts.master')
@section('title')
    @lang('translation.locations')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.locations')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('translation.location') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.locations.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.id') }}
                        </th>
                        <td>
                            {{ $location->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.name') }}
                        </th>
                        <td>
                            {{ $location->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.arabic_name') }}
                        </th>
                        <td>
                            {{ $location->arabic_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.description') }}
                        </th>
                        <td>
                            {{ $location->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.lat') }}
                        </th>
                        <td>
                            {{ $location->lat }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.lng') }}
                        </th>
                        <td>
                            {{ $location->lng }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.mobile') }}
                        </th>
                        <td>
                            {{ $location->mobile }}
                        </td>
                    </tr>
                    <tr>
                                        <td id='barcode_{{$location->id}}' class='text-center barcode d-none' colspan='3'>
                                            <img src="{{asset('assets/img/mtc_logo.jpg')}}" alt="">
                                            <h1>{{$location->name}}</h1>
                                            <div>
                                                {!! DNS1D::getBarcodeSVG($location->id.'-location', 'C128',5,200) !!}
                                            </div>

                                        </td>
                                    </tr>
                    <tr>
                        <th>
                            {{ trans('translation.location.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Location::STATUS_SELECT[$location->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.locations.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>

                 <a role="button"  class="text-info" onclick="printReport({{$location->id}})">
                                            <i class="mdi mdi-printer text-info" style="font-size: 20px;"></i>  </a>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header">
        {{ trans('translation.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#locations_clients" role="tab" data-toggle="tab">
                {{ trans('translation.client') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="locations_clients">
            @includeIf('admin.locations.relationships.locationsClients', ['clients' => $location->locationsClients])
        </div>
    </div>
</div>

@endsection



<script>

    function printReport(id)
    {


        var prtContent = document.getElementById("barcode_"+id);
        var WinPrint = window.open();
        WinPrint.document.write(
        `<div id='barcode_area' style='width:100%;margin-top:50px;margin:0 auto; text-align:center'>
        <style>@page { margin: 0; } body  {
    padding-top: 10rem;
  }</style>`

        +prtContent.innerHTML+
        `</div>`
        );
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();

    };

   
</script>





