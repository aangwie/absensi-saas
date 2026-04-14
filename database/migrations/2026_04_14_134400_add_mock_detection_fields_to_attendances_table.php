<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->float('accuracy')->nullable()->after('distance_meters');
            $table->boolean('is_mock_suspected')->default(false)->after('device_id');
            $table->text('mock_reasons')->nullable()->after('is_mock_suspected');
            $table->string('user_agent')->nullable()->after('mock_reasons');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['accuracy', 'is_mock_suspected', 'mock_reasons', 'user_agent']);
        });
    }
};
