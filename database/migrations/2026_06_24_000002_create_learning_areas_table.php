<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_type_id')->constrained('curriculum_types');
            $table->string('code')->unique(); // e.g., "LA001"
            $table->string('name'); // e.g., "Mathematical Activities"
            $table->text('description')->nullable();
            $table->integer('lessons_per_week')->nullable();
            $table->string('grade_level'); // e.g., "PP1", "PP2", "Grade 1"
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['curriculum_type_id', 'grade_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_areas');
    }
};
