<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\MapLocationController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\SyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes
Route::prefix('v1')->group(function () {
    // Animals
    Route::get('animals', [AnimalController::class, 'index']);
    Route::get('animals/search', [AnimalController::class, 'search']);
    Route::get('animals/category/{category}', [AnimalController::class, 'byCategory']);
    Route::get('animals/{id}', [AnimalController::class, 'show']);
    
    // Facilities
    Route::get('facilities', [FacilityController::class, 'index']);
    Route::get('facilities/search', [FacilityController::class, 'search']);
    Route::get('facilities/type/{type}', [FacilityController::class, 'byType']);
    Route::get('facilities/nearby', [FacilityController::class, 'nearby']);
    Route::get('facilities/{id}', [FacilityController::class, 'show']);
    
    // Activities
    Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
    Route::get('activities/today', [ActivityController::class, 'today']);
    
    // Map
    Route::get('map/data', [MapController::class, 'getMapData']);
    Route::post('map/route', [MapController::class, 'calculateRoute']);
    
    // Map Locations (legacy)
    Route::apiResource('map-locations', MapLocationController::class)->only(['index', 'show']);
    
    // Sync
    Route::post('sync/check-updates', [SyncController::class, 'checkUpdates']);
    Route::get('sync/full-dataset', [SyncController::class, 'getFullDataset']);
    
    // Search (legacy - now available on individual resources)
    Route::get('search', [SyncController::class, 'search']);
});

// Admin API routes (protected with Sanctum)
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('animals', AnimalController::class)->except(['index', 'show']);
    Route::apiResource('facilities', FacilityController::class)->except(['index', 'show']);
    Route::apiResource('activities', ActivityController::class)->except(['index', 'show']);
    Route::apiResource('map-locations', MapLocationController::class)->except(['index', 'show']);
});
