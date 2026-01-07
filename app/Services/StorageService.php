<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * FileUploadService - A centralized service for handling file uploads to R2 storage
 * 
 * Usage Examples:
 * 
 * // Basic upload
 * $fileUploadService = new FileUploadService();
 * $filePath = $fileUploadService->uploadToR2($request->file('image'), 'products');
 * 
 * // Upload with custom filename
 * $filePath = $fileUploadService->uploadToR2($request->file('image'), 'categories', 'custom-name.jpg');
 * 
 * // Upload multiple files
 * $filePaths = $fileUploadService->uploadMultipleToR2($request->file('images'), 'gallery');
 * 
 * // Get public URL
 * $publicUrl = $fileUploadService->getPublicUrl($filePath);
 * 
 * // Delete file
 * $fileUploadService->deleteFromR2($filePath);
 * 
 * // Validate file before upload
 * if ($fileUploadService->validateFile($file, ['jpg', 'png'], 10)) {
 *     $filePath = $fileUploadService->uploadToR2($file, 'uploads');
 * }
 */
class StorageService
{
    /**
     * Upload file to R2 storage with organized folder structure
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $customFilename
     * @return string The stored filename/path
     */
    public function uploadToR2(UploadedFile $file, string $folder, ?string $customFilename = null): string
    {
        // Generate filename
        $filename = $customFilename ?? $this->generateUniqueFilename($file);

        // Create full path with environment and folder
        $fullPath = $this->buildFilePath($folder, $filename);

        // Store file to R2
        Storage::disk('r2')->put($fullPath, file_get_contents($file));

        return $fullPath;
    }

    /**
     * Upload multiple files to R2 storage
     *
     * @param array $files Array of UploadedFile objects
     * @param string $folder
     * @return array Array of stored file paths
     */
    public function uploadMultipleToR2(array $files, string $folder): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->uploadToR2($file, $folder);
            }
        }

        return $uploadedFiles;
    }

    /**
     * Delete file from R2 storage
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFromR2(string $filePath): bool
    {
        return Storage::disk('r2')->delete($filePath);
    }

    /**
     * Delete multiple files from R2 storage
     *
     * @param array $filePaths
     * @return bool
     */
    public function deleteMultipleFromR2(array $filePaths): bool
    {
        return Storage::disk('r2')->delete($filePaths);
    }

    /**
     * Get public URL for R2 file
     *
     * @param string $filePath
     * @return string
     */
    public function getPublicUrl(string $filePath): string
    {
        $cdnUrl = env('CDN_URL');

        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($filePath, '/');
        }

        // Fallback to R2 endpoint if CDN URL is not configured
        $r2Endpoint = env('R2_ENDPOINT');
        $r2Bucket = env('R2_BUCKET');

        if ($r2Endpoint && $r2Bucket) {
            return rtrim($r2Endpoint, '/') . '/' . $r2Bucket . '/' . ltrim($filePath, '/');
        }

        return $filePath;
    }

    /**
     * Generate unique filename with timestamp and random string
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);

        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Build full file path with environment and folder structure
     *
     * @param string $folder
     * @param string $filename
     * @return string
     */
    private function buildFilePath(string $folder, string $filename): string
    {
        $environment = config('app.env', 'local');

        return "{$environment}/{$folder}/{$filename}";
    }

    /**
     * Validate file type and size
     *
     * @param UploadedFile $file
     * @param array $allowedTypes
     * @param int $maxSizeInMB
     * @return bool
     */
    public function validateFile(UploadedFile $file, array $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], int $maxSizeInMB = 5): bool
    {
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            return false;
        }

        // Check file size (convert MB to bytes)
        $maxSizeInBytes = $maxSizeInMB * 1024 * 1024;
        if ($file->getSize() > $maxSizeInBytes) {
            return false;
        }

        return true;
    }

    /**
     * Get file info
     *
     * @param UploadedFile $file
     * @return array
     */
    public function getFileInfo(UploadedFile $file): array
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'size_human' => $this->formatBytes($file->getSize()),
        ];
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function upload(UploadedFile $file, string $directory, ?string $filename = null, ?string $disk = null): string
    {
        $name = $filename ?? $this->generateUniqueFilename($file);
        $folder = trim($directory, '/');
        $environment = config('app.env', 'local');
        $dir = trim("{$environment}/{$folder}", '/');
        $path = "{$dir}/{$name}";
        $stream = fopen($file->getRealPath(), 'r');
        Storage::disk($disk ?? 'r2')->put($path, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $path;
    }

    public function exists(?string $path, ?string $disk = null): bool
    {
        if (empty($path)) {
            return false;
        }
        try {
            return Storage::disk($disk ?? 'r2')->exists($path);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function delete(?string $path, ?string $disk = null): bool
    {
        if (empty($path)) {
            return false;
        }
        try {
            return Storage::disk($disk ?? 'r2')->delete($path);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function cdnUrl(?string $path, ?string $disk = null): ?string
    {
        if (empty($path)) {
            return null;
        }
        return $this->getPublicUrl($path);
    }

    public function pathFromUrl(string $url): string
    {
        $u = $url;
        $candidates = [
            rtrim((string) env('R2_PUBLIC_URL', ''), '/'),
            rtrim((string) env('CDN_URL', ''), '/'),
            rtrim((string) env('R2_ENDPOINT', ''), '/') . '/' . trim((string) env('R2_BUCKET', ''), '/'),
            rtrim((string) config('app.url', ''), '/'),
        ];
        foreach ($candidates as $base) {
            if ($base && str_starts_with($u, $base)) {
                $u = substr($u, strlen($base));
                break;
            }
        }
        $u = ltrim($u, '/');
        if (str_starts_with($u, 'storage/')) {
            $u = substr($u, strlen('storage/'));
        }
        return $u;
    }
}
