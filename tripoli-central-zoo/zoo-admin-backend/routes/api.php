<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\MapLocationController;
use App\Http\Controllers\Api\SyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes
Route::prefix('v1')->group(function () {
    // Animals
    Route::apiResource('animals', AnimalController::class)->only(['index', 'show']);
    
    // Facilities
    Route::apiResource('facilities', FacilityController::class)->only(['index', 'show']);
    
    // Activities
    Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
    Route::get('activities/today', [ActivityController::class, 'today']);
    
    // Map Locations
    Route::apiResource('map-locations', MapLocationController::class)->only(['index', 'show']);
    
    // Sync
    Route::post('sync', [SyncController::class, 'sync']);
    
    // Search
    Route::get('search', [SyncController::class, 'search']);
});

// Admin API routes (protected with Sanctum)
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('animals', AnimalController::class)->except(['index', 'show']);
    Route::apiResource('facilities', FacilityController::class)->except(['index', 'show']);
    Route::apiResource('activities', ActivityController::class)->except(['index', 'show']);
    Route::apiResource('map-locations', MapLocationController::class)->except(['index', 'show']);
});
