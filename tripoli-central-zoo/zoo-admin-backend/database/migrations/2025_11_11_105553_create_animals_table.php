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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('species');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('location_x', 10, 6)->nullable(); // X coordinate on map
            $table->decimal('location_y', 10, 6)->nullable(); // Y coordinate on map
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->text('facts')->nullable(); // Fun facts about the animal
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->boolean('featured')->default(false);
            
            // Additional fields from original migration
            $table->string('scientific_name')->nullable();
            $table->json('gallery')->nullable(); // Multiple images
            $table->string('habitat')->nullable();
            $table->string('conservation_status')->nullable(); // Endangered, Vulnerable, etc.
            $table->json('diet')->nullable(); // Array of diet information
            $table->string('age')->nullable();
            $table->string('weight')->nullable();
            $table->string('size')->nullable();
            $table->json('feeding_times')->nullable(); // Schedule of feeding times
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category_id');
            $table->index('status');
            $table->index('featured');
            $table->index(['location_x', 'location_y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
