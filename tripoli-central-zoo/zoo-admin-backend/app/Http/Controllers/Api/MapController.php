<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Facility;
use App\Models\MapNode;
use App\Models\MapPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Get complete map data including nodes, paths, and markers.
     */
    public function getMapData(): JsonResponse
    {
        // Get all map nodes
        $nodes = MapNode::all()->map(function ($node) {
            return [
                'id' => $node->id,
                'x' => $node->x,
                'y' => $node->y,
                'type' => $node->type,
                'name' => $node->name,
                'connections' => $node->connections,
                'coordinates' => $node->coordinates,
            ];
        });

        // Get all map paths
        $paths = MapPath::with(['startNode', 'endNode'])
            ->get()
            ->map(function ($path) {
                return [
                    'id' => $path->id,
                    'start_node_id' => $path->start_node_id,
                    'end_node_id' => $path->end_node_id,
                    'distance' => $path->distance,
                    'accessible' => $path->accessible,
                    'path_data' => $path->path_data,
                    'start_node' => [
                        'id' => $path->startNode->id,
                        'x' => $path->startNode->x,
                        'y' => $path->startNode->y,
                    ],
                    'end_node' => [
                        'id' => $path->endNode->id,
                        'x' => $path->endNode->x,
                        'y' => $path->endNode->y,
                    ],
                ];
            });

        // Get markers for animals
        $animalMarkers = Animal::with('category')
            ->whereNotNull('location_x')
            ->whereNotNull('location_y')
            ->active()
            ->get()
            ->map(function ($animal) {
                return [
                    'id' => $animal->id,
                    'type' => 'animal',
                    'name' => $animal->name,
                    'x' => $animal->location_x,
                    'y' => $animal->location_y,
                    'category' => $animal->category->name ?? null,
                    'icon' => $animal->category->icon ?? null,
                    'featured' => $animal->featured,
                ];
            });

        // Get markers for facilities
        $facilityMarkers = Facility::with('category')
            ->whereNotNull('location_x')
            ->whereNotNull('location_y')
            ->open()
            ->get()
            ->map(function ($facility) {
                return [
                    'id' => $facility->id,
                    'type' => 'facility',
                    'name' => $facility->name,
                    'x' => $facility->location_x,
                    'y' => $facility->location_y,
                    'facility_type' => $facility->type,
                    'category' => $facility->category->name ?? null,
                    'icon' => $facility->category->icon ?? null,
                    'accessible' => $facility->is_accessible,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'nodes' => $nodes,
                'paths' => $paths,
                'markers' => [
                    'animals' => $animalMarkers,
                    'facilities' => $facilityMarkers,
                ],
            ],
        ]);
    }

    /**
     * Calculate route between two nodes using A* pathfinding algorithm.
     */
    public function calculateRoute(Request $request): JsonResponse
    {
        $request->validate([
            'start' => 'required|exists:map_nodes,id',
            'end' => 'required|exists:map_nodes,id',
            'accessible_only' => 'nullable|boolean',
        ]);

        $startId = $request->input('start');
        $endId = $request->input('end');
        $accessibleOnly = $request->boolean('accessible_only', false);

        // Get all nodes and paths
        $nodes = MapNode::all()->keyBy('id');
        $paths = MapPath::when($accessibleOnly, function ($query) {
            return $query->accessible();
        })->get();

        // Build adjacency list
        $adjacency = [];
        foreach ($paths as $path) {
            if (!isset($adjacency[$path->start_node_id])) {
                $adjacency[$path->start_node_id] = [];
            }
            $adjacency[$path->start_node_id][] = [
                'node_id' => $path->end_node_id,
                'distance' => $path->distance ?? $this->calculateDistance(
                    $nodes[$path->start_node_id],
                    $nodes[$path->end_node_id]
                ),
                'path_data' => $path->path_data,
            ];

            // Add reverse path (assuming bidirectional)
            if (!isset($adjacency[$path->end_node_id])) {
                $adjacency[$path->end_node_id] = [];
            }
            $adjacency[$path->end_node_id][] = [
                'node_id' => $path->start_node_id,
                'distance' => $path->distance ?? $this->calculateDistance(
                    $nodes[$path->start_node_id],
                    $nodes[$path->end_node_id]
                ),
                'path_data' => $path->path_data,
            ];
        }

        // Run A* algorithm
        $route = $this->astar($startId, $endId, $nodes, $adjacency);

        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'No route found between the specified nodes',
            ], 404);
        }

        // Build detailed route response
        $routeDetails = [];
        $totalDistance = 0;

        for ($i = 0; $i < count($route) - 1; $i++) {
            $currentNode = $nodes[$route[$i]];
            $nextNode = $nodes[$route[$i + 1]];

            // Find the path between current and next node
            $pathInfo = collect($adjacency[$route[$i]] ?? [])
                ->firstWhere('node_id', $route[$i + 1]);

            $distance = $pathInfo['distance'] ?? 0;
            $totalDistance += $distance;

            $routeDetails[] = [
                'from' => [
                    'id' => $currentNode->id,
                    'name' => $currentNode->name,
                    'x' => $currentNode->x,
                    'y' => $currentNode->y,
                    'type' => $currentNode->type,
                ],
                'to' => [
                    'id' => $nextNode->id,
                    'name' => $nextNode->name,
                    'x' => $nextNode->x,
                    'y' => $nextNode->y,
                    'type' => $nextNode->type,
                ],
                'distance' => $distance,
                'path_data' => $pathInfo['path_data'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'route' => $route,
                'total_distance' => $totalDistance,
                'steps' => count($route) - 1,
                'details' => $routeDetails,
                'accessible_only' => $accessibleOnly,
            ],
        ]);
    }

    /**
     * A* pathfinding algorithm implementation.
     */
    private function astar(int $startId, int $endId, $nodes, $adjacency): ?array
    {
        $openSet = [$startId];
        $cameFrom = [];
        $gScore = [$startId => 0];
        $fScore = [$startId => $this->heuristic($nodes[$startId], $nodes[$endId])];

        while (!empty($openSet)) {
            // Get node with lowest fScore
            $current = array_reduce($openSet, function ($carry, $nodeId) use ($fScore) {
                if ($carry === null) return $nodeId;
                return ($fScore[$nodeId] ?? INF) < ($fScore[$carry] ?? INF) ? $nodeId : $carry;
            }, null);

            if ($current === $endId) {
                // Reconstruct path
                return $this->reconstructPath($cameFrom, $current);
            }

            $openSet = array_values(array_diff($openSet, [$current]));

            // Check neighbors
            foreach ($adjacency[$current] ?? [] as $neighbor) {
                $neighborId = $neighbor['node_id'];
                $tentativeGScore = $gScore[$current] + $neighbor['distance'];

                if ($tentativeGScore < ($gScore[$neighborId] ?? INF)) {
                    $cameFrom[$neighborId] = $current;
                    $gScore[$neighborId] = $tentativeGScore;
                    $fScore[$neighborId] = $tentativeGScore + $this->heuristic($nodes[$neighborId], $nodes[$endId]);

                    if (!in_array($neighborId, $openSet)) {
                        $openSet[] = $neighborId;
                    }
                }
            }
        }

        return null; // No path found
    }

    /**
     * Heuristic function for A* (Euclidean distance).
     */
    private function heuristic($node1, $node2): float
    {
        return sqrt(
            pow($node1->x - $node2->x, 2) + 
            pow($node1->y - $node2->y, 2)
        );
    }

    /**
     * Calculate distance between two nodes.
     */
    private function calculateDistance($node1, $node2): float
    {
        return $this->heuristic($node1, $node2);
    }

    /**
     * Reconstruct path from A* came_from map.
     */
    private function reconstructPath(array $cameFrom, int $current): array
    {
        $path = [$current];
        while (isset($cameFrom[$current])) {
            $current = $cameFrom[$current];
            array_unshift($path, $current);
        }
        return $path;
    }
}
