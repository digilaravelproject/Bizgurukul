<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class MediaProcessingService
{
    /**
     * Compress image to max 500KB and convert to WebP
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string Relative path to stored file
     */
    public function compressAndConvertToWebP($file, $directory)
    {
        try {
            // Ensure directory exists
            $storagePath = storage_path('app/public/' . $directory);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
            $fullPath = $storagePath . '/' . $filename;

            // Standard PHP GD implementation
            $sourceImage = null;
            $extension = strtolower($file->getClientOriginalExtension());

            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $sourceImage = imagecreatefromjpeg($file->getRealPath());
                    break;
                case 'png':
                    $sourceImage = imagecreatefrompng($file->getRealPath());
                    // Handle transparency for PNG
                    imagepalettetotruecolor($sourceImage);
                    imagealphablending($sourceImage, true);
                    imagesavealpha($sourceImage, true);
                    break;
                case 'webp':
                    $sourceImage = imagecreatefromwebp($file->getRealPath());
                    break;
                default:
                    // If format not supported for conversion or is already optimized, just store it
                    return $file->storeAs($directory, $filename, 'public');
            }

            if (!$sourceImage) {
                 return $file->storeAs($directory, $filename, 'public');
            }

            // Quality loop to ensure < 500KB
            $quality = 80;
            $tempPath = sys_get_temp_dir() . '/' . $filename;

            do {
                imagewebp($sourceImage, $tempPath, $quality);
                $size = filesize($tempPath);
                $quality -= 5;
            } while ($size > 500 * 1024 && $quality > 10);

            // Move from temp to final destination
            rename($tempPath, $fullPath);
            imagedestroy($sourceImage);

            return $directory . '/' . $filename;

        } catch (Exception $e) {
            Log::error("Image compression failed: " . $e->getMessage());
            // Fallback: just store original
            return $file->store($directory, 'public');
        }
    }
}
