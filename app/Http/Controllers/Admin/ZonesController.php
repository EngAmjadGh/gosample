<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyZoneRequest;
use App\Http\Requests\StoreZoneRequest;
use App\Http\Requests\UpdateZoneRequest;
use App\Models\Zone;
use Gate;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ZonesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('zone_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $zones = Zone::all();

        return view('admin.zones.index', compact('zones'));
    }

    public function create()
    {
        abort_if(Gate::denies('zone_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.zones.create');
    }

    public function store(StoreZoneRequest $request)
    {
        \Log::info($request->all());
        // $zone = Zone::create($request->all());
        $zone = new Zone();

        $polygon = json_decode($request->area, true);

        $area=array();
        foreach ($polygon as $point) {
            $area[] = new Point($point['lat'], $point['lng']);
        }
        $area[]= new Point($polygon[0]['lat'], $polygon[0]['lng']);
        $zone->name = $request->name;
        $zone->area =  new Polygon([new LineString( $area)]);
        $zone->save();
        return redirect()->route('admin.zones.index');
    }

    public function edit(Zone $zone)
    {
        abort_if(Gate::denies('zone_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.zones.edit', compact('zone'));
    }

    public function update(UpdateZoneRequest $request, Zone $zone)
    {
        $zone->update($request->all());

        return redirect()->route('admin.zones.index');
    }

    public function show(Zone $zone)
    {
        abort_if(Gate::denies('zone_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.zones.show', compact('zone'));
    }

    public function destroy(Zone $zone)
    {
        abort_if(Gate::denies('zone_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $zone->delete();

        return back();
    }

    public function massDestroy(MassDestroyZoneRequest $request)
    {
        Zone::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
