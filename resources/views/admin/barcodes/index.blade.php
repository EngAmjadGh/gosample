@extends('layouts.master')
@section('content')
@can('barcode_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.barcodes.create') }}">
                {{ trans('translation.add') }} {{ trans('cruds.barcode.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.barcode.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Barcode">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.barcode.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.barcode.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.barcode.fields.last_number') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barcodes as $key => $barcode)
                        <tr data-entry-id="{{ $barcode->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $barcode->id ?? '' }}
                            </td>
                            <td>
                                {{ $barcode->type ?? '' }}
                            </td>
                            <td>
                                {{ $barcode->last_number ?? '' }}
                            </td>
                            <td>
                                @can('barcode_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.barcodes.show', $barcode->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('barcode_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.barcodes.edit', $barcode->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('barcode_delete')
                                    <form action="{{ route('admin.barcodes.destroy', $barcode->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('barcode_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.barcodes.massDestroy') }}",
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
  let table = $('.datatable-Barcode:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection