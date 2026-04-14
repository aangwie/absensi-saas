<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class School extends Model
{
    protected $fillable = [
        'name',
        'npsn',
        'slug',
        'address',
        'phone',
        'email',
        'logo',
        'late_threshold',
        'checkout_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'late_threshold' => 'string',
        'checkout_time' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
