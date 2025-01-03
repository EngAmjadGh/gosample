@extends('layouts.master')
@section('title')
    @lang('translation.tasks')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.tasks')
        @endslot
    @endcomponent




    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Filters</h4>
                </div>
                <div class="card-body">
                <form method="POST" action="{{ route("tasksdashboard.search") }}" enctype="multipart/form-data">
                @csrf
                        <div class="row">
                            <!-- <div class="col-lg-4">
                                <label for="billing_client">{{ trans('translation.task.fields.billing_client') }}</label>
                                <select class="form-control select2" name="billing_client" id="billing_client">
                                    <option value="">Select Client</option>
                                    @foreach($clients as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->english_name }}</option>
                                    @endforeach    
                                </select>
                            </div> -->
                            <div class="col-lg-4">
                                <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                                <select class="form-control select2" name="driver_id" id="driver_id">
                                    <option value="">Select Driver</option>    
                                    @foreach($drivers as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="required" for="date_from">{{ trans('translation.task.fields.date_from') }}</label>
                                <input class="form-control" type="date" name="date_from" id="date_from">
                            </div>
                            <div class="col-lg-4">
                                <label class="required" for="date_to">{{ trans('translation.task.fields.date_to') }}</label>
                                <input class="form-control" type="date" name="date_to" id="date_to">
                            </div>
                        </div>
                        <div class="row">
                        <!-- <div class="col-lg-4">
                                <label class="required" for="status">{{ trans('translation.task.fields.status') }}</label>
                                <select class="form-control" name="status" id="statuss" >
                                <option value="">Select Status</option>    
                                <option value="NEW">NEW</option>    
                                <option value="COLLECTED">COLLECTED</option>    
                                <option value="IN_FREEZER">IN_FREEZER</option>    
                                <option value="OUT_FREEZER">OUT_FREEZER</option>    
                                <option value="CLOSED">CLOSED</option>    
                                <option value="NO_SAMPLES">NO_SAMPLES</option>    
                                </select>

                            </div> -->
                        <div class="col-lg-4">
                                <label for="from_location">{{ trans('translation.task.fields.from_location') }}</label>
                                <select class="form-control select2" name="from_location" id="from_location">
                                    <option value="">Select Location</option>    
                                    @foreach($locations as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach   
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                                <select class="form-control select2" name="to_location" id="to_location">
                                    <option value="">Select Location</option>  
                                    @foreach($locations as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach     
                                </select>
                            </div>
                           
                            
                            <div class="col-lg-6">
                                <p></p>
                                <button class="btn btn-danger" type="submit" id="search">
                                    {{ trans('translation.search') }}
                                </button>
                            </div>
                           
                        </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12 " >
            <div class="row h-100">
               

                <div class="col-xl-12">
                <div class="card card-height-100">
                                <div class="card-header  {{(App::isLocale('ar') ? 'text-end' : '')}}">
                                    <h4 class="card-title mb-0">@lang('translation.Tasks_Clients')</h4>
                                </div><!-- end card header -->

                                <div class="card-body ">
                                @apexchartsScripts
                                {!! $chart->container() !!}
                                {!! $chart->script() !!}
                                    <!-- <div id="grouped_bar" data-colors='["--vz-primary", "--vz-success"]' data='[2,2]' class="apex-charts" dir="ltr"></div> -->
                                </div><!-- end card-body -->
                            </div><!-- end card -->
                  
                            
                </div> <!-- end col-->

            </div> <!-- end row-->
        </div><!-- end col -->

@endsection