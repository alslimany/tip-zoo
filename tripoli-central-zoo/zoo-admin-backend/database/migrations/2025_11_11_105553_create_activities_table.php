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
            $table->string('name');
            $table->string('activity_type'); // show, feeding, tour, event, workshop
            $table->text('description');
            $table->string('image')->nullable();
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->onDelete('set null');
            $table->foreignId('animal_id')->nullable()->constrained('animals')->onDelete('set null');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->json('recurrence')->nullable(); // For recurring activities (daily, weekly, etc.)
            $table->integer('duration_minutes')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('requires_booking')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->string('age_restriction')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('activities');
    }
};
