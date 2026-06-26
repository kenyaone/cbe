<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learner_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sub_strand_id')->nullable()->constrained('sub_strands')->cascadeOnDelete();
            $table->foreignId('sub_topic_844_id')->nullable()->constrained('sub_topics_844')->cascadeOnDelete();
            $table->foreignId('content_file_id')->nullable()->constrained('content_files')->cascadeOnDelete();
            $table->integer('progress_percentage')->default(0); // 0-100
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('device_id')->nullable(); // for offline sync tracking
            $table->enum('sync_status', ['pending', 'synced'])->default('pending');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'sync_status']);
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learner_progress');
    }
};
