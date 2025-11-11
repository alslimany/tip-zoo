<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnimalController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\MapEditorController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Animals
    Route::resource('animals', AnimalController::class);
    Route::post('animals/bulk-delete', [AnimalController::class, 'bulkDelete'])->name('animals.bulk-delete');
    Route::post('animals/bulk-update-status', [AnimalController::class, 'bulkUpdateStatus'])->name('animals.bulk-update-status');

    // Facilities
    Route::resource('facilities', FacilityController::class);

    // Activities
    Route::resource('activities', ActivityController::class);

    // Map Editor
    Route::get('map-editor', [MapEditorController::class, 'index'])->name('map-editor.index');
    Route::get('map-editor/data', [MapEditorController::class, 'getMapData'])->name('map-editor.data');
    Route::post('map-editor/nodes', [MapEditorController::class, 'storeNode'])->name('map-editor.nodes.store');
    Route::put('map-editor/nodes/{node}', [MapEditorController::class, 'updateNode'])->name('map-editor.nodes.update');
    Route::delete('map-editor/nodes/{node}', [MapEditorController::class, 'destroyNode'])->name('map-editor.nodes.destroy');
    Route::post('map-editor/paths', [MapEditorController::class, 'storePath'])->name('map-editor.paths.store');
    Route::delete('map-editor/paths/{path}', [MapEditorController::class, 'destroyPath'])->name('map-editor.paths.destroy');
    Route::post('map-editor/upload-map', [MapEditorController::class, 'uploadMapImage'])->name('map-editor.upload-map');
});
