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
        Schema::create('map_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('start_node_id')->constrained('map_nodes')->onDelete('cascade');
            $table->foreignId('end_node_id')->constrained('map_nodes')->onDelete('cascade');
            $table->decimal('distance', 10, 2)->nullable(); // Distance in meters
            $table->boolean('accessible')->default(true); // Wheelchair accessible
            $table->json('path_data')->nullable(); // SVG path data or waypoints
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('start_node_id');
            $table->index('end_node_id');
            $table->index('accessible');
            $table->unique(['start_node_id', 'end_node_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_paths');
    }
};
