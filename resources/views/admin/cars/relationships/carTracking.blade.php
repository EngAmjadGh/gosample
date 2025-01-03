<div class="card">
    <!-- <div class="card-header">
        {{ trans('translation.carTracking') }} {{ trans('translation.list') }}
    </div> -->

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-carTracking">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('translation.task.fields.id') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.address') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.temp5') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.temp6') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.temp7') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.temp8') }}
                        </th>
                        <th>
                            {{ trans('translation.task.fields.created_at') }}
                        </th>
                        {{-- 
                        <th>
                            &nbsp;
                        </th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($carTracking as $key => $record)
                        <tr data-entry-id="{{ $record->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $record->id ?? '' }}
                            </td>
                            <td>
                                <a href=" https://www.google.com/maps/place/{{ $record->lat . ',' . $record->lng }}">
                                    <button value="copy" class="btn btn-xs btn-info">Location</button>
                                </a>

                            </td>
                            <td>
                                {{ $record->temp5 ?? '' }}
                            </td>
                            <td>
                                {{ $record->temp6 ?? '' }}
                            </td>
                            <td>
                                {{ $record->temp7 ?? '' }}
                            </td>
                            <td>
                                {{ $record->temp8 ?? '' }}
                            </td>
                            <td>
                                {{ $record->created_at ?? '' }}
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
            let table = $('.datatable-carTracking:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
