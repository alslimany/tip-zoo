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
        // Add polymorphic relationship to map_nodes
        Schema::table('map_nodes', function (Blueprint $table) {
            $table->string('placeable_type')->nullable()->after('type');
            $table->unsignedBigInteger('placeable_id')->nullable()->after('placeable_type');
            $table->index(['placeable_type', 'placeable_id']);
        });
        
        // Remove location_x and location_y from animals
        // (keeping them for backward compatibility but will not use in admin)
        // Schema::table('animals', function (Blueprint $table) {
        //     $table->dropColumn(['location_x', 'location_y']);
        // });
        
        // Remove location_x and location_y from facilities
        // (keeping them for backward compatibility but will not use in admin)
        // Schema::table('facilities', function (Blueprint $table) {
        //     $table->dropColumn(['location_x', 'location_y']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('map_nodes', function (Blueprint $table) {
            $table->dropIndex(['placeable_type', 'placeable_id']);
            $table->dropColumn(['placeable_type', 'placeable_id']);
        });
    }
};
