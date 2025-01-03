@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.show') }} {{ trans('cruds.barcode.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.barcodes.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.barcode.fields.id') }}
                        </th>
                        <td>
                            {{ $barcode->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barcode.fields.type') }}
                        </th>
                        <td>
                            {{ $barcode->type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barcode.fields.last_number') }}
                        </th>
                        <td>
                            {{ $barcode->last_number }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.barcodes.index') }}">
                    {{ trans('translation.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection