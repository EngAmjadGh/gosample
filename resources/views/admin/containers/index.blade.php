@extends('layouts.master')
@section('title')
    @lang('translation.containers')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.containers')
        @endslot
    @endcomponent

    @can('container_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.containers.create') }}">
                    {{ trans('translation.add') }} {{ trans('translation.container.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('translation.container.title_singular') }} {{ trans('translation.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Container w-100">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>

                            <th width="10">

                                {{ trans('translation.task.fields.sequence') }}
                            </th>
                            <th>
                                {{ trans('translation.container.fields.id') }}
                            </th>
                            <th>
                                {{ trans('translation.container.fields.car') }}
                            </th>
                            <th>
                                {{ trans('translation.container.fields.sensor') }}
                            </th>
                            <th>
                                {{ trans('translation.container.fields.type') }}
                            </th>
                            <th>
                                {{ trans('translation.container.fields.description') }}
                            </th>
                            <th>
                                {{ trans('translation.container.fields.status') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($containers as $key => $container)
                            <tr data-entry-id="{{ $container->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    {{ $container->id ?? '' }}
                                </td>
                                <td>
                                    {{ $container->car->plate_number ?? '' }}
                                </td>
                                <td>
                                    {{ $container->imei ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\Container::TYPE_SELECT[$container->type] ?? '' }}
                                </td>
                                <td>
                                    {{ $container->description ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\Container::STATUS_SELECT[$container->status] ?? '' }}
                                </td>
                                <td>
                                    @can('container_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.containers.show', $container->id) }}">
                                            {{ trans('translation.view') }}
                                        </a>
                                    @endcan

                                    @can('container_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.containers.edit', $container->id) }}">
                                            {{ trans('translation.edit') }}
                                        </a>
                                    @endcan

                                    @can('container_delete')
                                        <form action="{{ route('admin.containers.destroy', $container->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('translation.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger"
                                                value="{{ trans('translation.delete') }}">
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
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('container_delete')
                let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.containers.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('translation.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('translation.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                // dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'asc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Container:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
