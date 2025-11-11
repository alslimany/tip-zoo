<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Facility;
use App\Models\MapNode;
use App\Models\MapPath;
use App\Models\MapLocation;
use App\Models\MapSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MapEditorController extends Controller
{
    public function index()
    {
        $nodes = MapNode::with('placeable')->get();
        $paths = MapPath::with(['startNode', 'endNode'])->get();
        $locations = MapLocation::all();
        $mapImageUrl = MapSetting::get('map_background_image', '');
        $mapBounds = MapSetting::get('map_bounds', '[[32.88, 13.18], [32.9, 13.2]]');
        
        // Get unmapped animals and facilities
        $unmappedAnimals = Animal::whereDoesntHave('mapNode')->where('status', 'active')->get(['id', 'name', 'species']);
        $unmappedFacilities = Facility::whereDoesntHave('mapNode')->where('status', 'open')->get(['id', 'name', 'type']);
        
        return view('admin.map-editor.index', compact('nodes', 'paths', 'locations', 'mapImageUrl', 'mapBounds', 'unmappedAnimals', 'unmappedFacilities'));
    }

    public function storeNode(Request $request)
    {
        $validated = $request->validate([
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'type' => 'required|string',
            'placeable_type' => 'nullable|string|in:animal,facility',
            'placeable_id' => 'nullable|integer',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Convert placeable_type to full class name
        if (!empty($validated['placeable_type'])) {
            $validated['placeable_type'] = $validated['placeable_type'] === 'animal' 
                ? Animal::class 
                : Facility::class;
        }

        $node = MapNode::create($validated);
        $node->load('placeable');

        return response()->json([
            'success' => true,
            'node' => $node
        ]);
    }

    public function updateNode(Request $request, MapNode $node)
    {
        $validated = $request->validate([
            'x' => 'numeric',
            'y' => 'numeric',
            'type' => 'string',
            'placeable_type' => 'nullable|string|in:animal,facility',
            'placeable_id' => 'nullable|integer',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Convert placeable_type to full class name if provided
        if (isset($validated['placeable_type']) && !empty($validated['placeable_type'])) {
            $validated['placeable_type'] = $validated['placeable_type'] === 'animal' 
                ? Animal::class 
                : Facility::class;
        }

        $node->update($validated);
        $node->load('placeable');

        return response()->json([
            'success' => true,
            'node' => $node
        ]);
    }

    public function destroyNode(MapNode $node)
    {
        // Delete all paths connected to this node
        MapPath::where('start_node_id', $node->id)
            ->orWhere('end_node_id', $node->id)
            ->delete();

        $node->delete();

        return response()->json([
            'success' => true,
            'message' => 'Node deleted successfully'
        ]);
    }

    public function storePath(Request $request)
    {
        $validated = $request->validate([
            'start_node_id' => 'required|exists:map_nodes,id',
            'end_node_id' => 'required|exists:map_nodes,id',
            'distance' => 'nullable|numeric',
            'accessible' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $path = MapPath::create($validated);

        return response()->json([
            'success' => true,
            'path' => $path->load(['startNode', 'endNode'])
        ]);
    }

    public function destroyPath(MapPath $path)
    {
        $path->delete();

        return response()->json([
            'success' => true,
            'message' => 'Path deleted successfully'
        ]);
    }

    public function uploadMapImage(Request $request)
    {
        // Handle map bounds calibration
        if ($request->has('bounds')) {
            MapSetting::set('map_bounds', $request->input('bounds'));
            
            return response()->json([
                'success' => true,
                'message' => 'Map bounds saved successfully'
            ]);
        }

        // Handle map image upload
        $request->validate([
            'map_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($request->hasFile('map_image')) {
            // Delete old map image if exists
            $oldPath = MapSetting::get('map_background_image');
            if ($oldPath) {
                $oldFile = str_replace('/storage/', '', $oldPath);
                Storage::disk('public')->delete($oldFile);
            }

            $path = $request->file('map_image')->store('maps', 'public');
            $fullPath = '/storage/' . $path;
            
            // Save to settings
            MapSetting::set('map_background_image', $fullPath);
            
            return response()->json([
                'success' => true,
                'path' => $fullPath
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image uploaded'
        ], 400);
    }

    public function getMapData()
    {
        return response()->json([
            'nodes' => MapNode::with('placeable')->get(),
            'paths' => MapPath::with(['startNode', 'endNode'])->get(),
            'locations' => MapLocation::all(),
            'mapBounds' => MapSetting::get('map_bounds', '[[32.88, 13.18], [32.9, 13.2]]'),
            'unmappedAnimals' => Animal::whereDoesntHave('mapNode')->where('status', 'active')->get(['id', 'name', 'species']),
            'unmappedFacilities' => Facility::whereDoesntHave('mapNode')->where('status', 'open')->get(['id', 'name', 'type']),
        ]);
    }
    
    public function getUnmappedPlaces()
    {
        return response()->json([
            'animals' => Animal::whereDoesntHave('mapNode')->where('status', 'active')->get(['id', 'name', 'species']),
            'facilities' => Facility::whereDoesntHave('mapNode')->where('status', 'open')->get(['id', 'name', 'type']),
        ]);
    }
}
