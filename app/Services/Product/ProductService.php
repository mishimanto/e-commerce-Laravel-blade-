<?php
// app/Services/Product/ProductService.php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Create product
            $product = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'sku' => $data['sku'],
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'description' => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'specifications' => isset($data['specifications']) ? json_encode($data['specifications']) : null,
                'base_price' => $data['base_price'],
                'sale_price' => $data['sale_price'] ?? null,
                'stock' => $data['stock'] ?? 0,
                'status' => $data['status'] ?? 'draft',
                'is_featured' => $data['is_featured'] ?? false,
                'is_trending' => $data['is_trending'] ?? false,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
                'warranty' => $data['warranty'] ?? null,
                'tags' => isset($data['tags']) ? json_encode(explode(',', $data['tags'])) : null,
                'weight' => $data['weight'] ?? null,
            ]);

            // Upload images
            if (isset($data['images']) && !empty($data['images'])) {
                $this->uploadImages($product, $data['images']);
            }

            // Sync attributes
            if (isset($data['attributes']) && !empty($data['attributes'])) {
                $this->syncAttributes($product, $data['attributes']);
            }

            // Create variants
            if (isset($data['variants']) && !empty($data['variants'])) {
                $this->createVariants($product, $data['variants']);
            }

            // Clear cache
            $this->clearProductCache();

            return $product;
        });
    }

    public function updateProduct($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->productRepository->find($id);

            // Update slug if name changed and slug not provided
            if (isset($data['name']) && $data['name'] !== $product->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Update product
            $product->update([
                'name' => $data['name'] ?? $product->name,
                'slug' => $data['slug'] ?? $product->slug,
                'sku' => $data['sku'] ?? $product->sku,
                'category_id' => $data['category_id'] ?? $product->category_id,
                'brand_id' => $data['brand_id'] ?? $product->brand_id,
                'description' => $data['description'] ?? $product->description,
                'short_description' => $data['short_description'] ?? $product->short_description,
                'specifications' => isset($data['specifications']) ? json_encode($data['specifications']) : $product->specifications,
                'base_price' => $data['base_price'] ?? $product->base_price,
                'sale_price' => $data['sale_price'] ?? $product->sale_price,
                'stock' => $data['stock'] ?? $product->stock,
                'status' => $data['status'] ?? $product->status,
                'is_featured' => $data['is_featured'] ?? $product->is_featured,
                'is_trending' => $data['is_trending'] ?? $product->is_trending,
                'meta_title' => $data['meta_title'] ?? $product->meta_title,
                'meta_description' => $data['meta_description'] ?? $product->meta_description,
                'meta_keywords' => $data['meta_keywords'] ?? $product->meta_keywords,
                'warranty' => $data['warranty'] ?? $product->warranty,
                'tags' => isset($data['tags']) ? json_encode(explode(',', $data['tags'])) : $product->tags,
                'weight' => $data['weight'] ?? $product->weight,
            ]);

            // Upload new images
            if (isset($data['new_images']) && !empty($data['new_images'])) {
                $this->uploadImages($product, $data['new_images']);
            }

            // Update primary image
            if (isset($data['primary_image'])) {
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                ProductImage::where('id', $data['primary_image'])->update(['is_primary' => true]);
            }

            // Sync attributes
            if (isset($data['attributes'])) {
                $this->syncAttributes($product, $data['attributes']);
            }

            // Clear cache
            $this->clearProductCache($product->id);

            return $product;
        });
    }

    public function deleteProduct($id)
    {
        return DB::transaction(function () use ($id) {
            $product = $this->productRepository->find($id);

            // Delete images from storage
            foreach ($product->images as $image) {
                if ($image->url && !str_contains($image->url, 'via.placeholder.com')) {
                    $path = str_replace('/storage/', '', parse_url($image->url, PHP_URL_PATH));
                    Storage::disk('public')->delete($path);
                }
                $image->delete();
            }

            // Delete product
            $product->delete();

            // Clear cache
            $this->clearProductCache($id);

            return true;
        });
    }

    public function uploadImages(Product $product, array $images)
    {
        foreach ($images as $index => $imageFile) {
            $path = $imageFile->store('products/' . date('Y/m'), 'public');
            
            ProductImage::create([
                'product_id' => $product->id,
                'url' => Storage::url($path),
                'is_primary' => $index === 0 && $product->images->count() === 0,
                'sort_order' => $product->images->count() + $index
            ]);
        }
    }

    /**
     * Sync product attributes
     */
    public function syncAttributes(Product $product, array $attributes)
    {
        $syncData = [];
        
        foreach ($attributes as $attributeId => $values) {
            if (is_array($values)) {
                foreach ($values as $valueId) {
                    $syncData[$valueId] = ['attribute_id' => $attributeId];
                }
            } else {
                $syncData[$values] = ['attribute_id' => $attributeId];
            }
        }
        
        $product->attributeValues()->sync($syncData);
    }

    /**
     * Create product variants
     */
    public function createVariants(Product $product, array $variants)
    {
        foreach ($variants as $variantData) {
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $variantData['sku'] ?? $product->sku . '-' . Str::random(4),
                'attributes' => json_encode($variantData['attributes']),
                'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                'stock' => $variantData['stock'] ?? 0,
                'image' => $variantData['image'] ?? null,
                'status' => $variantData['status'] ?? 'active',
            ]);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock($productId, $quantity, $operation = 'subtract')
    {
        $product = $this->productRepository->find($productId);
        
        if ($operation === 'subtract') {
            $product->decrement('stock', $quantity);
        } else {
            $product->increment('stock', $quantity);
        }

        // Check low stock alert
        if ($product->stock <= config('settings.low_stock_threshold', 5)) {
            $this->sendLowStockAlert($product);
        }

        return $product;
    }

    /**
     * Duplicate a product
     */
    public function duplicateProduct($id)
    {
        return DB::transaction(function () use ($id) {
            $original = $this->productRepository->find($id);
            
            $newProduct = $original->replicate();
            $newProduct->name = $original->name . ' (Copy)';
            $newProduct->slug = Str::slug($original->name . '-copy-' . Str::random(4));
            $newProduct->sku = $original->sku . '-COPY';
            $newProduct->status = 'draft';
            $newProduct->created_at = now();
            $newProduct->save();

            // Duplicate images
            foreach ($original->images as $image) {
                $newImage = $image->replicate();
                $newImage->product_id = $newProduct->id;
                $newImage->save();
            }

            // Duplicate attributes
            $attributes = $original->attributeValues->pluck('id')->toArray();
            $newProduct->attributeValues()->sync($attributes);

            // Duplicate variants
            foreach ($original->variants as $variant) {
                $newVariant = $variant->replicate();
                $newVariant->product_id = $newProduct->id;
                $newVariant->sku = $variant->sku . '-COPY';
                $newVariant->save();
            }

            return $newProduct;
        });
    }

    /**
     * Get product with relations for display
     */
    public function getProductForDisplay($slug)
    {
        $product = $this->productRepository->findBySlug($slug);
        
        if (!$product) {
            return null;
        }

        // Increment view count
        $product->increment('views');

        return $product->load([
            'category',
            'brand',
            'images',
            'variants',
            'reviews.user',
            'attributeValues.attribute'
        ]);
    }

    /**
     * Get related products
     */
    public function getRelatedProducts(Product $product, $limit = 4)
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->with(['brand', 'images'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(Product $product, $quantity = 1, $variantId = null)
    {
        if ($variantId) {
            $variant = $product->variants()->find($variantId);
            return $variant && $variant->stock >= $quantity;
        }
        
        return $product->stock >= $quantity;
    }

    /**
     * Send low stock alert
     */
    protected function sendLowStockAlert(Product $product)
    {
        // Notify admins about low stock
        $admins = User::role(['admin', 'super-admin'])->get();
        
        foreach ($admins as $admin) {
            // $admin->notify(new LowStockNotification($product));
            // Temporarily comment out notification
            \Log::info('Low stock alert for product: ' . $product->name);
        }
    }

    /**
     * Clear product cache
     */
    protected function clearProductCache($productId = null)
    {
        if ($productId) {
            cache()->forget("product_{$productId}");
            cache()->forget("product_slug_" . request('slug'));
        }
        
        cache()->forget('featured_products');
        cache()->forget('trending_products');
        cache()->forget('new_products');
    }
}