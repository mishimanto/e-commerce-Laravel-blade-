<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Uploadable
{
    /**
     * Upload a file to storage
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $disk
     * @return string
     */
    public function uploadFile(UploadedFile $file, $path = 'uploads', $disk = 'public')
    {
        $filename = $this->generateFileName($file);
        
        return $file->storeAs($path, $filename, $disk);
    }

    /**
     * Upload multiple files
     *
     * @param array $files
     * @param string $path
     * @param string|null $disk
     * @return array
     */
    public function uploadMultiple(array $files, $path = 'uploads', $disk = 'public')
    {
        $uploaded = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploaded[] = $this->uploadFile($file, $path, $disk);
            }
        }
        
        return $uploaded;
    }

    /**
     * Upload an image with optional resizing
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $options
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path = 'images', $options = [])
    {
        $filename = $this->generateFileName($file);
        
        // If intervention/image is installed, you can add image manipulation here
        if (class_exists('\Intervention\Image\Facades\Image')) {
            $image = \Intervention\Image\Facades\Image::make($file);
            
            // Resize if options provided
            if (isset($options['width']) || isset($options['height'])) {
                $width = $options['width'] ?? null;
                $height = $options['height'] ?? null;
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Save to storage
            $path = $path . '/' . $filename;
            Storage::disk('public')->put($path, (string) $image->encode());
            
            return $path;
        }
        
        // Fallback to regular upload
        return $file->storeAs($path, $filename, 'public');
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile($path, $disk = 'public')
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }

    /**
     * Delete multiple files
     *
     * @param array $paths
     * @param string $disk
     * @return bool
     */
    public function deleteMultiple(array $paths, $disk = 'public')
    {
        $existingPaths = array_filter($paths, function ($path) use ($disk) {
            return $path && Storage::disk($disk)->exists($path);
        });
        
        if (!empty($existingPaths)) {
            return Storage::disk($disk)->delete($existingPaths);
        }
        
        return false;
    }

    /**
     * Generate a unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFileName(UploadedFile $file)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        // Sanitize filename
        $sanitized = Str::slug($originalName);
        
        // Add timestamp and random string to ensure uniqueness
        return time() . '_' . $sanitized . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Get the full URL for an uploaded file
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public function getFileUrl($path, $disk = 'public')
    {
        if (!$path) {
            return null;
        }
        
        return Storage::disk($disk)->url($path);
    }

    /**
     * Check if file exists
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public function fileExists($path, $disk = 'public')
    {
        return $path && Storage::disk($disk)->exists($path);
    }

    /**
     * Get file size
     *
     * @param string|null $path
     * @param string $disk
     * @return int|null
     */
    public function getFileSize($path, $disk = 'public')
    {
        if ($this->fileExists($path, $disk)) {
            return Storage::disk($disk)->size($path);
        }
        
        return null;
    }

    /**
     * Get file mime type
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public function getFileMimeType($path, $disk = 'public')
    {
        if ($this->fileExists($path, $disk)) {
            return Storage::disk($disk)->mimeType($path);
        }
        
        return null;
    }

    /**
     * Move file to new location
     *
     * @param string $oldPath
     * @param string $newPath
     * @param string $disk
     * @return bool
     */
    public function moveFile($oldPath, $newPath, $disk = 'public')
    {
        if ($this->fileExists($oldPath, $disk)) {
            return Storage::disk($disk)->move($oldPath, $newPath);
        }
        
        return false;
    }

    /**
     * Copy file to new location
     *
     * @param string $sourcePath
     * @param string $destinationPath
     * @param string $disk
     * @return bool
     */
    public function copyFile($sourcePath, $destinationPath, $disk = 'public')
    {
        if ($this->fileExists($sourcePath, $disk)) {
            return Storage::disk($disk)->copy($sourcePath, $destinationPath);
        }
        
        return false;
    }

    /**
     * Upload base64 image
     *
     * @param string $base64Image
     * @param string $path
     * @return string|null
     */
    public function uploadBase64Image($base64Image, $path = 'images')
    {
        // Extract image data from base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $imageType = $matches[1];
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($imageData);
            
            if ($imageData === false) {
                return null;
            }
            
            $filename = time() . '_' . Str::random(10) . '.' . $imageType;
            $fullPath = $path . '/' . $filename;
            
            Storage::disk('public')->put($fullPath, $imageData);
            
            return $fullPath;
        }
        
        return null;
    }

    /**
     * Get file extension from path
     *
     * @param string $path
     * @return string
     */
    public function getFileExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get file name from path
     *
     * @param string $path
     * @return string
     */
    public function getFileName($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get file basename from path
     *
     * @param string $path
     * @return string
     */
    public function getFileBasename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Clean old files in a directory
     *
     * @param string $directory
     * @param int $days
     * @param string $disk
     * @return int
     */
    public function cleanOldFiles($directory, $days = 7, $disk = 'public')
    {
        $files = Storage::disk($disk)->files($directory);
        $deleted = 0;
        $cutoff = now()->subDays($days)->timestamp;
        
        foreach ($files as $file) {
            $lastModified = Storage::disk($disk)->lastModified($file);
            
            if ($lastModified < $cutoff) {
                Storage::disk($disk)->delete($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }
}