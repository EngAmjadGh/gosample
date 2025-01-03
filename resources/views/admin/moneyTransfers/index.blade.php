@extends('layouts.master')
@section('content')
    @can('money_transfer_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.money-transfers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.moneyTransfer.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.moneyTransfer.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-MoneyTransfer">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.client') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.from_location') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.to_location') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.from_location_otp') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.to_otp') }}
                        </th>
                        <th>
                            {{ trans('cruds.moneyTransfer.fields.amount') }}
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
            @can('money_transfer_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.money-transfers.massDestroy') }}",
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
                dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.money-transfers.index') }}",
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
                        data: 'client_english_name',
                        name: 'client.english_name'
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
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'from_location_otp',
                        name: 'from_location_otp'
                    },
                    {
                        data: 'to_location_otp',
                        name: 'to_location_otp'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
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
            let table = $('.datatable-MoneyTransfer').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
