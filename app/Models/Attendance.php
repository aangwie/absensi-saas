<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'school_id',
        'attendee_type',
        'attendee_id',
        'location_id',
        'latitude',
        'longitude',
        'distance_meters',
        'accuracy',
        'device_id',
        'is_mock_suspected',
        'mock_reasons',
        'user_agent',
        'type',
        'status',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'distance_meters' => 'float',
        'accuracy' => 'float',
        'is_mock_suspected' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function attendee()
    {
        return $this->morphTo();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('checked_at', today());
    }

    public function scopeCheckIn($query)
    {
        return $query->where('type', 'check_in');
    }

    public function scopeCheckOut($query)
    {
        return $query->where('type', 'check_out');
    }

    public function getAttendeeNameAttribute()
    {
        return $this->attendee?->name ?? '-';
    }

    public function getAttendeeIdentifierAttribute()
    {
        if ($this->attendee_type === 'App\\Models\\Student') {
            return $this->attendee?->nisn ?? '-';
        }
        return $this->attendee?->nip ?? '-';
    }
}
