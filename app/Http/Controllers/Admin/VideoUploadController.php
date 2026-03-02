<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class VideoUploadController extends Controller
{
    /**
     * Handle chunked video file uploads natively.
     */
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'video_file' => 'required|file',
            'resumableChunkNumber' => 'required|integer',
            'resumableTotalChunks' => 'required|integer',
            'resumableIdentifier' => 'required|string',
            'resumableFilename' => 'required|string',
        ]);

        try {
            $chunkNumber = $request->input('resumableChunkNumber');
            $totalChunks = $request->input('resumableTotalChunks');
            $identifier = $request->input('resumableIdentifier');
            $filename = $request->input('resumableFilename');
            $file = $request->file('video_file');

            $tempDir = storage_path('app/temp_uploads/' . $identifier);

            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Move the chunk to the temp directory
            $chunkPath = $tempDir . '/' . $chunkNumber;
            $file->move($tempDir, $chunkNumber);

            // Check if all chunks are uploaded
            $uploadedChunks = count(glob($tempDir . '/*'));

            if ($uploadedChunks == $totalChunks) {
                // Assemble the file
                $finalPath = storage_path('app/public/lessons/raw/' . $identifier . '_' . $filename);
                $finalDir = dirname($finalPath);

                if (!file_exists($finalDir)) {
                    mkdir($finalDir, 0755, true);
                }

                $out = fopen($finalPath, 'ab');

                for ($i = 1; $i <= $totalChunks; $i++) {
                    $in = fopen($tempDir . '/' . $i, 'rb');
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    fclose($in);
                    unlink($tempDir . '/' . $i); // Delete chunk after reading
                }

                fclose($out);
                rmdir($tempDir); // Delete temp dir

                // Return final path for lesson creation logic
                return response()->json([
                    'status' => 'completed',
                    'path' => 'lessons/raw/' . $identifier . '_' . $filename,
                ]);
            }

            return response()->json([
                'status' => 'uploading',
                'chunk' => $chunkNumber,
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Chunk upload failed: ' . $e->getMessage()], 500);
        }
    }
}
