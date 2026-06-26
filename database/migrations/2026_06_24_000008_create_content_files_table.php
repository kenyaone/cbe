<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_files', function (Blueprint $table) {
            $table->id();
            // Polymorphic relationship: can belong to sub_strand or sub_topic_844
            $table->nullableMorphs('contentable');
            $table->foreignId('content_type_id')->constrained('content_types');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // local path or URL
            $table->bigInteger('file_size')->nullable();
            $table->integer('duration')->nullable(); // in seconds, for videos
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_files');
    }
};
