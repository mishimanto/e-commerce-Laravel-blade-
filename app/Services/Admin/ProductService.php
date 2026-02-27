<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageOptimizerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get all products with filtering and pagination
     */
    public function getFilteredProducts($request)
    {
        $query = Product::with(['category', 'brand', 'images']);
        
        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        
        // Category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }
        
        // Brand filter
        if ($request->has('brand') && !empty($request->brand)) {
            $query->where('brand_id', $request->brand);
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Stock filter
        if ($request->has('stock') && !empty($request->stock)) {
            switch ($request->stock) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('stock', '<', 5)->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', 0);
                    break;
            }
        }
        
        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        return $query->paginate($request->get('per_page', 15));
    }
    
    /**
     * Generate unique slug from name
     */
    private function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        
        $query = Product::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
            
            $query = Product::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }
        
        return $slug;
    }
    
    /**
     * Create a new product
     */
    public function createProduct(array $data, $request = null, ?ImageOptimizerService $imageOptimizer = null)
    {
        try {
            DB::beginTransaction();
            
            // Generate slug from name if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
            }
            
            // Handle specifications
            if (isset($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }
            
            // Handle tags
            if (isset($data['tags'])) {
                $tags = explode(',', $data['tags']);
                $data['tags'] = json_encode(array_map('trim', $tags));
            }
            
            $product = Product::create($data);
            
            // Handle images if any
            if ($request && $request->hasFile('new_images')) {
                $this->uploadImages($product, $request->file('new_images'), $imageOptimizer);
            }
            
            // Handle attributes if any
            if (isset($data['attributes']) && !empty($data['attributes'])) {
                $this->syncAttributes($product, $data['attributes']);
            }
            
            DB::commit();
            
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data, $request = null, ?ImageOptimizerService $imageOptimizer = null)
    {
        try {
            DB::beginTransaction();
            
            // Handle slug if name changed
            if (isset($data['name']) && $data['name'] !== $product->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);
            }
            
            // Handle specifications
            if (isset($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }
            
            // Handle tags
            if (isset($data['tags'])) {
                $tags = explode(',', $data['tags']);
                $data['tags'] = json_encode(array_map('trim', $tags));
            }
            
            $product->update($data);
            
            // Handle image deletions
            if ($request && $request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $this->deleteImage($imageId, $imageOptimizer);
                }
            }
            
            // Handle new images if any
            if ($request && $request->hasFile('new_images')) {
                $this->uploadImages($product, $request->file('new_images'), $imageOptimizer);
            }
            
            // Set primary image
            if ($request && $request->has('primary_image')) {
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                ProductImage::where('id', $request->primary_image)->update(['is_primary' => true]);
            }
            
            // Handle attributes if any
            if (isset($data['attributes']) && !empty($data['attributes'])) {
                $this->syncAttributes($product, $data['attributes']);
            }
            
            DB::commit();
            
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Upload multiple images for a product with optimization
     */
    public function uploadImages(Product $product, $images, ?ImageOptimizerService $imageOptimizer = null)
    {
        $currentCount = $product->images()->count();
        
        foreach ($images as $index => $image) {
            try {
                // Optimize and upload image using ImageOptimizerService
                if ($imageOptimizer) {
                    $path = $imageOptimizer->upload($image, 'products');
                } else {
                    // Fallback to simple upload
                    $path = $image->store('products', 'public');
                }
                
                $product->images()->create([
                    'url' => $path,
                    'is_primary' => $currentCount === 0 && $index === 0, // First image is primary only if no images exist
                    'sort_order' => $currentCount + $index
                ]);
                
            } catch (\Exception $e) {
                Log::error('Image upload failed: ' . $e->getMessage());
                throw $e;
            }
        }
    }
    
    /**
     * Sync attributes with the product
     */
    public function syncAttributes(Product $product, array $attributes)
    {
        $syncData = [];
        
        foreach ($attributes as $attributeId => $values) {
            if (is_array($values)) {
                foreach ($values as $valueId) {
                    $syncData[$valueId] = ['attribute_id' => $attributeId];
                }
            }
        }
        
        if (!empty($syncData)) {
            $product->attributeValues()->sync($syncData);
        }
    }
    
    /**
     * Delete a product
     */
    public function deleteProduct(Product $product, ?ImageOptimizerService $imageOptimizer = null)
    {
        try {
            DB::beginTransaction();
            
            // Delete associated images from storage
            foreach ($product->images as $image) {
                $this->deleteImageFile($image->url, $imageOptimizer);
                $image->delete();
            }
            
            // Detach attributes
            $product->attributeValues()->detach();
            
            // Delete the product
            $product->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function deleteImage($imageId, ?ImageOptimizerService $imageOptimizer = null)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            
            // Get the original storage path (not the URL)
            $storagePath = $image->getRawOriginal('url');
            
            \Log::info('Deleting image:', [
                'image_id' => $imageId,
                'url' => $image->url,
                'storage_path' => $storagePath
            ]);
            
            // Delete file from storage using optimizer
            if ($imageOptimizer) {
                $imageOptimizer->delete($storagePath);
            } else {
                // Fallback delete
                if (Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }
            
            // Check if this was primary image
            $wasPrimary = $image->is_primary;
            
            $image->delete();
            
            // If deleted image was primary, set another image as primary
            if ($wasPrimary) {
                $newPrimary = ProductImage::where('product_id', $image->product_id)->first();
                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage(), [
                'image_id' => $imageId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Delete image file from storage
     */
    protected function deleteImageFile($url, ?ImageOptimizerService $imageOptimizer = null)
    {
        // Remove 'storage/' prefix if exists
        $path = str_replace('storage/', '', $url);
        
        if ($imageOptimizer) {
            $imageOptimizer->delete($path);
        } else {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
    
    /**
     * Get product statistics
     */
    public function getProductStats()
    {
        return [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '<', 5)->where('stock', '>', 0)->count(),
        ];
    }
    
    /**
     * Bulk delete products
     */
    public function bulkDelete(array $productIds, ?ImageOptimizerService $imageOptimizer = null)
    {
        try {
            DB::beginTransaction();
            
            foreach ($productIds as $id) {
                $product = Product::find($id);
                if ($product) {
                    $this->deleteProduct($product, $imageOptimizer);
                }
            }
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export products to CSV
     */
    public function exportProducts($request)
    {
        $products = $this->getFilteredProducts($request);
        
        $csvData = [];
        $csvData[] = ['ID', 'Name', 'SKU', 'Price', 'Stock', 'Status', 'Category', 'Brand'];
        
        foreach ($products as $product) {
            $csvData[] = [
                $product->id,
                $product->name,
                $product->sku,
                $product->base_price,
                $product->stock,
                $product->status,
                $product->category->name ?? 'N/A',
                $product->brand->name ?? 'N/A'
            ];
        }
        
        return $csvData;
    }
}