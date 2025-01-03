@extends('layouts.master')
@section('content')
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
                        <div class="form-group">
                            <label for="email">{{ trans('cruds.sample.fields.barcode') }}</label>
                            <input class="form-control" type="text" name="barcode_id" id="barcode_id">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="confirmed_by_client">{{ trans('cruds.sample.fields.status') }}</label>
                            <select class="form-control select2" name="confirmed_by_client" id="confirmed_by_client">
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach (App\Models\Sample::RECEIVING_STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('confirmed_by_client') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
            {{ trans('cruds.sample.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Sample">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.sample.fields.id') }}
                        </th>

                        <th>
                            {{ trans('cruds.sample.fields.location') }}
                        </th>
                        <th>
                            TO LOCATION
                        </th>
                        <th>
                            {{ trans('cruds.sample.fields.task') }}
                        </th>
                        <th>
                            {{ trans('cruds.sample.fields.barcode') }}
                        </th>
                        <th>
                            DRIVER
                        </th>
                        <th>
                            COLLECTION DATE
                        </th>
                        <th>
                            CLOSE DATE
                        </th>
                        <th>
                            {{ trans('cruds.sample.fields.confirmed_by_client') }}
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
            @can('sample_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.samples.massDestroy') }}",
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
                //   dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                searching: false,
                // ajax: "{{ route('admin.samples.index') }}",

                ajax: {
                    url: "{{ route('admin.samples.index') }}",
                    data: function(d) {
                        d.confirmed_by_client = $("#confirmed_by_client option:selected").val();
                        d.barcode_id = $("#barcode_id").val();
                        d.task_id = $("#task_id").val();
                        // d.task_id = $("#task_id option:selected").val();
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
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
                        data: 'location_name',
                        name: 'location.name'
                    },
                    {
                        data: 'to_location',
                        name: 'to_location'
                    },
                    {
                        data: 'task_id',
                        name: 'task_id'
                    },
                    {
                        data: 'barcode_id',
                        name: 'barcode_id'
                    },
                    {
                        data: 'driver_id',
                        name: 'driver_id'
                    },
                    {
                        data: 'collection_date',
                        name: 'collection_date'
                    },

                    {
                        data: 'close_date',
                        name: 'close_date'
                    },



                    {
                        data: 'confirmed_by_client',
                        name: 'confirmed_by_client'
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
            let table = $('.datatable-Sample').DataTable(dtOverrideGlobals);
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
