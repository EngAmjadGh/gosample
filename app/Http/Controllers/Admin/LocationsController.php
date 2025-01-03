<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLocationRequest;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LocationsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Location::query()->select(sprintf('%s.*', (new Location)->table));

            // Apply search criteria
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->filled('driver_id')) {
                $query->where('driver_id', $request->driver_id);
            }
           
            // if ($request->filled('client_id')) {
            //     $query->where('client_id', $request->client_id);
            // }
            // if ($request->filled('from_location')) {
            //     $query->where('scheduled_tasks.from_location_id', $request->from_location);
            // }
            // if ($request->filled('to_location')) {
            //     $query->where('scheduled_tasks.to_location_id', $request->to_location);
            // }


            $table = Datatables::of($query);

            

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'location_show';
                $editGate      = 'location_edit';
                $deleteGate    = 'location_delete';
                $crudRoutePart = 'locations';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('arabic_name', function ($row) {
                return $row->arabic_name ? $row->arabic_name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('lat', function ($row) {
                return $row->lat ? $row->lat : '';
            });
            $table->editColumn('lng', function ($row) {
                return $row->lng ? $row->lng : '';
            });
            $table->editColumn('mobile', function ($row) {
                return $row->mobile ? $row->mobile : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Location::STATUS_SELECT[$row->status] : '';
            });

            $table->addColumn('coordinates', function ($row) {
                $lat = $row->lat ?? '';
                $lng = $row->lng ?? '';
            
                // Create a unique ID for the hidden input and button elements
                $elementId = 'copy-coordinates-' . $row->id;
            
                return '<td>' .
                    '<span>' . $lng . '</span>' . // Display the 'lng' value
                    '<input id="' . $elementId . '-link" value="https://www.google.com/maps/place/' . $lat . ',' . $lng . '" type="hidden">' .
                    '<button value="copy" class="btn btn-xs btn-info" onclick="copyToClipboard(\'' . $elementId . '-link\')">Copy</button>' .
                    '</td>';
            });
            $table->addColumn('coordinates', function ($row) {
                $lat = $row->lat ?? '';
                $lng = $row->lng ?? '';
            
                // Create a unique ID for the hidden input and button elements
                $elementId = 'copy-coordinates-' . $row->id;
            
                return '<td>' .
                    '<input id="' . $elementId . '-link" value="https://www.google.com/maps/place/' . $lat . ',' . $lng . '" type="hidden">' .
                    '<button value="copy" class="btn btn-sm btn-primary copy-coordinates-btn" onclick="copyToClipboard(\'' . $elementId . '-link\')">Copy</button>' .
                    '</td>';
            });
           

            $table->rawColumns(['actions', 'placeholder','coordinates']);

            return $table->make(true);
        }

        return view('admin.locations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        $location = Location::create($request->all());

        return redirect()->route('admin.locations.index');
    }

    public function edit(Location $location)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->all());

        return redirect()->route('admin.locations.index');
    }

    public function show(Location $location)
    {
        abort_if(Gate::denies('location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $location->load('locationsClients');

        return view('admin.locations.show', compact('location'));
    }

    public function destroy(Location $location)
    {
        abort_if(Gate::denies('location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $location->delete();

        return back();
    }

    public function massDestroy(MassDestroyLocationRequest $request)
    {
        $locations = Location::find(request('ids'));

        foreach ($locations as $location) {
            $location->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
