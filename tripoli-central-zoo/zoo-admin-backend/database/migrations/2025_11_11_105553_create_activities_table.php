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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('schedule')->nullable(); // Schedule information (times, dates, recurrence)
            $table->string('location')->nullable(); // Location description or reference
            $table->string('type'); // show, feeding, tour, event, workshop
            $table->string('image_url')->nullable();
            $table->enum('status', ['scheduled', 'cancelled', 'completed'])->default('scheduled');
            
            // Additional fields from original migration
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->onDelete('set null');
            $table->foreignId('animal_id')->nullable()->constrained('animals')->onDelete('set null');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->json('recurrence')->nullable(); // For recurring activities (daily, weekly, etc.)
            $table->integer('duration_minutes')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('requires_booking')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->string('age_restriction')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('type');
            $table->index('status');
            $table->index('facility_id');
            $table->index('animal_id');
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
