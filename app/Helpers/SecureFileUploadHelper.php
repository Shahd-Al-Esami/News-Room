<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SecureFileUploadHelper
{public static function store(UploadedFile $file, string $folder): string
    {
        self::validateUpload($file);

        $safeFolder = self::sanitizeFolderPath($folder);

        self::assertFolderAllowed($safeFolder);

        $safeFileName = self::generateSafeFileName($file);

        $path = self::buildPath($safeFolder, $safeFileName);

        $stream = fopen($file->getRealPath(), 'rb');

        if (!$stream) {
            throw new RuntimeException('Cannot open file stream.');
        }

        Storage::disk(config('media.disk', 'public'))->put($path, $stream);

        fclose($stream);

        return $path;
    }

    private static function validateUpload(UploadedFile $file): void
    {
        $maxSize = config('media.max_size_kb', 5120) * 1024;

        if ($file->getSize() > $maxSize) {
            throw new RuntimeException('File size exceeds the allowed limit.');
        }

        $ext = strtolower($file->guessExtension() ?? $file->getClientOriginalExtension());

        if (in_array($ext, config('media.disallowed_extensions', []))) {
            throw new RuntimeException('This file extension is strictly disallowed.');
        }

        if (!in_array($ext, config('media.allowed_extensions', []))) {
            throw new RuntimeException('Invalid file extension.');
        }

        if (!in_array($file->getMimeType(), config('media.allowed_mimes', []))) {
            throw new RuntimeException('Invalid MIME type.');
        }
    }

    private static function sanitizeFolderPath(string $folder): string
    {
        $folder = str_replace(['../', '..\\', '..'], '', $folder);

        $folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $folder);

        return trim($folder, '/');
    }

    private static function assertFolderAllowed(string $folder): void
    {
        $allowedPatterns = config('media.allowed_folders', []);
        $isMatched = false;

        foreach ($allowedPatterns as $pattern) {
            $regex = preg_replace('/\{[a-zA-Z0-9_\-]+\}/', '[a-zA-Z0-9_\-]+', $pattern);
            $regex = '#^' . str_replace('/', '\/', $regex) . '$#';

            if (preg_match($regex, $folder)) {
                $isMatched = true;
                break;
            }
        }

        if (!$isMatched) {
            throw new RuntimeException('Target directory structure is not allowed.');
        }
    }

    private static function generateSafeFileName(UploadedFile $file): string
    {
        $ext = strtolower($file->guessExtension() ?? $file->getClientOriginalExtension());
        return Str::uuid() . '_' . time() . '.' . $ext;
    }

    private static function buildPath(string $folder, string $fileName): string
    {
        $base = config('media.base_folder', 'uploads');
        return trim($base . '/' . $folder . '/' . $fileName, '/');
    }
}
