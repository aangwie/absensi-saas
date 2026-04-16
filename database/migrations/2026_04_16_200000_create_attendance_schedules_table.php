<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 0=Minggu, 1=Senin, ..., 6=Sabtu
            $table->time('check_in_start')->nullable();   // Waktu mulai absen masuk
            $table->time('check_in_end')->nullable();     // Waktu batas absen masuk
            $table->time('check_out_start')->nullable();  // Waktu mulai absen pulang
            $table->time('check_out_end')->nullable();    // Waktu batas absen pulang
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_schedules');
    }
};
