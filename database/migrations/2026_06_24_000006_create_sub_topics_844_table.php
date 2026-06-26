<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_topics_844', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_844_id')->constrained('topics_844')->cascadeOnDelete();
            $table->string('code'); // e.g., "T001.1"
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['topic_844_id', 'code']);
            $table->index('topic_844_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_topics_844');
    }
};
