<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('attendee_type'); // student or teacher
            $table->unsignedBigInteger('attendee_id');
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->float('distance_meters')->default(0);
            $table->string('device_id')->nullable();
            $table->enum('type', ['check_in', 'check_out'])->default('check_in');
            $table->enum('status', ['on_time', 'late', 'absent'])->default('on_time');
            $table->text('notes')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['attendee_type', 'attendee_id']);
            $table->index(['school_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
