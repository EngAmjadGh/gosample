@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.apiAyenati.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ApiAyenati">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.apiAyenati.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.apiAyenati.fields.api_url') }}
                        </th>
                        <th>
                            {{ trans('cruds.apiAyenati.fields.response') }}
                        </th>
                        <th>
                            {{ trans('cruds.apiAyenati.fields.response_flag') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.api-ayenatis.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'api_url',
                        name: 'api_url'
                    },
                    {
                        data: 'response',
                        name: 'response'
                    },
                    {
                        data: 'response_flag',
                        name: 'response_flag'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            };
            let table = $('.datatable-ApiAyenati').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
