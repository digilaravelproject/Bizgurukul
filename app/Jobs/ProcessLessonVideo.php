<?php

namespace App\Jobs;

use App\Models\Lesson;
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
    public $tries = 3;
    public $backoff = [60, 300];

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
            | 1. Basic Config
            |--------------------------------------------------------------------------
            */

            config(['laravel-ffmpeg.ffmpeg.threads' => 2]); // safer for shared hosting

            $disk = 'public';
            $originalPath = $this->lesson->video_path;

            if (!Storage::disk($disk)->exists($originalPath)) {
                throw new \Exception("Original video not found: {$originalPath}");
            }

            Log::info("Processing Lesson ID {$lessonId}");

            /*
            |--------------------------------------------------------------------------
            | 2. Generate Thumbnail
            |--------------------------------------------------------------------------
            */

            if (empty($this->lesson->thumbnail)) {

                $thumbnailPath = "lessons/thumbnails/{$lessonId}_auto.jpg";

                FFMpeg::fromDisk($disk)
                    ->open($originalPath)
                    ->getFrameFromSeconds(2)
                    ->export()
                    ->toDisk($disk)
                    ->save($thumbnailPath);

                $this->lesson->update([
                    'thumbnail' => $thumbnailPath
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Prepare HLS Paths
            |--------------------------------------------------------------------------
            */

            $filename = pathinfo($originalPath, PATHINFO_FILENAME);

            $hlsDirectory = "lessons/hls/{$filename}";
            $playlistPath = "{$hlsDirectory}/playlist.m3u8";

            /*
            |--------------------------------------------------------------------------
            | 4. Generate AES-128 Encryption Key
            |--------------------------------------------------------------------------
            */

            $encryptionKey = random_bytes(16);

            $keyFilename = "{$filename}.key";
            $keyStoragePath = "lessons/keys/{$keyFilename}";

            // Store key privately (local disk)
            Storage::disk('local')->put($keyStoragePath, $encryptionKey);

            // Secure route for serving key
            $keyUrl = route('student.video.key', [
                'lesson' => $lessonId
            ], false);

            /*
            |--------------------------------------------------------------------------
            | 5. Create Key Info File for FFmpeg
            |--------------------------------------------------------------------------
            */
            $keyFullPath = storage_path('app/' . $keyStoragePath);

            $keyInfoFilename = "{$filename}.keyinfo";
            $keyInfoStoragePath = "lessons/keys/{$keyInfoFilename}";
            $keyInfoFullPath = storage_path('app/' . $keyInfoStoragePath);

            // The format requires:
            // 1. URL to fetch key (embedded in .m3u8)
            // 2. Absolute local path to actual key file
            $keyInfoContent = "{$keyUrl}\n{$keyFullPath}\n";
            Storage::disk('local')->put($keyInfoStoragePath, $keyInfoContent);

            /*
            |--------------------------------------------------------------------------
            | 6. Export Encrypted HLS via Raw FFmpeg Process
            |--------------------------------------------------------------------------
            |
            | Using Symfony Process directly to avoid api incompatibilities with
            | pbmedia/laravel-ffmpeg package versions on the server.
            */

            // Fallback to "ffmpeg" if config empty
            $ffmpegPath = config('laravel-ffmpeg.ffmpeg.binaries', 'ffmpeg');

            $sourceFullPath = Storage::disk($disk)->path($originalPath);
            $playlistFullPath = Storage::disk($disk)->path($playlistPath);
            $segmentFullPath = Storage::disk($disk)->path("{$hlsDirectory}/%03d.ts");

            // Ensure HLS directory exists locally in the public disk
            $hlsDirFullPath = dirname($playlistFullPath);
            if (!file_exists($hlsDirFullPath)) {
                mkdir($hlsDirFullPath, 0755, true);
            }

            // Construct exact FFmpeg command
            $ffmpegCommand = [
                $ffmpegPath,
                '-y', // Overwrite
                '-i', $sourceFullPath,
                '-vf', 'scale=-2:720,format=yuv420p',
                '-c:v', 'libx264',
                '-b:v', '1200k',
                '-maxrate', '1200k',
                '-bufsize', '2400k',
                '-c:a', 'aac',
                '-b:a', '128k',
                '-hls_time', '10',
                '-hls_playlist_type', 'vod',
                '-hls_key_info_file', $keyInfoFullPath,
                '-hls_segment_filename', $segmentFullPath,
                $playlistFullPath
            ];

            Log::info("Executing FFmpeg CLI for Lesson ID {$lessonId}: " . implode(' ', $ffmpegCommand));

            // Run process with 2 hours timeout
            $process = new \Symfony\Component\Process\Process($ffmpegCommand);
            $process->setTimeout(7200);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \Symfony\Component\Process\Exception\ProcessFailedException($process);
            }

            // Cleanup temp keyinfo file
            Storage::disk('local')->delete($keyInfoStoragePath);

            /*
            |--------------------------------------------------------------------------
            | 8. Save HLS Path
            |--------------------------------------------------------------------------
            */

            $this->lesson->update([
                'hls_path' => $playlistPath,
            ]);

            Log::info("Encrypted HLS conversion successful for Lesson ID {$lessonId}");

        } catch (\Throwable $e) {

            $this->lesson->update([
                'hls_path' => 'failed'
            ]);

            Log::error("Video Processing Failed ID {$lessonId}: " . $e->getMessage());
            throw $e;
        }
    }
}
