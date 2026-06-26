<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_strands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('strand_id')->constrained('strands')->cascadeOnDelete();
            $table->string('code'); // e.g., "1.1", "1.2"
            $table->string('name'); // e.g., "Sorting and Grouping"
            $table->text('description')->nullable();
            $table->integer('lesson_count')->nullable(); // 8, 10, 20 lessons
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['strand_id', 'code']);
            $table->index('strand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_strands');
    }
};
