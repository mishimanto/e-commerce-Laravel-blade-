<?php

namespace App\Services\Compare;

use App\Models\Compare;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CompareService
{
    protected $maxItems = 4;

    /**
     * Get compare list items
     */
    public function getCompareList()
    {
        if (auth()->check()) {
            $items = Compare::where('user_id', auth()->id())
                ->with('product.images', 'product.brand', 'product.reviews', 'product.category')
                ->get();
        } else {
            $items = Compare::where('session_id', session()->getId())
                ->with('product.images', 'product.brand', 'product.reviews', 'product.category')
                ->get();
        }

        return $items;
    }

    /**
     * Add product to compare
     */
    public function addProduct($productId)
    {
        return DB::transaction(function () use ($productId) {
            // Check if already in compare
            if ($this->isInCompare($productId)) {
                throw new \Exception('Product already in compare list');
            }

            // Check limit
            if ($this->getCount() >= $this->maxItems) {
                throw new \Exception("You can compare up to {$this->maxItems} products at a time");
            }

            $data = ['product_id' => $productId];

            if (auth()->check()) {
                $data['user_id'] = auth()->id();
                return Compare::create($data);
            }

            $data['session_id'] = session()->getId();
            return Compare::create($data);
        });
    }

    /**
     * Remove product from compare
     */
    public function removeProduct($productId)
    {
        if (auth()->check()) {
            return Compare::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();
        }

        return Compare::where('session_id', session()->getId())
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Clear compare list
     */
    public function clearCompare()
    {
        if (auth()->check()) {
            return Compare::where('user_id', auth()->id())->delete();
        }

        return Compare::where('session_id', session()->getId())->delete();
    }

    /**
     * Check if product is in compare
     */
    public function isInCompare($productId)
    {
        if (auth()->check()) {
            return Compare::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->exists();
        }

        return Compare::where('session_id', session()->getId())
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get compare count
     */
    public function getCount()
    {
        if (auth()->check()) {
            return Compare::where('user_id', auth()->id())->count();
        }

        return Compare::where('session_id', session()->getId())->count();
    }

    /**
     * Get comparison table data
     */
    public function getComparisonData()
    {
        $items = $this->getCompareList();

        if ($items->isEmpty()) {
            return [];
        }

        $products = $items->pluck('product');

        // Get all unique attributes across products
        $allSpecs = [];
        foreach ($products as $product) {
            $specs = json_decode($product->specifications, true) ?? [];
            foreach ($specs as $key => $value) {
                $allSpecs[$key] = $key;
            }
        }

        return [
            'products' => $products,
            'specifications' => array_keys($allSpecs),
            'count' => $products->count()
        ];
    }

    /**
     * Get common attributes across compared products
     */
    public function getCommonAttributes()
    {
        $items = $this->getCompareList();
        
        if ($items->count() < 2) {
            return [];
        }

        $products = $items->pluck('product');
        $firstProductSpecs = json_decode($products->first()->specifications, true) ?? [];

        $common = [];
        foreach ($firstProductSpecs as $key => $value) {
            $isCommon = true;
            
            foreach ($products->skip(1) as $product) {
                $specs = json_decode($product->specifications, true) ?? [];
                if (!isset($specs[$key])) {
                    $isCommon = false;
                    break;
                }
            }

            if ($isCommon) {
                $common[] = $key;
            }
        }

        return $common;
    }

    /**
     * Get unique attributes per product
     */
    public function getUniqueAttributes()
    {
        $items = $this->getCompareList();
        $unique = [];

        foreach ($items as $item) {
            $product = $item->product;
            $specs = json_decode($product->specifications, true) ?? [];
            
            $unique[$product->id] = array_keys($specs);
        }

        return $unique;
    }
}