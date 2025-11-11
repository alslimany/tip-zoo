<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $nodes = MapNode::all();
        $paths = MapPath::with(['startNode', 'endNode'])->get();
        $locations = MapLocation::all();
        $mapImageUrl = MapSetting::get('map_background_image', '');
        $mapBounds = MapSetting::get('map_bounds', '[[32.88, 13.18], [32.9, 13.2]]');
        
        return view('admin.map-editor.index', compact('nodes', 'paths', 'locations', 'mapImageUrl', 'mapBounds'));
    }

    public function storeNode(Request $request)
    {
        $validated = $request->validate([
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'type' => 'required|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $node = MapNode::create($validated);

        return response()->json([
            'success' => true,
            'node' => $node
        ]);
    }

    public function updateNode(Request $request, MapNode $node)
    {
        $validated = $request->validate([
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'type' => 'required|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $node->update($validated);

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
            'nodes' => MapNode::all(),
            'paths' => MapPath::with(['startNode', 'endNode'])->get(),
            'locations' => MapLocation::all(),
            'mapBounds' => MapSetting::get('map_bounds', '[[32.88, 13.18], [32.9, 13.2]]'),
        ]);
    }
}
