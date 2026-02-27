<?php

namespace App\Services\Search;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Search products with filters
     */
    public function search(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['brand', 'images' => function($q) {
                $q->where('is_primary', true)
                  ->orWhereIn('id', function($sub) {
                      $sub->select(DB::raw('MIN(id)'))
                          ->from('product_images')
                          ->groupBy('product_id');
                  })
                  ->limit(1);
            }])
            ->where('status', 'active');

        // Apply keyword search
        if (!empty($filters['keyword'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['keyword'] . '%')
                  ->orWhere('short_description', 'LIKE', '%' . $filters['keyword'] . '%')
                  ->orWhere('description', 'LIKE', '%' . $filters['keyword'] . '%')
                  ->orWhere('sku', 'LIKE', '%' . $filters['keyword'] . '%');
            });
        }

        // Apply category filter
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Apply multiple categories filter
        if (!empty($filters['categories']) && is_array($filters['categories'])) {
            $query->whereIn('category_id', $filters['categories']);
        }

        // Apply brand filter
        if (!empty($filters['brand'])) {
            $query->where('brand_id', $filters['brand']);
        }

        // Apply multiple brands filter
        if (!empty($filters['brands']) && is_array($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }

        // Apply price range filter
        if (isset($filters['min_price']) && $filters['min_price'] !== null && $filters['min_price'] > 0) {
            $query->where(function($q) use ($filters) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sale_price')
                        ->whereColumn('sale_price', '<', 'base_price');
                })->where('sale_price', '>=', $filters['min_price'])
                  ->orWhere(function($sub) use ($filters) {
                      $sub->where(function($inner) {
                          $inner->whereNull('sale_price')
                                ->orWhereColumn('sale_price', '>=', 'base_price');
                      })->where('base_price', '>=', $filters['min_price']);
                  });
            });
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== null && $filters['max_price'] > 0) {
            $query->where(function($q) use ($filters) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sale_price')
                        ->whereColumn('sale_price', '<', 'base_price');
                })->where('sale_price', '<=', $filters['max_price'])
                  ->orWhere(function($sub) use ($filters) {
                      $sub->where(function($inner) {
                          $inner->whereNull('sale_price')
                                ->orWhereColumn('sale_price', '>=', 'base_price');
                      })->where('base_price', '<=', $filters['max_price']);
                  });
            });
        }

        // Apply attribute filters
        if (!empty($filters['attributes']) && is_array($filters['attributes'])) {
            foreach ($filters['attributes'] as $attributeId => $values) {
                if (!empty($values)) {
                    $values = is_array($values) ? $values : [$values];
                    $query->whereHas('attributeValues', function($q) use ($attributeId, $values) {
                        $q->where('attribute_id', $attributeId)
                          ->whereIn('attribute_value_id', $values);
                    });
                }
            }
        }

        // Apply sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, base_price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, base_price) DESC');
                break;
            case 'popular':
                $query->orderBy('views', 'DESC');
                break;
            case 'rating':
                $query->orderBy('rating', 'DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'DESC');
                break;
        }

        // Get paginated results
        $perPage = $filters['per_page'] ?? 12;
        
        return $query->paginate($perPage);
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions(string $keyword): array
    {
        if (strlen($keyword) < 2) {
            return [];
        }

        $products = Product::where('name', 'LIKE', '%' . $keyword . '%')
            ->where('status', 'active')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'base_price', 'sale_price']);

        $suggestions = [];
        foreach ($products as $product) {
            $suggestions[] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->sale_price ?? $product->base_price,
                'url' => route('product.show', $product->slug)
            ];
        }

        return $suggestions;
    }

    /**
     * Log search query
     */
    public function logSearch(string $keyword, int $resultsCount): void
    {
        try {
            DB::table('search_logs')->insert([
                'keyword' => $keyword,
                'results_count' => $resultsCount,
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the search
            \Log::error('Failed to log search: ' . $e->getMessage());
        }
    }
}