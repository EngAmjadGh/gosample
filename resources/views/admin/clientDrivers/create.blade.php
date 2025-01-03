
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
    @section('css')
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->

<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" /> -->
@endsection
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.clientDriver.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.client-drivers.store") }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="required" for="driver_id">{{ trans('cruds.clientDriver.fields.driver') }}</label>

                <select class="form-control select2 {{ $errors->has('drivers') ? 'is-invalid' : '' }}" name="drivers[]" id="drivers" multiple required>
                    @foreach($drivers as $id => $role)
                        <option value="{{ $id }}" {{ in_array($id, old('drivers', [])) ? 'selected' : '' }}>{{ $drive }}</option>
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
                        <option value="{{ $id }}" {{ old('client_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
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

@section('script')

<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.select2').select2({})
})
</script> -->

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
       
       <script>
         $(document).ready(function() {
             // Select2 Multiple
             $('.select2-multiple').select2({
                 placeholder: "Select",
                 allowClear: true
             });

            
 
         });
 
     </script>
@endsection