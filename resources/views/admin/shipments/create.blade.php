@extends('layouts.master')
@section('title')
    @lang('translation.shipments')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.shipments')
        @endslot
    @endcomponent
    <!-- Add these in your layout's <head> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Add this before your page's closing </body> tag -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>




    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.shipment.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.shipments.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="sender_name">{{ trans('cruds.shipment.fields.sender_name') }}</label>
                            <input class="form-control" type="text" name="sender_name" id="sender_name">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="sender_long">{{ trans('cruds.shipment.fields.sender_long') }}</label>
                            <input class="form-control" type="text" name="sender_long" id="sender_long">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="sender_lat">{{ trans('cruds.shipment.fields.sender_lat') }}</label>
                            <input class="form-control" type="text" name="sender_lat" id="sender_lat">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="sender_mobile">{{ trans('cruds.shipment.fields.sender_mobile') }}</label>
                            <input class="form-control" type="text" name="sender_mobile" id="sender_mobile">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="receiver_name">{{ trans('cruds.shipment.fields.receiver_name') }}</label>
                            <input class="form-control" type="text" name="receiver_name" id="receiver_name">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="receiver_long">{{ trans('cruds.shipment.fields.receiver_long') }}</label>
                            <input class="form-control" type="text" name="receiver_long" id="receiver_long">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="receiver_lat">{{ trans('cruds.shipment.fields.receiver_lat') }}</label>
                            <input class="form-control" type="text" name="receiver_lat" id="receiver_lat">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="receiver_mobile">{{ trans('cruds.shipment.fields.receiver_mobile') }}</label>
                            <input class="form-control" type="text" name="receiver_mobile" id="receiver_mobile">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="carrier">{{ trans('cruds.shipment.fields.carrier') }} <span class="danger">*</span></label>
                            <input class="form-control" type="text" name="carrier" id="carrier" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="reference_number">{{ trans('cruds.shipment.fields.reference_number') }} <span class="danger">*</span></label>
                            <input class="form-control" type="text" name="reference_number" id="reference_number" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="batch">{{ trans('cruds.shipment.fields.batch') }} <span class="danger">*</span></label>
                            <input class="form-control" type="text" name="batch" id="batch" required>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="task">{{ trans('cruds.shipment.fields.task') }} <span class="danger">*</span></label>
                            <select class="form-control select2" name="task" id="task"
                                required>
                                @foreach ($tasks as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('task', [])) ? 'selected' : '' }}>{{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('task'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="from_location">{{ trans('translation.task.fields.from_location') }} <span class="danger">*</span></label>
                            <select class="form-control select2" name="from_location" id="from_location"
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
                    <div class="col-3">
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
                    <div class="col-3">
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
