<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("setting_{$key}");
    }

    public static function remove(string $key): void
    {
        self::where('key', $key)->delete();
        Cache::forget("setting_{$key}");
    }

    protected static function booted()
    {
        static::updated(fn($setting) => Cache::forget("setting_{$setting->key}"));
        static::deleted(fn($setting) => Cache::forget("setting_{$setting->key}"));
    }
}
