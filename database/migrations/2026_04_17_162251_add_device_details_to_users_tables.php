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
        Schema::table('students', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('device_id');
            $table->string('device_version')->nullable()->after('device_name');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('device_id');
            $table->string('device_version')->nullable()->after('device_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['device_name', 'device_version']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['device_name', 'device_version']);
        });
    }
};
