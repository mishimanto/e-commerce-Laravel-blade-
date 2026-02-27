<?php

namespace App\Traits;

use App\Services\ImageOptimizerService;
use Illuminate\Http\UploadedFile;

trait HandlesImageOptimization
{
    protected $imageOptimizer;

    protected function initializeImageOptimizer(array $config = []): void
    {
        $this->imageOptimizer = new ImageOptimizerService($config);
    }

    protected function uploadImage(?UploadedFile $image, string $path, ?string $oldPath = null): ?string
    {
        if (!$image) {
            return $oldPath;
        }
        
        return $this->imageOptimizer->upload($image, $path, $oldPath);
    }
    
    protected function deleteImage(?string $path): void
    {
        $this->imageOptimizer->delete($path);
    }

    protected function getImageUrl(?string $path): ?string
    {
        return $this->imageOptimizer->getUrl($path);
    }
}