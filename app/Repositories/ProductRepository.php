<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Cache::remember("product_slug_{$slug}", 3600, function () use ($slug) {
            return $this->model
                ->with(['category', 'brand', 'images', 'variants', 'reviews'])
                ->where('slug', $slug)
                ->first();
        });
    }

    public function findWithRelations(string $slug, array $relations = []): ?Product
    {
        return $this->model
            ->with($relations)
            ->where('slug', $slug)
            ->first();
    }

    public function getActiveProducts(int $perPage = 12)
    {
        return Cache::remember('active_products_page_' . request('page', 1), 3600, function () use ($perPage) {
            return $this->model
                ->with(['category', 'brand', 'images'])
                ->active()
                ->latest()
                ->paginate($perPage);
        });
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        return Cache::remember('featured_products', 3600, function () use ($limit) {
            return $this->model
                ->with(['category', 'brand', 'images'])
                ->active()
                ->featured()
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    public function getTrendingProducts(int $limit = 6)
    {
        return Cache::remember('trending_products', 1800, function () use ($limit) {
            return $this->model
                ->with(['category', 'brand', 'images'])
                ->active()
                ->where('is_trending', true)
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    public function getByCategory(int $categoryId, int $perPage = 12)
    {
        return $this->model
            ->with(['category', 'brand', 'images'])
            ->active()
            ->where('category_id', $categoryId)
            ->paginate($perPage);
    }

    public function search(string $query, array $filters = [])
    {
        $products = $this->model
            ->with(['category', 'brand', 'images'])
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            });

        // Apply filters
        if (isset($filters['category'])) {
            $products->where('category_id', $filters['category']);
        }

        if (isset($filters['brand'])) {
            $products->where('brand_id', $filters['brand']);
        }

        if (isset($filters['min_price'])) {
            $products->where('base_price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $products->where('base_price', '<=', $filters['max_price']);
        }

        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_low':
                    $products->orderBy('base_price', 'asc');
                    break;
                case 'price_high':
                    $products->orderBy('base_price', 'desc');
                    break;
                case 'newest':
                    $products->orderBy('created_at', 'desc');
                    break;
                case 'popular':
                    $products->orderBy('views', 'desc');
                    break;
            }
        }

        return $products->paginate($filters['per_page'] ?? 12);
    }

    public function getForDisplay(string $slug): ?Product
    {
        return $this->model
            ->with([
                'category',
                'brand',
                'images',
                'variants',
                'reviews.user',
                'attributeValues.attribute'
            ])
            ->where('slug', $slug)
            ->first();
    }

    public function updateStock(int $id, int $quantity, string $operation = 'subtract'): bool
    {
        $product = $this->find($id);
        
        if (!$product) {
            return false;
        }

        if ($operation === 'subtract') {
            $product->decrement('stock', $quantity);
        } else {
            $product->increment('stock', $quantity);
        }

        // Clear cache
        $this->clearCache($product->slug);

        return true;
    }

    protected function clearCache(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget("product_slug_{$slug}");
        }
        
        Cache::forget('featured_products');
        Cache::forget('trending_products');
        Cache::forget('active_products_page_' . request('page', 1));
    }
}