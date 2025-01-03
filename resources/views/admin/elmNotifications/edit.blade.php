@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('translation.edit') }} {{ trans('cruds.elmNotification.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.elm-notifications.update", [$elmNotification->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('translation.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection