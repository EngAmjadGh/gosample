@extends('layouts.master')
@section('title')
    @lang('translation.zones')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.appname')
        @endslot
        @slot('title')
            @lang('translation.zones')
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.zone.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.zones.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.zone.fields.id') }}
                            </th>
                            <td>
                                {{ $zone->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.zone.fields.name') }}
                            </th>
                            <td>
                                {{ $zone->name }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group" name="map" id="map"></div>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.zones.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection




@section('script')
    <script type="text/javascript">
        var locations = <?php print_r(json_encode($zone->area->toJson())); ?>;
        let coordinates = JSON.parse(locations).coordinates;
        var area = [];
        var polygonArray = [];

        $('#myform').on('submit', function() {
            $('#area').val(JSON.stringify(polygonArray));
            return true;
        });

        $("#resetMap").on("click", function() {

            $('#success-notifiy').attr('data-toast-text', 'Map is cleared successfully');
            $('#success-notifiy').click();
            polygonArray = [];
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 24.7156901,
                    lng: 46.6439257
                },
                zoom: 12
            });

            var drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: ['polygon']
                    //   drawingModes: ['polygon', 'circle']
                },
                polygonOptions: {
                    editable: true
                }

            });

            drawingManager.setMap(map);

            google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                polygonArray = [];
                polygon2 = polygon;
                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                    var coords = polygon.getPath().getAt(i).toUrlValue(6).split(',');
                    var lat = coords[0];
                    var lng = coords[1];
                    polygonArray.push({
                        'lat': lat,
                        'lng': lng
                    });
                }
            });
        });


        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 24.7156901,
                    lng: 46.6439257
                },
                zoom: 12
            });

            var drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: ['polygon']
                    //   drawingModes: ['polygon', 'circle']
                },
                polygonOptions: {
                    editable: true
                }

            });

            drawingManager.setMap(map);

            google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                polygonArray = [];
                polygon2 = polygon;
                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                    var coords = polygon.getPath().getAt(i).toUrlValue(6).split(',');
                    var lat = coords[0];
                    var lng = coords[1];
                    polygonArray.push({
                        'lat': lat,
                        'lng': lng
                    });
                }
            });


            var data = [];
            coordinates[0].forEach(element => {
                data.push(new google.maps.LatLng(element[1], element[0]));
            });
            dataPolygon = new google.maps.Polygon({
                paths: data,
                strokeWeight: 5,
                fillColor: '#FF0000',
                fillOpacity: 0.35
            });
            dataPolygon.setMap(map);

        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDf1ht01vFyWcfWS33mmdfd30qm5-uyWhM&libraries=drawing&callback=initMap"
        async defer></script>
    <link href="{{ asset('css/map.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('assets/libs/prismjs/prismjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/notifications.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
