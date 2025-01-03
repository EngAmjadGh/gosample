@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.notification.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Notification">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.task') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.from_location') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.to_location') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.driver') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.billing_client') }}
                    </th>
                    <!-- <th>
                        {{ trans('cruds.notification.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.notifiable_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.notifiable') }}
                    </th>
                    <th>
                        {{ trans('cruds.notification.fields.data') }}
                    </th> -->
                    <th>
                        {{ trans('cruds.notification.fields.read_at') }}
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  
  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.notifications.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'task_id', name: 'task.id',sorting:false },
{ data: 'from_location_name', name: 'from_location.name',sorting:false },
{ data: 'to_location_name', name: 'to_location.name' ,sorting:false},
{ data: 'driver_name', name: 'driver.name',sorting:false },
{ data: 'billing_client_english_name', name: 'billing_client.english_name',sorting:false },
// { data: 'type', name: 'type',sorting:false },
// { data: 'notifiable_type', name: 'notifiable_type',sorting:false },
// { data: 'notifiable', name: 'notifiable',sorting:false },
// { data: 'data', name: 'data',sorting:false },
{ data: 'read_at', name: 'read_at',sorting:true },
{ data: 'actions', name: '{{ trans('global.actions') }}' ,sorting:false}
    ],
    orderCellsTop: false,
    order: [[ 7, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-Notification').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection