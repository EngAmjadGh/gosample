@extends('layouts.master')
@section('title')
    @lang('translation.locations')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.locations')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('translation.create') }} {{ trans('translation.location') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.locations.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('translation.location.fields.name') }}</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                                name="name" id="name" value="{{ old('name', '') }}" required>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="arabic_name">{{ trans('translation.location.fields.arabic_name') }}</label>
                            <input class="form-control {{ $errors->has('arabic_name') ? 'is-invalid' : '' }}" type="text"
                                name="arabic_name" id="arabic_name" value="{{ old('arabic_name', '') }}" required>
                            @if ($errors->has('arabic_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('arabic_name') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="description">{{ trans('translation.location.fields.description') }}</label>
                            <input class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                type="text" name="description" id="description" value="{{ old('description', '') }}"
                                required>
                            @if ($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- <div class="col-6">
                        <div class="form-group">
                            <label class="required"
                                for="mobile">{{ trans('translation.location.fields.mobile') }}</label>
                            <input class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}" type="text"
                                name="mobile" id="mobile" value="{{ old('mobile', '') }}" required>
                            @if ($errors->has('mobile'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('mobile') }}
                                </div>
                            @endif
                        </div>
                    </div> --}}

                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ trans('translation.location.fields.status') }}</label>
                            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                                id="statuss">
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                    {{ trans('translation.pleaseSelect') }}</option>
                                @foreach (App\Models\Location::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="lat">{{ trans('translation.location.fields.lat') }}</label>
                            <input class="form-control {{ $errors->has('lat') ? 'is-invalid' : '' }}" type="text"
                                name="lat" id="lat" value="{{ old('lat', '') }}">
                            @if ($errors->has('lat'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('lat') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="lng">{{ trans('translation.location.fields.lng') }}</label>
                            <input class="form-control {{ $errors->has('lng') ? 'is-invalid' : '' }}" type="text"
                                name="lng" id="lng" value="{{ old('lng', '') }}">
                            @if ($errors->has('lng'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('lng') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label
                                for="pickup_waiting_time">{{ trans('translation.location.fields.pickup_waiting_time') }}</label>
                            <input class="form-control {{ $errors->has('pickup_waiting_time') ? 'is-invalid' : '' }}"
                                type="text" name="pickup_waiting_time" id="pickup_waiting_time"
                                value="{{ old('pickup_waiting_time', '') }}">
                            @if ($errors->has('pickup_waiting_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('pickup_waiting_time') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label
                                for="drop_off_waiting_time">{{ trans('translation.location.fields.drop_off_waiting_time') }}</label>
                            <input class="form-control {{ $errors->has('drop_off_waiting_time') ? 'is-invalid' : '' }}"
                                type="text" name="drop_off_waiting_time" id="drop_off_waiting_time"
                                value="{{ old('drop_off_waiting_time', '') }}">
                            @if ($errors->has('drop_off_waiting_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('drop_off_waiting_time') }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>




                <div class="form-group">
                    <div class="form-group" id="searchbox">
                        <input id="pac-input" class="form-control" type="text" placeholder="Search address"
                            autocomplete="off" />
                    </div>
                    <div class="form-group" id="map"></div>
                </div>


                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('translation.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection



@section('script')
    <script type="text/javascript">
        function initMap() {
            var myStyles = [{
                featureType: "poi",
                elementType: "labels",
                stylers: [{
                    visibility: "off"
                }]
            }];

            const myLatlng = {
                lat: 24.71171284420022,
                lng: 46.67523452599728
            };
            var marker;
            let markers = [];

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
@endsection
