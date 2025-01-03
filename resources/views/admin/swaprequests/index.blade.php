@extends('layouts.master')
@section('content')
    @can('swaprequest_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.swaprequests.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.swaprequest.title_singular') }}
                </a>
            </div>
        </div>
    @endcan

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
                                <label for="task_id">Task ID</label>
                                <input class="form-control" type="text" name="task_id" id="task_id">
                            </div>
                        </div>



                        <div class="row">

                            <div class="col-lg-12 d-flex justify-content-between mt-2">
                                <button class="btn btn-danger" type="button" id="search">
                                    {{ trans('translation.search') }}
                                </button>

                            </div>


                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.swaprequest.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Swaprequest w-100">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.swaprequest.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.swaprequest.fields.task') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.status') }}
                        </th>
                        <th>
                            {{ trans('translation.swaprequest.fields.driver_a') }}
                        </th>
                        <th>
                            {{ trans('cruds.swaprequest.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('cruds.swaprequest.fields.status') }}
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
            @can('swaprequest_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.swaprequests.massDestroy') }}",
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

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                // ajax: "{{ route('admin.swaprequests.index') }}",

                ajax: {
                    url: "{{ route('admin.swaprequests.index') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.task_id = $('#task_id').val();
                        // d.delayed_reason = $('#delayed_reason').val();
                    }
                },
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'task_id',
                        name: 'task_id'
                    },
                    {
                        data: 'task.status',
                        name: 'task.status'
                    },
                    {
                        data: 'driverA',
                        name: 'driverA'
                    },
                    {
                        data: 'driver_name',
                        name: 'driver.name'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            };
            let table = $('.datatable-Swaprequest').DataTable(dtOverrideGlobals);
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
