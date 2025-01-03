@extends('layouts.master')
@section('content')
    @can('car_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.cars.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.car.title_singular') }}
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
                                <label for="imei">{{ trans('cruds.car.fields.imei') }}</label>
                                <input class="form-control" type="text" name="imei" id="imei">
                            </div>
                            <div class="col-lg-4">
                                <label for="plate_number">{{ trans('cruds.car.fields.plate_number') }}</label>
                                <input class="form-control" type="text" name="plate_number" id="plate_number">
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
            {{ trans('cruds.car.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Car w-100">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.car.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('cruds.driver.fields.mobile') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.imei') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.plate_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.model') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.color') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.contact_person') }}
                        </th>
                        <th>
                            {{ trans('cruds.car.fields.description') }}
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
            @can('car_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.cars.massDestroy') }}",
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
                ajax: {
                    url: "{{ route('admin.cars.index') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.imei = $('#imei').val();
                        d.plate_number = $('#plate_number').val();
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
                        data: 'driver_name',
                        name: 'driver.name'
                    },
                    {
                        data: 'driver.mobile',
                        name: 'driver.mobile'
                    },
                    {
                        data: 'imei',
                        name: 'imei'
                    },
                    {
                        data: 'plate_number',
                        name: 'plate_number'
                    },
                    {
                        data: 'model',
                        name: 'model'
                    },
                    {
                        data: 'color',
                        name: 'color'
                    },
                    {
                        data: 'contact_person',
                        name: 'contact_person'
                    },
                    {
                        data: 'description',
                        name: 'description'
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
            let table = $('.datatable-Car').DataTable(dtOverrideGlobals);
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
