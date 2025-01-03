@extends('layouts.admin')
@section('content')
@can('car_link_history_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.car-link-histories.create') }}">
                {{ trans('translation.add') }} {{ trans('cruds.carLinkHistory.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.carLinkHistory.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-CarLinkHistory">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.car') }}
                        </th>
                        <th>
                            {{ trans('cruds.carLinkHistory.fields.action') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carLinkHistories as $key => $carLinkHistory)
                        <tr data-entry-id="{{ $carLinkHistory->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $carLinkHistory->id ?? '' }}
                            </td>
                            <td>
                                {{ $carLinkHistory->driver->name ?? '' }}
                            </td>
                            <td>
                                {{ $carLinkHistory->car->imei ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\CarLinkHistory::ACTION_SELECT[$carLinkHistory->action] ?? '' }}
                            </td>
                            <td>
                                @can('car_link_history_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.car-link-histories.show', $carLinkHistory->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('car_link_history_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.car-link-histories.edit', $carLinkHistory->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('car_link_history_delete')
                                    <form action="{{ route('admin.car-link-histories.destroy', $carLinkHistory->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('car_link_history_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.car-link-histories.massDestroy') }}",
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
  let table = $('.datatable-CarLinkHistory:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection