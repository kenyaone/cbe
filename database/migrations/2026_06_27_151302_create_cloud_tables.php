<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cloud device registry - tracks all remote devices
        Schema::create('cloud_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 36)->unique();
            $table->string('device_name');
            $table->string('region')->nullable();
            $table->string('county')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('total_students')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->integer('total_certs')->default(0);
            $table->decimal('avg_score', 5, 2)->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Cloud learner progress - synced from all remote devices
        Schema::create('cloud_learner_progress', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 36);
            $table->string('learner_username');
            $table->string('learner_name');
            $table->string('subject');
            $table->string('content_title');
            $table->integer('progress_percentage')->default(0);
            $table->string('status'); // completed, in_progress, not_started
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('synced_at');
            $table->foreign('device_id')
                  ->references('device_id')
                  ->on('cloud_devices')
                  ->onDelete('cascade');
            $table->timestamps();
            $table->index(['device_id', 'status']);
            $table->index(['synced_at']);
        });

        // Cloud device stats - aggregated metrics per device
        Schema::create('cloud_device_stats', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 36);
            $table->date('date');
            $table->integer('active_learners')->default(0);
            $table->integer('lessons_completed')->default(0);
            $table->integer('quiz_attempts')->default(0);
            $table->decimal('avg_score', 5, 2)->nullable();
            $table->integer('certs_issued')->default(0);
            $table->foreign('device_id')
                  ->references('device_id')
                  ->on('cloud_devices')
                  ->onDelete('cascade');
            $table->unique(['device_id', 'date']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_device_stats');
        Schema::dropIfExists('cloud_learner_progress');
        Schema::dropIfExists('cloud_devices');
    }
};
