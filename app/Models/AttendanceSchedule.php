<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSchedule extends Model
{
    protected $fillable = [
        'school_id',
        'day_of_week',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Day names in Indonesian
     */
    public static array $dayNames = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::$dayNames[$this->day_of_week] ?? '-';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get today's schedule for a specific school
     */
    public static function getTodaySchedule(int $schoolId): ?self
    {
        $today = now()->dayOfWeek; // 0=Sunday, 1=Monday, ...
        return static::where('school_id', $schoolId)
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if current time is within check-in window
     */
    public function isWithinCheckInWindow(): bool
    {
        if (!$this->check_in_start || !$this->check_in_end) return false;
        $now = now()->format('H:i:s');
        return $now >= $this->check_in_start && $now <= $this->check_in_end;
    }

    /**
     * Check if current time is within check-out window
     */
    public function isWithinCheckOutWindow(): bool
    {
        if (!$this->check_out_start || !$this->check_out_end) return false;
        $now = now()->format('H:i:s');
        return $now >= $this->check_out_start && $now <= $this->check_out_end;
    }

    /**
     * Get formatted time window description
     */
    public function getCheckInWindowAttribute(): string
    {
        if (!$this->check_in_start || !$this->check_in_end) return '-';
        return substr($this->check_in_start, 0, 5) . ' - ' . substr($this->check_in_end, 0, 5);
    }

    public function getCheckOutWindowAttribute(): string
    {
        if (!$this->check_out_start || !$this->check_out_end) return '-';
        return substr($this->check_out_start, 0, 5) . ' - ' . substr($this->check_out_end, 0, 5);
    }
}
