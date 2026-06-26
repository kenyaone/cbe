<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_strand_id')->constrained('sub_strands')->cascadeOnDelete();
            $table->foreignId('linked_sub_strand_id')->constrained('sub_strands')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['sub_strand_id', 'linked_sub_strand_id']);
            $table->index(['sub_strand_id', 'linked_sub_strand_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_links');
    }
};
