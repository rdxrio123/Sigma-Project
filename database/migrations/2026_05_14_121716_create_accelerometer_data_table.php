<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accelerometer_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('x', 8, 4);
            $table->decimal('y', 8, 4);
            $table->decimal('z', 8, 4);
            $table->decimal('magnitude', 8, 4);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accelerometer_data');
    }
};
