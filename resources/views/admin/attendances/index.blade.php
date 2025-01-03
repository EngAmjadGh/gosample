@extends('layouts.master')
@section('title')
    @lang('translation.attendances')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.attendances')
        @endslot
    @endcomponent
@can('attendance_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.attendances.create') }}">
                {{ trans('translation.add') }} {{ trans('translation.attendance') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('translation.attendance') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Attendance">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.attendance.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.attendance.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('translation.driver.fields.mobile') }}
                        </th>
                        <th>
                            {{ trans('translation.attendance.fields.checkin_time') }}
                        </th>
                        <th>
                            {{ trans('translation.attendance.fields.checkout_time') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $key => $attendance)
                        <tr data-entry-id="{{ $attendance->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $attendance->id ?? '' }}
                            </td>
                            <td>
                                {{ $attendance->driver->name ?? '' }}
                            </td>
                            <td>
                                {{ $attendance->driver->mobile ?? '' }}
                            </td>
                            <td>
                                {{ $attendance->checkin_time ?? '' }}
                            </td>
                            <td>
                                {{ $attendance->checkout_time ?? '' }}
                            </td>
                            <td>
                                @can('attendance_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.attendances.show', $attendance->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('attendance_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.attendances.edit', $attendance->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('attendance_delete')
                                    <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('attendance_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.attendances.massDestroy') }}",
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
  let table = $('.datatable-Attendance:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection