<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\CategoryResource;
use App\Models\Animal;
use App\Models\Facility;
use App\Models\Activity;
use App\Models\Category;
use App\Models\MapNode;
use App\Models\MapPath;
use App\Models\OpeningHour;
use App\Models\SyncLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    /**
     * Check for updates since last sync timestamp.
     */
    public function checkUpdates(Request $request): JsonResponse
    {
        $request->validate([
            'last_sync' => 'required|date',
        ]);

        $lastSync = $request->input('last_sync');
        $currentTime = now();

        // Get updated records since last sync
        $animals = Animal::with(['category', 'openingHours'])
            ->where('updated_at', '>', $lastSync)
            ->active()
            ->get();

        $facilities = Facility::with(['category', 'openingHours'])
            ->where('updated_at', '>', $lastSync)
            ->get();

        $activities = Activity::with(['facility', 'animal'])
            ->where('updated_at', '>', $lastSync)
            ->scheduled()
            ->get();

        $categories = Category::where('updated_at', '>', $lastSync)
            ->active()
            ->get();

        $mapNodes = MapNode::where('updated_at', '>', $lastSync)->get();
        $mapPaths = MapPath::where('updated_at', '>', $lastSync)->get();

        // Log this sync
        SyncLog::create([
            'table_name' => 'sync_request',
            'last_sync' => $currentTime,
            'record_count' => $animals->count() + $facilities->count() + $activities->count(),
            'sync_status' => 'success',
            'metadata' => [
                'animals' => $animals->count(),
                'facilities' => $facilities->count(),
                'activities' => $activities->count(),
                'categories' => $categories->count(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'has_updates' => $animals->isNotEmpty() || $facilities->isNotEmpty() || 
                           $activities->isNotEmpty() || $categories->isNotEmpty() ||
                           $mapNodes->isNotEmpty() || $mapPaths->isNotEmpty(),
            'data' => [
                'animals' => AnimalResource::collection($animals),
                'facilities' => FacilityResource::collection($facilities),
                'activities' => ActivityResource::collection($activities),
                'categories' => CategoryResource::collection($categories),
                'map_nodes' => $mapNodes,
                'map_paths' => $mapPaths,
            ],
            'counts' => [
                'animals' => $animals->count(),
                'facilities' => $facilities->count(),
                'activities' => $activities->count(),
                'categories' => $categories->count(),
                'map_nodes' => $mapNodes->count(),
                'map_paths' => $mapPaths->count(),
            ],
            'sync_time' => $currentTime->toIso8601String(),
        ]);
    }

    /**
     * Get complete dataset for initial offline storage.
     */
    public function getFullDataset(): JsonResponse
    {
        $currentTime = now();

        // Get all active data
        $animals = Animal::with(['category', 'openingHours', 'activities'])
            ->active()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $facilities = Facility::with(['category', 'openingHours', 'activities'])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $activities = Activity::with(['facility', 'animal'])
            ->scheduled()
            ->orderBy('start_time')
            ->get();

        $categories = Category::active()
            ->orderBy('display_order')
            ->get();

        $mapNodes = MapNode::all();
        $mapPaths = MapPath::with(['startNode', 'endNode'])->get();

        // Log this full dataset request
        SyncLog::create([
            'table_name' => 'full_dataset',
            'last_sync' => $currentTime,
            'record_count' => $animals->count() + $facilities->count() + $activities->count(),
            'sync_status' => 'success',
            'metadata' => [
                'animals' => $animals->count(),
                'facilities' => $facilities->count(),
                'activities' => $activities->count(),
                'categories' => $categories->count(),
                'map_nodes' => $mapNodes->count(),
                'map_paths' => $mapPaths->count(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'animals' => AnimalResource::collection($animals),
                'facilities' => FacilityResource::collection($facilities),
                'activities' => ActivityResource::collection($activities),
                'categories' => CategoryResource::collection($categories),
                'map_nodes' => $mapNodes,
                'map_paths' => $mapPaths,
            ],
            'counts' => [
                'animals' => $animals->count(),
                'facilities' => $facilities->count(),
                'activities' => $activities->count(),
                'categories' => $categories->count(),
                'map_nodes' => $mapNodes->count(),
                'map_paths' => $mapPaths->count(),
            ],
            'sync_time' => $currentTime->toIso8601String(),
            'version' => '1.0.0',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
            ], 400);
        }

        $animals = Animal::where('is_visible', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('scientific_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $facilities = Facility::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $activities = Activity::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'animals' => $animals,
                'facilities' => $facilities,
                'activities' => $activities,
            ],
        ]);
    }
}
