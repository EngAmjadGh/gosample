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
            @lang('translation.tasks')
        @endslot
    @endcomponent
    <!-- Add these in your layout's <head> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Add this before your page's closing </body> tag -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>




    <div class="card">
        <div class="card-header">
            {{ trans('translation.create') }} {{ trans('translation.task.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.tasks.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="from_location">{{ trans('translation.task.fields.from_location') }}</label>
                            <select class="form-control select2" name="from_location[]" id="from_location" multiple
                                required>
                                @foreach ($from_locations as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('from_location', [])) ? 'selected' : '' }}>{{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('from_location'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('from_location') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="to_location">{{ trans('translation.task.fields.to_location') }}</label>
                            <select class="form-control select2 {{ $errors->has('to_location') ? 'is-invalid' : '' }}"
                                name="to_location" id="to_location">
                                @foreach ($to_locations as $id => $entry)
                                    <option value="{{ $id }}" {{ old('to_location') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('to_location'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('to_location') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="billing_client">{{ trans('translation.task.fields.billing_client') }}</label>
                            <select class="form-control select2 {{ $errors->has('billing_client') ? 'is-invalid' : '' }}"
                                name="billing_client" id="billing_client">
                                @foreach ($billing_clients as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ old('billing_client') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('billing_client'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('billing_client') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="driver_id">{{ trans('translation.task.fields.driver') }}</label>
                            <select class="form-control select2 {{ $errors->has('driver') ? 'is-invalid' : '' }}"
                                name="driver_id" id="driver_id">
                                @foreach ($drivers as $id => $entry)
                                    <option value="{{ $id }}" {{ old('driver_id') == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('driver'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('driver') }}
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="col-6">
                        <div class="mb-3">
                            <label>{{ trans('translation.task.fields.type') }}</label>
                            <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type"
                                id="type">
                                <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Task::TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('type', 'one_time') === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('type') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end col-->
                    <!-- <div class="col-6">
                                                <div class="mb-3">
                                                <label class="required" for="close_hour">{{ trans('translation.task.fields.close_hour') }}</label>
                                                    <input class="form-control {{ $errors->has('close_hour') ? 'is-invalid' : '' }}" type="text" name="close_hour" id="close_hour" value="{{ old('close_hour', '2') }}" required>
                                                    @if ($errors->has('close_hour'))
    <div class="invalid-feedback">
                                                            {{ $errors->first('close_hour') }}
                                                        </div>
    @endif
                                                </div>
                                            </div> -->
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="required"
                                for="pickup_time">{{ trans('translation.task.fields.pickup_time') }}</label>
                            <input class="form-control {{ $errors->has('pickup_time') ? 'is-invalid' : '' }}"
                                type="datetime-local" name="pickup_time" id="pickup_time"
                                value="{{ old('pickup_time', '') }}" required>
                            @if ($errors->has('pickup_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('pickup_time') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="required"
                                for="dropoff_time">{{ trans('translation.task.fields.dropoff_time') }}</label>
                            <input class="form-control {{ $errors->has('dropoff_time') ? 'is-invalid' : '' }}"
                                type="datetime-local" name="dropoff_time" id="dropoff_time"
                                value="{{ old('dropoff_time', '') }}" required>
                            @if ($errors->has('dropoff_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('dropoff_time') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end col-->
                    @role('Admin')
                        <div class="col-6">
                            <div class="mb-3">
                                <label>{{ trans('translation.task.fields.takasi') }}</label>
                                <select class="form-control {{ $errors->has('takasi') ? 'is-invalid' : '' }}" name="takasi"
                                    id="takasi">
                                    <option value disabled {{ old('takasi', null) === null ? 'selected' : '' }}>
                                        {{ trans('translation.pleaseSelect') }}</option>
                                    @foreach (App\Models\Task::TAKASI_SELECT as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('takasi', 'NO') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('takasi'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('takasi') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endrole
                    <div class="col-6">
                        <div class="mb-3">
                            <label>{{ trans('translation.task.fields.task_type') }}</label>
                            <select class="form-control {{ $errors->has('task_type') ? 'is-invalid' : '' }}"
                                name="task_type" id="task_type">
                                <option value disabled {{ old('task_type', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Task::TASK_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('task_type', 'SAMPLE') === (string) $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('task_type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task_type') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="time_of_visit">{{ trans('translation.task.fields.number_of_visit') }}</label>
                            <input class="form-control {{ $errors->has('time_of_visit') ? 'is-invalid' : '' }}"
                                type="number" name="time_of_visit" id="time_of_visit" value="1" required>
                            @if ($errors->has('time_of_visit'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('time_of_visit') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- <div class="col-6">
                                                <div class="mb-3">
                                                <div class="form-group" id="searchbox">
                                                        <input id="pac-input" class="form-control" type="text" placeholder="Search for pickup location" autocomplete="off" />
                                                    </div>
                                                    <div class="form-group" id="map"></div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                <div class="form-group" id="searchbox2">
                                                        <input id="pac-input" class="form-control" type="text" placeholder="Search for drop off location " autocomplete="off" />
                                                    </div>
                                                    <div class="form-group" id="map_drop_off"></div>
                                                </div>
                                            </div> -->
                    <div class="col-lg-12">
                        <div class="text-start">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('translation.save') }}
                            </button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </form>
        </div>
    </div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Enter values",
                allowClear: true
            });
        });
    </script>
@endsection

@section('script')
    <script type="text/javascript">
        function initMap() {
            const myLatlng = {
                lat: 24.71171284420022,
                lng: 46.67523452599728
            };
            var marker;
            let markers = [];

            var myStyles = [{
                featureType: "poi",
                elementType: "labels",
                stylers: [{
                    visibility: "off"
                }]
            }];
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 24.7156901,
                    lng: 46.6439257
                },
                zoom: 12,
                styles: myStyles
            });

            function placeMarker(location) {
                if (marker) {
                    marker.setPosition(location);
                } else {
                    marker = new google.maps.Marker({
                        position: location,
                        map: map
                    });
                }
            }

            google.maps.event.addListener(map, 'click', function(event) {
                placeMarker(event.latLng);
                $("#lat").val(event.latLng.lat());
                $("#lng").val(event.latLng.lng());
                console.log(event);
            });

            var searchBox = new google.maps.places.SearchBox(document.getElementById('pac-input'));
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById('pac-input'));
            google.maps.event.addListener(searchBox, 'places_changed', function() {
                searchBox.set('map', null);
                var places = searchBox.getPlaces();
                var bounds = new google.maps.LatLngBounds();
                var i, place;
                for (i = 0; place = places[i]; i++) {
                    (function(place) {
                        var marker = new google.maps.Marker({

                            position: place.geometry.location
                        });
                        console.log(marker.position.lat());
                        console.log(marker.position.lng());
                        $("#lat").val(marker.position.lat());
                        $("#lng").val(marker.position.lng());
                        marker.bindTo('map', searchBox, 'map');
                        google.maps.event.addListener(marker, 'map_changed', function() {
                            if (!this.getMap()) {
                                this.unbindAll();
                            }
                        });
                        bounds.extend(place.geometry.location);
                    }(place));
                }
                map.fitBounds(bounds);
                searchBox.set('map', map);
                map.setZoom(Math.min(map.getZoom(), 12));

            });



        }

        function notify(message) {
            console.log('notify');
            $("#notification").fadeIn("slow").append(message);
            $(".dismiss").click(function() {
                $("#notification").fadeOut("slow");
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDf1ht01vFyWcfWS33mmdfd30qm5-uyWhM&libraries=places&callback=initMap"
        async defer></script>
    <link href="{{ asset('css/map.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('assets/libs/prismjs/prismjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/notifications.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

    <script src="build/js/pages/select2.init.js"></script>
@endsection
