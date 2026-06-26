<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_sync_at')->nullable();
            $table->integer('records_synced')->default(0);
            $table->enum('sync_direction', ['upload', 'download', 'bidirectional'])->default('bidirectional');
            $table->enum('status', ['success', 'failed', 'in_progress'])->default('in_progress');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'created_at']);
            $table->index(['user_id', 'last_sync_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
