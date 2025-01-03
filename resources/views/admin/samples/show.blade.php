@extends('layouts.master')
@section('title')
    @lang('translation.tasks')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.samples')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.show') }} {{ trans('translation.task.title_singular') }}
        </div>

        <div class="card-body" id="print_area">
            <div class="container-fluid">
                <div class="row justify-content-center pull-up  px-5">
                    <div class="col-md-12">
                        <div class="card">

                            <div class="card-body pt-3">
                                <table class="table table-bordered" border="1" cellspacing="0" cellpadding="3"
                                    width="100%">
                                    <tbody>
                                        <tr>
                                            <td>Sample</td>
                                            <td>{{ $sample->barcode_id }}</td>

                                            <td>Task </td>
                                            <td>{{ $sample->task_id }}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Pick Up Location</td>
                                            <td>{{ $task->from->name }}</td>
                                            <td>Delivery Location</td>
                                            <td>{{ $task->to->name }}</td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
