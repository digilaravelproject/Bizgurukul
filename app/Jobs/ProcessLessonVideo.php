<?php

namespace App\Jobs;

use App\Models\Lesson;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ProcessLessonVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lesson;
    public $timeout = 3600; // 1 hour

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function handle()
    {
        $lessonId = $this->lesson->id;
        $this->lesson = Lesson::find($lessonId);

        if (!$this->lesson) {
            Log::error("Job cancelled: Lesson ID {$lessonId} not found.");
            return;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Force FFmpeg Threads (Helpful for Shared Hosting)
            |--------------------------------------------------------------------------
            */
            config(['laravel-ffmpeg.ffmpeg.threads' => 2]);

            $disk = 'public';
            $originalPath = $this->lesson->video_path;

            if (!Storage::disk($disk)->exists($originalPath)) {
                throw new \Exception("Original video not found: {$originalPath}");
            }

            /*
            |--------------------------------------------------------------------------
            | 1. Generate Thumbnail (if not exists)
            |--------------------------------------------------------------------------
            */
            if (empty($this->lesson->thumbnail)) {

                $thumbName = 'lessons/thumbnails/' . $this->lesson->id . '_auto.jpg';

                FFMpeg::fromDisk($disk)
                    ->open($originalPath)
                    ->getFrameFromSeconds(2)
                    ->export()
                    ->toDisk($disk)
                    ->save($thumbName);

                $this->lesson->update(['thumbnail' => $thumbName]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. HLS + Encryption Setup
            |--------------------------------------------------------------------------
            */
            $filename = pathinfo($originalPath, PATHINFO_FILENAME);

            $hlsDirectory = "lessons/hls/{$filename}";
            $hlsPath = "{$hlsDirectory}/playlist.m3u8";

            // Generate AES-128 encryption key
            $encryptionKey = random_bytes(16);

            $keyFilename = $filename . '.key';
            $keyStoragePath = 'lessons/keys/' . $keyFilename;

            // Store key privately
            Storage::disk('local')->put($keyStoragePath, $encryptionKey);

            // Route that serves key securely
            $keyUrl = route('student.video.key', [
                'lesson' => $this->lesson->id
            ]);

            Log::info("Starting Encrypted HLS Processing for Lesson ID: {$this->lesson->id}");

            /*
            |--------------------------------------------------------------------------
            | 3. Configure Video Format
            |--------------------------------------------------------------------------
            */
            $videoFormat = (new X264('aac', 'libx264'))
                ->setKiloBitrate(1200);

            /*
            |--------------------------------------------------------------------------
            | 4. Export Encrypted HLS
            |--------------------------------------------------------------------------
            */

            // NOTE: exportForHLS() uses ComplexFilters, which does NOT support ->resize().
            // We use a raw FFmpeg scale filter string instead, which works in all contexts.
            $hlsExporter = FFMpeg::fromDisk($disk)
                ->open($originalPath)
                ->exportForHLS()
                ->toDisk($disk)
                ->withEncryptionKey($encryptionKey, 'video.key');

            try {
                // The addFormat() callback receives HLSVideoFilters, which has ->resize($w, $h) directly.
                // Do NOT nest inside addFilter() — that exposes ComplexFilters which has NO resize().
                $hlsExporter->addFormat($videoFormat, function ($media) {
                    $media->resize(1280, 720);
                });
            } catch (\Throwable $filterException) {
                Log::warning("Could not add resize filter for Lesson ID: {$this->lesson->id}. Falling back to no scaling. Error: " . $filterException->getMessage());
                // Fallback: export without any scaling (original resolution)
                $hlsExporter->addFormat($videoFormat);
            }

            try {
                $hlsExporter->save($hlsPath);
            } catch (\Throwable $hlsException) {
                Log::error("HLS save failed for Lesson ID: {$this->lesson->id}. Error: " . $hlsException->getMessage());
                throw $hlsException;
            }

            /*
            |--------------------------------------------------------------------------
            | 5. Replace Key Placeholder in Playlist Files
            |--------------------------------------------------------------------------
            */
            $files = Storage::disk($disk)->files($hlsDirectory);

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'm3u8') {
                    $content = Storage::disk($disk)->get($file);
                    $content = str_replace('video.key', $keyUrl, $content);
                    Storage::disk($disk)->put($file, $content);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Update Lesson Record
            |--------------------------------------------------------------------------
            */
            $this->lesson->update([
                'hls_path' => $hlsPath,
            ]);

            Log::info("Encrypted HLS conversion successful for Lesson ID: {$this->lesson->id}");

        } catch (\Exception $e) {

            Log::error("Video Processing Failed ID {$this->lesson->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
