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
        Schema::create('map_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location_type'); // animal, facility, activity
            $table->unsignedBigInteger('reference_id'); // ID of the related animal/facility/activity
            $table->decimal('coordinate_x', 10, 6); // X coordinate on map
            $table->decimal('coordinate_y', 10, 6); // Y coordinate on map
            $table->json('svg_path')->nullable(); // SVG path data for complex shapes
            $table->integer('map_level')->default(1); // Floor/level number
            $table->text('description')->nullable();
            $table->boolean('is_interactive')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['location_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_locations');
    }
};
