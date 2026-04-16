<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, $default = null): ?string
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, ?string $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'description' => $description,
            ], fn($v) => $v !== null)
        );

        Cache::forget("setting_{$key}");
    }
}
