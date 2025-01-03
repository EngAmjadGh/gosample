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
                <form action="{{ route('admin.reportExport') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="required" for="keyword">{{ trans('translation.search') }}</label>
                                <input class="form-control" type="text" name="keyword" id="keyword">
                            </div>

                            <div class="col-lg-4">
                                <label class="required" for="status">{{ trans('translation.task.fields.status') }}</label>
                                <select class="form-control" name="status" id="statuss">
                                    <option value="">Select Status</option>
                                    <option value="NEW">NEW</option>
                                    <option value="COLLECTED">COLLECTED</option>
                                    <option value="IN_FREEZER">IN_FREEZER</option>
                                    <option value="OUT_FREEZER">OUT_FREEZER</option>
                                    <option value="CLOSED">CLOSED</option>
                                    <option value="NO_SAMPLES">NO_SAMPLES</option>
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
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="required"
                                    for="date_from">{{ trans('translation.task.fields.date_from') }}</label>
                                <input class="form-control" type="datetime-local" name="date_from" id="date_from">
                            </div>
                            <div class="col-lg-4">
                                <label class="required"
                                    for="date_to">{{ trans('translation.task.fields.date_to') }}</label>
                                <input class="form-control" type="datetime-local" name="date_to" id="date_to">
                            </div>
                            <div class="col-lg-4">
                                <label for="billing_client">{{ trans('translation.task.fields.billing_client') }}</label>
                                <select class="form-control select2" name="billing_client" id="billing_client">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->english_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="from_location">{{ trans('translation.task.fields.from_location') }}</label>
                                <select class="form-control select2" name="from_location" id="from_location">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                                <select class="form-control select2" name="to_location" id="to_location">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $id => $entry)
                                        <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-lg-4">
                                <label for="delayed_reason">{{ trans('translation.task.fields.delayed_reason') }}</label>
                                <select class="form-control" name="delayed_reason" id="delayed_reason">
                                    <option value="">Select Delay Reason</option>
                                    <option value="pickup_delayed">Pickup Off</option>
                                    <option value="dropoff_delayed">Drop Off</option>
                                </select>
                            </div> --}}
                            <div class="col-lg-4">
                                <label for="report_type">{{ trans('translation.export') }}</label>
                                <select class="form-control" name="report_type" id="report_type">
                                    {{-- <option value="">Select Report Type </option> --}}
                                    <option value="pdf">PDF</option>
                                    <option value="excel">EXCEL</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p></p>
                                <button class="btn btn-danger" type="button" id="search">
                                    {{ trans('translation.search') }}
                                </button>
                            </div>
                            <div class="col-lg-6">

                                <button class="btn btn-danger" type="submit" id="export">
                                    {{ trans('translation.export') }}
                                </button>



                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @can('task_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.tasks.create') }}">
                    {{ trans('translation.add') }} {{ trans('translation.tasks') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('translation.tasks') }} {{ trans('translation.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Task">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.task.fields.sequence') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.billing_client') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.from_location') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.to_location') }}
                        </th>

                        <!-- <th>
                                                                                                                                                                        {{ trans('translation.task.fields.car') }}
                                                                                                                                                                    </th>
                                                                                                                                                                    
                                                                                                                                                                    <th>
                                                                                                                                                                        {{ trans('translation.task.fields.type') }}
                                                                                                                                                                    </th> -->
                        <th>
                            {{ trans('translation.task.fields.task_type') }}
                        </th>

                        <th>
                            {{ trans('translation.task.fields.status') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.added_by') }}
                        </th>

                        <th>
                            {{ trans('translation.task.fields.close_date') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.collection_date') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.hours') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.freezer_date') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.freezer_out_date') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.delayed_reason') }}
                        </th>
                        <th>
                            {{ trans('translation.created_at') }}
                        </th>
                        <!-- <th>
                                                                                                                                                                        {{ trans('translation.task.fields.from_location_arrival_time') }}
                                                                                                                                                                    </th>
                                                                                                                                                                    <th>
                                                                                                                                                                        {{ trans('translation.task.fields.to_location_arrival_time') }}
                                                                                                                                                                    </th> -->
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('task_delete')
                let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.tasks.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).data(), function(entry) {
                            return entry.id
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('translation.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('translation.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.tasks.outfreezerdelayed') }}",
                    data: function(d) {
                        d.status = $("#statuss option:selected").val();
                        d.driver_id = $("#driver_id option:selected").val();
                        d.billing_client = $("#billing_client option:selected").val();
                        d.to_location = $("#to_location option:selected").val();
                        d.from_location = $("#from_location option:selected").val();
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.keyword = $('#keyword').val();
                        d.delayed_reason = $('#delayed_reason').val();
                    }
                },
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'sequence',
                        name: 'sequence'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'billing_client_status',
                        name: 'billing_client.name'
                    },
                    {
                        data: 'driver_name',
                        name: 'driver.name'
                    },
                    {
                        data: 'from_location_name',
                        name: 'from_location.name'
                    },
                    {
                        data: 'to_location_name',
                        name: 'to_location.name'
                    },

                    // { data: 'car_imei', name: 'car.imei' },
                    // { data: 'type', name: 'type' },
                    {
                        data: 'task_type',
                        name: 'task_type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'added_by',
                        name: 'added_by'
                    },

                    {
                        data: 'close_date',
                        name: 'close_date'
                    },
                    {
                        data: 'collection_date',
                        name: 'collection_date'
                    },
                    {
                        data: 'hours',
                        name: 'hours'
                    },
                    {
                        data: 'freezer_date',
                        name: 'freezer_date'
                    },
                    {
                        data: 'freezer_out_date',
                        name: 'freezer_out_date'
                    },
                    {
                        data: 'delayed_reason',
                        name: 'delayed_reason'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    // { data: 'from_location_arrival_time', name: 'from_location_arrival_time' },
                    // { data: 'to_location_arrival_time', name: 'to_location_arrival_time' },
                    {
                        data: 'actions',
                        name: '{{ trans('translation.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [16, 'desc']
                ],
                pageLength: 100,
            };
            let table = $('.datatable-Task').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });




            $("#search").click(function() {
                // alert("button");
                table.draw();
            });
            $("#export1").click(function() {
                // alert('export');
                var d = {};
                d.status = $("#statuss option:selected").val();
                d.driver_id = $("#driver_id option:selected").val();
                d.billing_client = $("#billing_client option:selected").val();
                d.to_location = $("#to_location option:selected").val();
                d.from_location = $("#from_location option:selected").val();
                d.date_from = $("#date_from").val();
                d.date_to = $("#date_to").val();
                d.keyword = $('#keyword').val();
                d.delayed_reason = $('#delayed_reason').val();
                // alert(JSON.stringify(d));

                $.ajax({
                    type: "POST",
                    url: "/api/task/report/export",
                    data: JSON.stringify(d),
                    dataType: 'json',
                    contentType: "application/json; charset=utf-8",
                }).done(function(data, textStatus, xhr) {
                    BatchSamples = [];
                    if (data.status) {
                        alert('succss');
                    } else {
                        alert('succss');
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    BatchSamples = [];
                    alert('f');
                });
            });
        });
    </script>
@endsection
