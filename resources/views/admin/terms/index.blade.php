@extends('layouts.master')
@section('title')
    @lang('translation.terms')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.terms')
        @endslot
    @endcomponent
    
@can('term_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.terms.create') }}">
                {{ trans('translation.add') }} {{ trans('translation.term.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('translation.term.title_singular') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Term">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.term.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.term.fields.english_text') }}
                        </th>
                        <th>
                            {{ trans('translation.term.fields.arabic_text') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terms as $key => $term)
                        <tr data-entry-id="{{ $term->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $term->id ?? '' }}
                            </td>
                            <td>
                                {{ $term->english_text ?? '' }}
                            </td>
                            <td>
                                {{ $term->arabic_text ?? '' }}
                            </td>
                            <td>
                                @can('term_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.terms.show', $term->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('term_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.terms.edit', $term->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('term_delete')
                                    <form action="{{ route('admin.terms.destroy', $term->id) }}" method="POST" onsubmit="return confirm('{{ trans('translation.areYouSure') }}');" style="display: inline-block;">
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
@can('term_delete')
  let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.terms.massDestroy') }}",
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
  let table = $('.datatable-Term:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection