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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // restroom, restaurant, gift shop, etc.
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->decimal('location_x', 10, 6)->nullable(); // X coordinate on map
            $table->decimal('location_y', 10, 6)->nullable(); // Y coordinate on map
            $table->text('description');
            $table->json('opening_hours')->nullable(); // Daily schedule
            $table->string('image_url')->nullable();
            $table->enum('status', ['open', 'closed', 'maintenance'])->default('open');
            
            // Additional fields from original migration
            $table->json('gallery')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('amenities')->nullable(); // Restrooms, wheelchair access, etc.
            $table->boolean('is_accessible')->default(true); // Wheelchair accessible
            $table->integer('capacity')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category_id');
            $table->index('type');
            $table->index('status');
            $table->index(['location_x', 'location_y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
