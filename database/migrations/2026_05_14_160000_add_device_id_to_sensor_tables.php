<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_data', function (Blueprint $table): void {
            $table->string('device_id', 100)->nullable()->after('id')->index();
        });

        Schema::table('accelerometer_data', function (Blueprint $table): void {
            $table->string('device_id', 100)->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('gps_data', function (Blueprint $table): void {
            $table->dropIndex(['device_id']);
            $table->dropColumn('device_id');
        });

        Schema::table('accelerometer_data', function (Blueprint $table): void {
            $table->dropIndex(['device_id']);
            $table->dropColumn('device_id');
        });
    }
};