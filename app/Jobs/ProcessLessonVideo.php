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
    public $timeout = 3600;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function handle()
    {
        try {
            $disk = 'public'; // Fixed: Files are stored in public disk
            $originalPath = $this->lesson->video_path;
            $hlsFolder = 'lessons/hls/' . $this->lesson->id;

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

            if ($disk === 'local' || $disk === 'public') {
                Storage::disk($disk)->makeDirectory($hlsFolder);
            }

            $hlsPlaylistPath = $hlsFolder . '/playlist.m3u8';
            $lowBitrate = (new X264)->setKiloBitrate(500);
            $highBitrate = (new X264)->setKiloBitrate(1500);

            FFMpeg::fromDisk($disk)
                ->open($originalPath)
                ->exportForHLS()
                ->addFormat($lowBitrate, fn($filters) => $filters->resize(640, 360))
                ->addFormat($highBitrate, fn($filters) => $filters->resize(1280, 720))
                ->setSegmentLength(10)
                ->toDisk($disk)
                ->save($hlsPlaylistPath);

            $this->lesson->update(['hls_path' => $hlsPlaylistPath]);

        } catch (\Exception $e) {
            Log::error("Video Processing Failed ID {$this->lesson->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
