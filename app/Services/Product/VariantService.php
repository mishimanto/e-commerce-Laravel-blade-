<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VariantService
{
    /**
     * Create a new variant
     */
    public function createVariant(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSku($product);
            }

            $variant = $product->variants()->create([
                'sku' => $data['sku'],
                'attributes' => $data['attributes'],
                'price_adjustment' => $data['price_adjustment'] ?? 0,
                'stock' => $data['stock'] ?? 0,
                'image' => $data['image'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);

            // Upload variant image if provided
            if (isset($data['image_file']) && $data['image_file']) {
                $this->uploadVariantImage($variant, $data['image_file']);
            }

            return $variant;
        });
    }

    /**
     * Update an existing variant
     */
    public function updateVariant(ProductVariant $variant, array $data)
    {
        return DB::transaction(function () use ($variant, $data) {
            $variant->update([
                'sku' => $data['sku'] ?? $variant->sku,
                'attributes' => $data['attributes'] ?? $variant->attributes,
                'price_adjustment' => $data['price_adjustment'] ?? $variant->price_adjustment,
                'stock' => $data['stock'] ?? $variant->stock,
                'status' => $data['status'] ?? $variant->status,
            ]);

            // Upload new variant image if provided
            if (isset($data['image_file']) && $data['image_file']) {
                $this->uploadVariantImage($variant, $data['image_file']);
            }

            return $variant;
        });
    }

    /**
     * Delete a variant
     */
    public function deleteVariant(ProductVariant $variant)
    {
        return DB::transaction(function () use ($variant) {
            // Delete variant image
            if ($variant->image && !str_contains($variant->image, 'via.placeholder.com')) {
                $path = str_replace('/storage/', '', parse_url($variant->image, PHP_URL_PATH));
                \Storage::disk('public')->delete($path);
            }

            return $variant->delete();
        });
    }

    /**
     * Upload variant image
     */
    public function uploadVariantImage(ProductVariant $variant, $imageFile)
    {
        // Delete old image
        if ($variant->image && !str_contains($variant->image, 'via.placeholder.com')) {
            $oldPath = str_replace('/storage/', '', parse_url($variant->image, PHP_URL_PATH));
            \Storage::disk('public')->delete($oldPath);
        }

        // Upload new image
        $path = $imageFile->store('products/variants/' . date('Y/m'), 'public');
        
        $variant->update([
            'image' => \Storage::url($path)
        ]);

        return $variant;
    }

    /**
     * Update variant stock
     */
    public function updateStock(ProductVariant $variant, $quantity, $operation = 'subtract')
    {
        if ($operation === 'subtract') {
            $variant->decrement('stock', $quantity);
        } else {
            $variant->increment('stock', $quantity);
        }

        // Update parent product total stock
        $this->updateProductTotalStock($variant->product);

        return $variant;
    }

    /**
     * Update product total stock based on variants
     */
    public function updateProductTotalStock(Product $product)
    {
        $totalStock = $product->variants()->sum('stock');
        $product->update(['stock' => $totalStock]);
    }

    /**
     * Generate unique SKU for variant
     */
    protected function generateSku(Product $product)
    {
        $baseSku = $product->sku;
        $count = $product->variants()->count() + 1;
        
        do {
            $sku = $baseSku . '-VAR-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $count++;
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Get variant by attributes
     */
    public function findVariantByAttributes(Product $product, array $attributes)
    {
        return $product->variants()
            ->where('attributes', json_encode($attributes))
            ->first();
    }

    /**
     * Check if variant combination exists
     */
    public function variantExists(Product $product, array $attributes, $excludeId = null)
    {
        $query = $product->variants()
            ->where('attributes', json_encode($attributes));

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get variant price
     */
    public function getVariantPrice(ProductVariant $variant)
    {
        return $variant->product->base_price + $variant->price_adjustment;
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock(ProductVariant $variant, $quantity = 1)
    {
        return $variant->stock >= $quantity && $variant->status === 'active';
    }

    /**
     * Bulk create variants from attribute combinations
     */
    public function bulkCreateVariants(Product $product, array $attributeCombinations)
    {
        $variants = [];

        foreach ($attributeCombinations as $combination) {
            $variants[] = [
                'product_id' => $product->id,
                'sku' => $this->generateSku($product),
                'attributes' => json_encode($combination),
                'price_adjustment' => 0,
                'stock' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ProductVariant::insert($variants);

        return $product->variants()->get();
    }

    /**
     * Get available attribute combinations
     */
    public function getAvailableCombinations(Product $product)
    {
        return $product->variants()
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->get()
            ->map(function($variant) {
                return json_decode($variant->attributes, true);
            })
            ->filter()
            ->values()
            ->toArray();
    }
}