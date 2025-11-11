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
            $table->foreignId('facility_type_id')->constrained('facility_types')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('opening_hours')->nullable(); // Daily schedule
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('amenities')->nullable(); // Restrooms, wheelchair access, etc.
            $table->boolean('is_accessible')->default(true); // Wheelchair accessible
            $table->boolean('is_open')->default(true);
            $table->integer('capacity')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
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
