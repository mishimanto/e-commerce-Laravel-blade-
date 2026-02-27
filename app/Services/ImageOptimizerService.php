<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    protected array $config = [
        'max_width' => 800,
        'max_height' => 800,
        'quality' => 80,
        'format' => 'webp',
        'strip_exif' => true,
    ];

    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function upload(UploadedFile $image, string $path, ?string $oldPath = null): string
    {
        // Validate image
        $this->validateImage($image);
        
        // Delete old image if exists
        if ($oldPath) {
            $this->delete($oldPath);
        }
        
        // Generate filename
        $filename = $this->generateFilename($image);
        
        // Read image
        $img = Image::read($image);
        
        // Original dimensions
        $originalWidth = $img->width();
        $originalHeight = $img->height();
        
        // Calculate new dimensions maintaining aspect ratio
        $dimensions = $this->calculateAspectRatioResize($originalWidth, $originalHeight);
        
        // Resize image with calculated dimensions
        if ($dimensions['width'] != $originalWidth || $dimensions['height'] != $originalHeight) {
            $img->resize($dimensions['width'], $dimensions['height']);
        }
        
        // Log for debugging
        \Log::info("Image resized: {$originalWidth}x{$originalHeight} -> {$dimensions['width']}x{$dimensions['height']}");
        
        // Get full storage path
        $fullPath = storage_path('app/public/' . $path);
        
        // Create directory if not exists
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        // Save in configured format
        $savePath = $fullPath . '/' . $filename;
        
        switch ($this->config['format']) {
            case 'webp':
                $img->toWebp($this->config['quality'])->save($savePath);
                break;
            case 'jpg':
            case 'jpeg':
                $img->toJpeg($this->config['quality'])->save($savePath);
                break;
            case 'png':
                $img->toPng()->save($savePath);
                break;
            default:
                $img->save($savePath);
        }
        
        return $path . '/' . $filename;
    }

    protected function calculateAspectRatioResize(int $originalWidth, int $originalHeight): array
    {
        $maxWidth = $this->config['max_width'];
        $maxHeight = $this->config['max_height'];
 
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }

        $aspectRatio = $originalWidth / $originalHeight;
        
        $widthRatio = $maxWidth / $originalWidth;
        $heightRatio = $maxHeight / $originalHeight;
        
        $ratio = min($widthRatio, $heightRatio);
        
        if ($ratio < 1) {
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }
        
        return [
            'width' => $newWidth,
            'height' => $newHeight
        ];
    }

    protected function resizeWithAspectRatio($img): void
    {
        $maxWidth = $this->config['max_width'];
        $maxHeight = $this->config['max_height'];

        $img->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();  
            $constraint->upsize();        
        });
    }
    
    public function uploadMultiple(array $images, string $path): array
    {
        $uploadedPaths = [];
        
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $uploadedPaths[] = $this->upload($image, $path);
            }
        }
        
        return $uploadedPaths;
    }

    public function delete(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    public function deleteMultiple(array $paths): void
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }
    }

    public function getUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        
        return Storage::disk('public')->url($path);
    }

    protected function validateImage(UploadedFile $image): void
    {
        $extension = strtolower($image->getClientOriginalExtension());
        
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \Exception("Invalid image extension: {$extension}. Allowed: " . implode(', ', $this->allowedExtensions));
        }
        
        if (!$image->isValid()) {
            throw new \Exception("Invalid image upload");
        }
    }
    
    protected function generateFilename(UploadedFile $image): string
    {
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = time();
        $unique = uniqid();
        $slug = Str::slug($originalName);
        
        return $timestamp . '_' . $unique . '_' . $slug . '.' . $this->config['format'];
    }

    public function getDimensions(string $path): ?array
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        list($width, $height) = getimagesize($fullPath);
        
        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}