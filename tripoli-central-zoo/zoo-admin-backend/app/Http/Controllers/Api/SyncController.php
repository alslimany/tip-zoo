<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Facility;
use App\Models\Activity;
use App\Models\MapLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $lastSync = $request->input('last_sync', '2000-01-01 00:00:00');

        $animals = Animal::with('category')
            ->where('updated_at', '>', $lastSync)
            ->where('is_visible', true)
            ->get();

        $facilities = Facility::with('facilityType')
            ->where('updated_at', '>', $lastSync)
            ->get();

        $activities = Activity::with(['facility', 'animal'])
            ->where('updated_at', '>', $lastSync)
            ->where('is_active', true)
            ->get();

        $mapLocations = MapLocation::where('updated_at', '>', $lastSync)
            ->where('is_interactive', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'animals' => $animals,
                'facilities' => $facilities,
                'activities' => $activities,
                'map_locations' => $mapLocations,
            ],
            'sync_time' => now()->toIso8601String(),
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
