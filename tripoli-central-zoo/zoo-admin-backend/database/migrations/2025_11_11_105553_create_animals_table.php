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
            $table->foreignId('category_id')->constrained('animal_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->text('description');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // Multiple images
            $table->string('habitat')->nullable();
            $table->string('conservation_status')->nullable(); // Endangered, Vulnerable, etc.
            $table->json('diet')->nullable(); // Array of diet information
            $table->string('age')->nullable();
            $table->string('weight')->nullable();
            $table->string('size')->nullable();
            $table->text('fun_facts')->nullable();
            $table->json('feeding_times')->nullable(); // Schedule of feeding times
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
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
        Schema::dropIfExists('animals');
    }
};
