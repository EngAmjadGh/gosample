@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.elmNotification.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ElmNotification">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.elmNotification.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.elmNotification.fields.task') }}
                        </th>
                        <th>
                            {{ trans('cruds.elmNotification.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.elmNotification.fields.response_body') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($elmNotifications as $key => $elmNotification)
                        <tr data-entry-id="{{ $elmNotification->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $elmNotification->id ?? '' }}
                            </td>
                            <td>
                                {{ $elmNotification->task->collect_lat ?? '' }}
                            </td>
                            <td>
                                {{ $elmNotification->type ?? '' }}
                            </td>
                            <td>
                                {{ $elmNotification->response_body ?? '' }}
                            </td>
                            <td>
                                @can('elm_notification_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.elm-notifications.show', $elmNotification->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan



                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  
  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-ElmNotification:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection