<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('map_nodes', function (Blueprint $table) {
            $table->id();
            $table->decimal('x', 10, 6); // X coordinate on map
            $table->decimal('y', 10, 6); // Y coordinate on map
            $table->string('type'); // intersection, poi, entrance, exit, etc.
            $table->string('name')->nullable();
            $table->json('connections')->nullable(); // Array of connected node IDs
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index(['x', 'y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_nodes');
    }
};
