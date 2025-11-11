<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MapNode;
use App\Models\MapPath;
use App\Models\MapLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MapEditorController extends Controller
{
    public function index()
    {
        $nodes = MapNode::all();
        $paths = MapPath::with(['startNode', 'endNode'])->get();
        $locations = MapLocation::all();
        
        return view('admin.map-editor.index', compact('nodes', 'paths', 'locations'));
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
        $request->validate([
            'map_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($request->hasFile('map_image')) {
            $path = $request->file('map_image')->store('maps', 'public');
            
            return response()->json([
                'success' => true,
                'path' => '/storage/' . $path
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
        ]);
    }
}
