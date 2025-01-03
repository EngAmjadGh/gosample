@extends('layouts.master')
@section('title')
    @lang('translation.coupons')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
        @lang('translation.appname')
        @endslot
        @slot('title')
        @lang('translation.audit-logs')
        @endslot
    @endcomponent

<div class="card">
    <div class="card-header">
        {{ trans('translation.auditLog') }} {{ trans('translation.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-AuditLog">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.description') }}
                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.subject_id') }}
                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.subject_type') }}
                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.user_id') }}
                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.host') }}
                        </th>
                        <th>
                            {{ trans('translation.auditLog.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditLogs as $key => $auditLog)
                        <tr data-entry-id="{{ $auditLog->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $auditLog->id ?? '' }}
                            </td>
                            <td>
                                {{ $auditLog->description ?? '' }}
                            </td>
                            <td>
                                {{ $auditLog->subject_id ?? '' }}
                            </td>
                            <td>
                                {{ $auditLog->subject_type ?? '' }}
                            </td>
                            <td>
                                {{ $auditLog->user_id ?? '' }}
                            </td>
                            <td>
                                {{ $auditLog->host ?? '' }}
                            </td>
                            <td>
                                {{ $auditLog->created_at ?? '' }}
                            </td>
                            <td>
                                @can('audit_log_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.audit-logs.show', $auditLog->id) }}">
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
  let table = $('.datatable-AuditLog:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection