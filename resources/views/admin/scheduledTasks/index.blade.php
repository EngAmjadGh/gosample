@extends('layouts.master')
@section('content')
    @can('scheduled_task_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.scheduled-tasks.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.scheduledTask.title_singular') }}
                </a>
                <a class="btn btn-primary" href="{{ route('admin.scheduled-tasks.quick') }}">
                    Add Quick Schedule Task
                </a>
            </div>
        </div>
    @endcan
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="#">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">{{ trans('global.date_from') }}</label>
                            <input class="form-control date-range-picker" data-provider="flatpickr" data-date-format="Y-m-d"
                                type="text" name="date_from" id="date_from" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">{{ trans('global.date_to') }}</label>
                            <input class="form-control date-range-picker" data-provider="flatpickr" data-date-format="Y-m-d"
                                type="text" name="date_to" id="date_to" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                        <select class="form-control select2" name="driver_id" id="driver_id">
                            <option value="">Select Driver</option>
                            @foreach ($drivers as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="from_location">{{ trans('translation.task.fields.from_location') }}</label>
                        <select class="form-control select2" name="from_location" id="from_location">
                            <option value="">Select Location</option>
                            @foreach ($locations as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                        <select class="form-control select2" name="to_location" id="to_location">
                            <option value="">Select Location</option>
                            @foreach ($locations as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="client_id">{{ trans('translation.task.fields.billing_client') }}</label>
                        <select class="form-control select2" name="client_id" id="client_id">
                            <option value="">Select Client</option>
                            @foreach ($clients as $id => $entry)
                                <option value="{{ $entry->id }}">{{ $entry->english_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 text-right mt-2">
                        <button type="button" id="search" name="search"
                            class="btn btn-primary">{{ trans('global.search') }}</button>

                        <button type="reset" id="reset" name="reset"
                            class="btn btn-secondary">{{ trans('global.reset') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.scheduledTask.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table
                class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ScheduledTask w-100">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.task.fields.sequence') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.start_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.end_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.from_location') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.to_location') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.client') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.selected_hour') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.task_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.days') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.added_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.scheduledTask.fields.driver') }}
                        </th>
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
            @can('scheduled_task_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.scheduled-tasks.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).data(), function(entry) {
                            return entry.id
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
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
                // dtButtons.push(deleteButton)
            @endcan
            var scheduleRoute = "{{ route('admin.scheduled-tasks.show', ['__ID__']) }}";
            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                searching: false,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.scheduled-tasks.index') }}",
                    data: function(d) {
                        d.client_id = $("#client_id option:selected").val();
                        d.to_location = $("#to_location option:selected").val();
                        d.driver_id = $("#driver_id option:selected").val();
                        d.from_location = $("#from_location option:selected").val();
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'from_location_name',
                        name: 'from_location.name'
                    },
                    {
                        data: 'to_location_name',
                        name: 'to_location.name'
                    },
                    {
                        data: 'client_status',
                        name: 'client.name'
                    },
                    {
                        data: 'selected_hour',
                        name: 'selected_hour'
                    },
                    {
                        data: 'task_type',
                        name: 'task_type'
                    },
                    {
                        data: 'day',
                        name: 'day'
                    },
                    {
                        data: 'added_by',
                        name: 'added_by'
                    },
                    {
                        data: 'driver_name',
                        name: 'driver.name'
                    },
                    { data: 'id', name: 'action',title:'action',render: function (data,type,row)
                        {
                            var url = scheduleRoute.replace('__ID__', row['id']);
                            return '<a href="' + url + '" id="delete-address"  class="btn btn-primary btn-sm">View</a>';
                        }
                    }/*,

                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }*/
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            };
            let table = $('.datatable-ScheduledTask').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            $("#search").click(function() {
                // alert("button");
                table.draw();
            });

        });
    </script>
@endsection
