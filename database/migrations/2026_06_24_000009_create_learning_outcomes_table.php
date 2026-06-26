<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_strand_id')->constrained('sub_strands')->cascadeOnDelete();
            $table->text('description');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('sub_strand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_outcomes');
    }
};
