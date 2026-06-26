<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('server_ip')->nullable(); // WiFi hotspot IP
            $table->timestamps();

            $table->index('name');
            $table->index('server_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
