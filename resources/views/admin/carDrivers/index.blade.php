@extends('layouts.admin')
@section('content')
@can('car_driver_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.car-drivers.create') }}">
                {{ trans('translation.add') }} {{ trans('cruds.carDriver.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.carDriver.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-CarDriver">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.carDriver.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.carDriver.fields.car') }}
                        </th>
                        <th>
                            {{ trans('cruds.carDriver.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('cruds.carDriver.fields.is_linked') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carDrivers as $key => $carDriver)
                        <tr data-entry-id="{{ $carDriver->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $carDriver->id ?? '' }}
                            </td>
                            <td>
                                {{ $carDriver->car->imei ?? '' }}
                            </td>
                            <td>
                                {{ $carDriver->driver->name ?? '' }}
                            </td>
                            <td>
                                {{ $carDriver->is_linked ?? '' }}
                            </td>
                            <td>
                                @can('car_driver_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.car-drivers.show', $carDriver->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('car_driver_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.car-drivers.edit', $carDriver->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('car_driver_delete')
                                    <form action="{{ route('admin.car-drivers.destroy', $carDriver->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('car_driver_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.car-drivers.massDestroy') }}",
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
  let table = $('.datatable-CarDriver:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection