<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics_844', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_type_id')->constrained('curriculum_types');
            $table->string('code'); // e.g., "T001"
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('form_level'); // e.g., "Form 3", "Form 4"
            $table->string('subject'); // e.g., "English", "Mathematics"
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->unique('code');
            $table->index(['curriculum_type_id', 'form_level', 'subject']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics_844');
    }
};
