@extends('layouts.master')
@section('title')
    @lang('translation.driverSchedules')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.driverSchedules')
        @endslot
    @endcomponent
@can('driver_schedule_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.driver-schedules.create') }}">
                {{ trans('translation.add') }} {{ trans('translation.driverSchedule.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('translation.driverSchedule.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-DriverSchedule">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.driverSchedule.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.driverSchedule.fields.from_location') }}
                        </th>
                        <th>
                            {{ trans('translation.driverSchedule.fields.to_location') }}
                        </th>
                        <th>
                            {{ trans('translation.driverSchedule.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('translation.driverSchedule.fields.note') }}
                        </th>
                        <th>
                            {{ trans('translation.driverSchedule.fields.plate_number') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($driverSchedules as $key => $driverSchedule)
                        <tr data-entry-id="{{ $driverSchedule->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $driverSchedule->id ?? '' }}
                            </td>
                            <td>
                                {{ $driverSchedule->from->name ?? '' }}
                            </td>
                            <td>
                                {{ $driverSchedule->to->name ?? '' }}
                            </td>
                            <td>
                                {{ $driverSchedule->driver->name ?? '' }}
                            </td>
                            <td>
                                {{ $driverSchedule->note ?? '' }}
                            </td>
                            <td>
                                {{ $driverSchedule->plate_number ?? '' }}
                            </td>
                            <td>
                                @can('driver_schedule_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.driver-schedules.show', $driverSchedule->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('driver_schedule_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.driver-schedules.edit', $driverSchedule->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('driver_schedule_delete')
                                    <form action="{{ route('admin.driver-schedules.destroy', $driverSchedule->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('translation.delete') }}">
                                    </form>
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
@can('driver_schedule_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.driver-schedules.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('translation.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('translation.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-DriverSchedule:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection