@extends('layouts.master')
@section('title')
    Unused Tasks
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            Unused Tasks
        @endslot
    @endcomponent


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Filters</h4>
                </div>
                <form>
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="client_id">{{ trans('translation.task.fields.billing_client') }}</label>
                                <select class="form-control select2" name="client_id" id="client_id">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->english_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                                <select class="form-control select2" name="driver_id" id="driver_id">
                                    <option value="">Select Driver</option>
                                    @foreach ($drivers as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <label class="required"
                                       for="date_from">{{ trans('translation.task.fields.date_from') }}</label>
                                <input class="form-control" type="datetime-local" name="date_from" id="date_from">
                            </div>
                            <div class="col-lg-4">
                                <label class="required"
                                       for="date_from">{{ trans('translation.task.fields.date_to') }}</label>
                                <input class="form-control" type="datetime-local" name="date_to" id="date_to">
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            {{ trans('translation.tasks') }} {{ trans('translation.list') }}
        </div>

        <div class="card-body">
            <table style="width: 100%" class=" table table-bordered table-striped table-hover ajaxTable datatable" id="datatable-un-used-tasks">

            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>

    <script>
        $(document).ready(function () {
        var unusedTaskTable = $('#datatable-un-used-tasks').DataTable({

            serverSide: true,
            responsive: true,
            stateSave: true,
            "ordering": false,
            "processing": true,
            ajax: {
                url: "{{ route('admin.tasks.unused') }}",
                data: function (d) {
                    d.driver_id = $("#driver_id option:selected").val();
                    d.client_id = $("#client_id option:selected").val();
                    d.date_from = $("#date_from").val();
                    d.date_to = $("#date_to").val();
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    title:'ID',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title:'Order Date',
                },
                {
                    data: 'english_name',
                    name: 'english_name',
                    title:'Client',
                    render: function ( data, type, row, meta ) {
                        if(data != null) return data;
                        return '';
                    }
                },
                {
                    data: 'dname',
                    name: 'dname',
                    title:'Driver',
                    render: function ( data, type, row, meta ) {
                        if(data != null) return data;
                        return '';
                    }
                },
                {
                    data: 'from_name',
                    name: 'from_name',
                    title:'From Location',
                    render: function ( data, type, row, meta ) {
                        if(data != null) return data;
                        return '';
                    }
                },
                {
                    data: 'to_name',
                    name: 'to_name',
                    title:'To Location',
                    render: function ( data, type, row, meta ) {
                        if(data != null) return data;
                        return '';
                    }
                }
            ],
            order: [
                [3, 'desc']
            ],
            pageLength: 100,
        });
        $("#client_id").change(function(){
            unusedTaskTable.draw();
        });
        $("#driver_id").change(function(){
            unusedTaskTable.draw();
        });
        $("#date_from").change(function(){
            unusedTaskTable.draw();
        });
        $("#date_to").change(function(){
            unusedTaskTable.draw();
        });
        });
    </script>
@endsection
