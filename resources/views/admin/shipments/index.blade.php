@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.shipment.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Shipment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.carrier') }}
                        </th>
                        <th>
                            Location From
                        </th>
                        {{-- <th>
                            {{ trans('cruds.shipment.fields.sender_long') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.sender_lat') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.sender_mobile') }}
                        </th> --}}
                        <th>
                            Location To
                        </th>
                        {{-- <th>
                            {{ trans('cruds.shipment.fields.receiver_long') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.receiver_lat') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.receiver_mobile') }}
                        </th> --}}
                        <th>
                            {{ trans('cruds.shipment.fields.reference_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.pickup_otp') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.status_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.dropoff_otp') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.batch') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.journey_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.sla_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.task') }}
                        </th>
                        <th>
                            {{ trans('cruds.shipment.fields.created_at') }}
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

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.shipments.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'carrier',
                        name: 'carrier'
                    },
                    {
                        data: 'from_location',
                        name: 'from_location'
                    },
                    // {
                    //     data: 'sender_long',
                    //     name: 'sender_long'
                    // },
                    // {
                    //     data: 'sender_lat',
                    //     name: 'sender_lat'
                    // },
                    // {
                    //     data: 'sender_mobile',
                    //     name: 'sender_mobile'
                    // },
                    {
                        data: 'to_location',
                        name: 'to_location'
                    },
                    // {
                    //     data: 'receiver_long',
                    //     name: 'receiver_long'
                    // },
                    // {
                    //     data: 'receiver_lat',
                    //     name: 'receiver_lat'
                    // },
                    // {
                    //     data: 'receiver_mobile',
                    //     name: 'receiver_mobile'
                    // },
                    {
                        data: 'reference_number',
                        name: 'reference_number'
                    },
                    {
                        data: 'pickup_otp',
                        name: 'pickup_otp'
                    },
                    {
                        data: 'status_code',
                        name: 'status_code'
                    },
                    {
                        data: 'dropoff_otp',
                        name: 'dropoff_otp'
                    },
                    {
                        data: 'batch',
                        name: 'batch'
                    },
                    {
                        data: 'journey_type',
                        name: 'journey_type'
                    },
                    {
                        data: 'sla_code',
                        name: 'sla_code'
                    },
                    {
                        data: 'task_id',
                        name: 'task.task_id'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
            let table = $('.datatable-Shipment').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
