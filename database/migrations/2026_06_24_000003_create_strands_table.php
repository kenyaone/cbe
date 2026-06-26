<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_area_id')->constrained('learning_areas')->cascadeOnDelete();
            $table->string('code'); // e.g., "1.0", "2.0"
            $table->string('name'); // e.g., "Pre-Number Activities"
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['learning_area_id', 'code']);
            $table->index('learning_area_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strands');
    }
};
