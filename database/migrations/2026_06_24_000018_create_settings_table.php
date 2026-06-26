<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['boolean', 'integer', 'string', 'json'])->default('string');
            $table->timestamp('updated_at');

            $table->index('key');
        });

        // Seed default settings
        $now = now();
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'offline_mode', 'value' => 'true', 'type' => 'boolean', 'updated_at' => $now],
            ['key' => 'sync_interval', 'value' => '300', 'type' => 'integer', 'updated_at' => $now], // 5 minutes
            ['key' => 'max_file_size', 'value' => '104857600', 'type' => 'integer', 'updated_at' => $now], // 100MB
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
