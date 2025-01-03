@extends('layouts.admin')
@section('content')
@can('client_location_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.client-locations.create') }}">
                {{ trans('translation.add') }} {{ trans('cruds.clientLocation.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.clientLocation.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ClientLocation">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.clientLocation.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientLocation.fields.client') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientLocation.fields.location') }}
                        </th>
                        
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientLocations as $key => $clientLocation)
                        <tr data-entry-id="{{ $clientLocation->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $clientLocation->id ?? '' }}
                            </td>
                            <td>
                                {{ $clientLocation->client->status ?? '' }}
                            </td>
                            <td>
                                {{ $clientLocation->location->name ?? '' }}
                            </td>
                            <td>
                                @can('client_location_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.client-locations.show', $clientLocation->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('client_location_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.client-locations.edit', $clientLocation->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('client_location_delete')
                                    <form action="{{ route('admin.client-locations.destroy', $clientLocation->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('client_location_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.client-locations.massDestroy') }}",
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
  let table = $('.datatable-ClientLocation:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection