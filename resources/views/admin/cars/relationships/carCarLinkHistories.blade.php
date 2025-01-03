
<!-- <div class="card"> -->
    <!-- <div class="card-header">
        {{ trans('translation.carLinkHistory') }}
    </div> -->

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-carCarLinkHistories">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.carLinkHistory.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.carLinkHistory.fields.driver') }}
                        </th>
                        <th>
                            {{ trans('translation.carLinkHistory.fields.car') }}
                        </th>
                        <th>
                            {{ trans('translation.action') }}
                        </th>
                        <th>
                        {{ trans('translation.created_at') }}
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
                            {{ $carLinkHistory->car->created_at ?? '' }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
<!-- </div> -->

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
//   dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-carCarLinkHistories:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection