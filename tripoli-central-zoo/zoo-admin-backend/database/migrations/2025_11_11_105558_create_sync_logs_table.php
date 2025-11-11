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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // Name of the table that was synced
            $table->timestamp('last_sync'); // Last synchronization timestamp
            $table->integer('record_count')->default(0); // Number of records synced
            $table->string('sync_status')->default('success'); // success, failed, partial
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Additional sync information
            $table->timestamps();
            
            // Indexes
            $table->index('table_name');
            $table->index('last_sync');
            $table->index('sync_status');
            $table->unique(['table_name', 'last_sync']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
