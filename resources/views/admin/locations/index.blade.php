@extends('layouts.master')
@section('content')
    @can('location_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.locations.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.location.title_singular') }}
                </a>
            </div>
        </div>
    @endcan

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Filters</h4>
                </div>
                <form action="{{ route('admin.reportExport') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="required"
                                    for="date_from">{{ trans('translation.task.fields.date_from') }}</label>
                                <input class="form-control" type="datetime-local" name="date_from" id="date_from">
                            </div>
                            <div class="col-lg-4">
                                <label class="required"
                                    for="date_to">{{ trans('translation.task.fields.date_to') }}</label>
                                <input class="form-control" type="datetime-local" name="date_to" id="date_to">
                            </div>

                        </div>



                        <div class="row">

                            <div class="col-lg-12 d-flex justify-content-between mt-2">
                                <button class="btn btn-danger" type="button" id="search">
                                    {{ trans('translation.search') }}
                                </button>

                            </div>


                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            {{ trans('cruds.location.title_singular') }} {{ trans('global.list') }}
        </div>




        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Location w-100">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.location.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.arabic_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.description') }}
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.lat') }}
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.lng') }}
                        </th>
                        <th>
                            Address
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.mobile') }}
                        </th>
                        <th>
                            {{ trans('cruds.location.fields.status') }}
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
            @can('location_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.locations.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).data(), function(entry) {
                            return entry.id
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
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

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.locations.index') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        // d.keyword = $('#keyword').val();
                        // d.delayed_reason = $('#delayed_reason').val();
                    }
                },
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'arabic_name',
                        name: 'arabic_name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'lat',
                        name: 'lat'
                    },
                    {
                        data: 'lng',
                        name: 'lng'
                    },
                    {
                        data: 'coordinates',
                        name: 'coordinates'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
            let table = $('.datatable-Location').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            $("#search").click(function() {
                // alert("button");
                table.draw();
            });

        });
    </script>

    <script>
        function copyToClipboard(id) {
            console.log(id);
            // document.getElementById(id).select();
            var text = document.getElementById(id);
            /* Prevent iOS keyboard from opening */
            text.readOnly = true;
            /* Change the input's type to text so its text becomes selectable */
            text.type = 'text';
            /* Select the text field */
            text.select();
            document.execCommand('copy');
            text.type = 'hidden';
        }
    </script>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').on('click', '.copy-coordinates-btn', function() {
                var lat = $(this).data('lat');
                var lng = $(this).data('lng');

                // Create a temporary textarea element to copy the coordinates
                var tempTextArea = document.createElement('textarea');
                tempTextArea.value = lat + ', ' + lng;
                document.body.appendChild(tempTextArea);
                tempTextArea.select();

                // Copy the coordinates to the clipboard
                document.execCommand('copy');

                // Remove the temporary textarea
                document.body.removeChild(tempTextArea);

                // Display a notification or alert to indicate successful copy
                // You can use a library like Toastr or implement your own notification
                // Example using Toastr:
                toastr.success('Coordinates copied to clipboard');
            });
        });
    </script>
@endpush
