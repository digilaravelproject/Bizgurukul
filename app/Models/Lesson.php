<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'video_path',
        'hls_path',
        'document_path',
        'bunny_video_id',
        'bunny_embed_url',
        'thumbnail',
        'order_column'
    ];

    protected $appends = [
        'lesson_file_url',
        'thumbnail_url',
        'admin_video_url',
        'bunny_thumbnail_url',
        'player_url',
        'is_bunny'
    ];

    public function getBunnyThumbnailUrlAttribute(): ?string
    {
        // Bunny Stream lacks a public thumbnail URL without a CDN Pull Zone hostname.
        // Returning null here forces the blade view to show a styled placeholder instead.
        return null;
    }

    public function getThumbnailAttribute($value): ?string
    {
        if ($value) {
            $path = ltrim($value, '/');
            return str_starts_with($path, 'storage/') ? '/' . $path : '/storage/' . $path;
        }

        if ($this->getRawOriginal('type') === 'video' && $this->getRawOriginal('bunny_video_id')) {
            return $this->bunny_thumbnail_url;
        }

        return null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail;
    }

    public function getLessonFileUrlAttribute(): ?string
    {
        // Accommodates both legacy local video paths and standard document paths
        $path = $this->getRawOriginal('type') === 'video'
            ? ($this->getRawOriginal('hls_path') ?? $this->getRawOriginal('video_path'))
            : $this->getRawOriginal('document_path');

        if (!$path)
            return null;

        $path = ltrim($path, '/');
        return str_starts_with($path, 'storage/') ? '/' . $path : '/storage/' . $path;
    }

    public function getAdminVideoUrlAttribute(): ?string
    {
        // Specifically bypasses HLS encryption to allow for standard MP4 admin previews
        $path = $this->getRawOriginal('video_path');

        if (!$path)
            return null;

        $path = ltrim($path, '/');
        return str_starts_with($path, 'storage/') ? '/' . $path : '/storage/' . $path;
    }

    public function setBunnyEmbedUrlAttribute($value): void
    {
        if (!$value) {
            $this->attributes['bunny_embed_url'] = null;
            return;
        }

        $value = trim($value);

        // Gracefully handles inputs where users paste the entire <iframe> tag instead of just the URL
        if (stripos($value, '<iframe') !== false && preg_match('/src=["\']([^"\']+)["\']/i', $value, $match)) {
            $this->attributes['bunny_embed_url'] = trim($match[1]);
            return;
        }

        $this->attributes['bunny_embed_url'] = $value;
    }

    public function getIsBunnyAttribute(): bool
    {
        return (bool) ($this->getRawOriginal('bunny_video_id') || $this->getRawOriginal('bunny_embed_url'));
    }

    public function getPlayerUrlAttribute(): ?string
    {
        if ($embedUrl = $this->getRawOriginal('bunny_embed_url')) {
            return $embedUrl;
        }

        if ($videoId = $this->getRawOriginal('bunny_video_id')) {
            $libId = config('services.bunny.library_id');
            return "https://iframe.mediadelivery.net/embed/{$libId}/{$videoId}?autoplay=false&preload=true";
        }

        return $this->lesson_file_url;
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function progress()
    {
        return $this->hasOne(VideoProgress::class);
    }
}
