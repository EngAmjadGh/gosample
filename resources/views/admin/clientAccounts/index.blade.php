@extends('layouts.admin')
@section('content')
@can('client_account_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.client-accounts.create') }}">
                {{ trans('translation.add') }} {{ trans('cruds.clientAccount.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.clientAccount.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ClientAccount">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.clientAccount.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientAccount.fields.client') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientAccount.fields.username') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientAccount.fields.password') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientAccount.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.clientAccount.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientAccounts as $key => $clientAccount)
                        <tr data-entry-id="{{ $clientAccount->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $clientAccount->id ?? '' }}
                            </td>
                            <td>
                                {{ $clientAccount->client->status ?? '' }}
                            </td>
                            <td>
                                {{ $clientAccount->username ?? '' }}
                            </td>
                            <td>
                                {{ $clientAccount->password ?? '' }}
                            </td>
                            <td>
                                {{ $clientAccount->name ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\ClientAccount::STATUS_SELECT[$clientAccount->status] ?? '' }}
                            </td>
                            <td>
                                @can('client_account_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.client-accounts.show', $clientAccount->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('client_account_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.client-accounts.edit', $clientAccount->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('client_account_delete')
                                    <form action="{{ route('admin.client-accounts.destroy', $clientAccount->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('client_account_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.client-accounts.massDestroy') }}",
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
  let table = $('.datatable-ClientAccount:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection