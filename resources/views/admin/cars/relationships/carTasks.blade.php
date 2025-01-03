<div class="card">
    <!-- <div class="card-header">
        {{ trans('translation.tasks') }} {{ trans('translation.list') }}
    </div> -->

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-carTasks">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.task.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.from_location') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.to_location') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.billing_client') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.driver') }}
                        </th>

                        <th>
                            {{ trans('translation.task.fields.status') }}
                        </th>

                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $key => $task)
                        <tr data-entry-id="{{ $task->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $task->id ?? '' }}
                            </td>
                            <td>
                                {{ $task->from_location->name ?? '' }}
                            </td>
                            <td>
                                {{ $task->to_location->name ?? '' }}
                            </td>
                            <td>
                                {{ $task->billing_client->name ?? '' }}
                            </td>
                            <td>
                                {{ $task->driver->name ?? '' }}
                            </td>

                            <td>
                                {{ App\Models\Task::STATUS_SELECT[$task->status] ?? '' }}
                            </td>

                            <td>
                                @can('task_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.tasks.show', $task->id) }}">
                                        {{ trans('translation.view') }}
                                    </a>
                                @endcan

                                @can('task_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.tasks.edit', $task->id) }}">
                                        {{ trans('translation.edit') }}
                                    </a>
                                @endcan

                                @can('task_delete')
                                    <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST"
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

@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('task_delete')
                let deleteButtonTrans = '{{ trans('translation.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.tasks.massDestroy') }}",
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
                //   dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-carTasks:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
