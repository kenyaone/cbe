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
        // Create device_settings table for storing device ID and sync metadata
        Schema::create('device_settings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 36)->unique();
            $table->string('device_name')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('last_checkin')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Create sync_queue for tracking changes to sync
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // learner_progress, content_file, etc
            $table->unsignedBigInteger('entity_id');
            $table->string('action'); // create, update, delete
            $table->json('data')->nullable();
            $table->boolean('synced')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('synced_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_settings');
        Schema::dropIfExists('sync_queue');
    }
};
