<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // "PDF", "Video", "Text", "HTML Interactive"
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });

        // Seed default content types
        \Illuminate\Support\Facades\DB::table('content_types')->insert([
            ['name' => 'PDF', 'mime_type' => 'application/pdf'],
            ['name' => 'Video', 'mime_type' => 'video/mp4'],
            ['name' => 'Text', 'mime_type' => 'text/plain'],
            ['name' => 'HTML', 'mime_type' => 'text/html'],
            ['name' => 'Interactive', 'mime_type' => 'text/html'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('content_types');
    }
};
