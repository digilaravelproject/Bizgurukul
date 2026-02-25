<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmailTemplate extends Model
{
    protected $fillable = ['key', 'name', 'subject', 'body', 'variables'];

    protected $casts = [
        'variables' => 'array',
    ];

    /**
     * Get template by key with caching. Falls back to hardcoded defaults.
     */
    public static function getByKey(string $key): ?self
    {
        return Cache::remember("email_template_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });
    }

    /**
     * Update template and clear cache.
     */
    public static function updateByKey(string $key, array $data): void
    {
        self::where('key', $key)->update($data);
        Cache::forget("email_template_{$key}");
    }

    protected static function booted(): void
    {
        static::updated(fn($t) => Cache::forget("email_template_{$t->key}"));
        static::saved(fn($t) => Cache::forget("email_template_{$t->key}"));
    }
}
