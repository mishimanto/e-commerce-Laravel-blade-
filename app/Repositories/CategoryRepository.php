<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active categories
     */
    public function getActiveCategories()
    {
        return Cache::remember('active_categories', 3600, function () {
            return $this->model
                ->where('status', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get parent categories with children
     */
    public function getParentCategories($showAll = false)
{
    $cacheKey = $showAll ? 'all_parent_categories' : 'parent_categories';
    
    return Cache::remember($cacheKey, 3600, function () use ($showAll) {
        $query = $this->model
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order');
        
        if (!$showAll) {
            $query->where('status', true);
        }
        
        return $query->get();
    });
}

    /**
     * Get category tree
     */
    public function getCategoryTree()
    {
        return Cache::remember('category_tree', 3600, function () {
            return $this->buildTree($this->model->whereNull('parent_id')->get());
        });
    }

    /**
     * Build category tree recursively
     */
    protected function buildTree($categories)
    {
        foreach ($categories as $category) {
            if ($category->children->isNotEmpty()) {
                $category->children = $this->buildTree($category->children);
            }
        }
        
        return $categories;
    }

    /**
     * Find by slug
     */
    public function findBySlug($slug)
    {
        return Cache::remember("category_{$slug}", 3600, function () use ($slug) {
            return $this->model
                ->with('parent')
                ->where('slug', $slug)
                ->where('status', true)
                ->first();
        });
    }

    /**
     * Get category with products
     */
    public function getCategoryWithProducts($slug, $perPage = 12)
    {
        return $this->model
            ->with(['products' => function ($query) {
                $query->with(['brand', 'images'])
                    ->where('status', 'active')
                    ->latest();
            }])
            ->where('slug', $slug)
            ->where('status', true)
            ->first();
    }

    /**
     * Get featured categories
     */
    public function getFeaturedCategories($limit = 6)
    {
        return Cache::remember('featured_categories', 3600, function () use ($limit) {
            return $this->model
                ->where('is_featured', true)
                ->where('status', true)
                ->orderBy('sort_order')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get category breadcrumb
     */
    public function getBreadcrumb($categoryId)
    {
        $breadcrumb = [];
        $category = $this->find($categoryId);

        while ($category) {
            array_unshift($breadcrumb, [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ]);
            $category = $category->parent;
        }

        return $breadcrumb;
    }

    /**
     * Move category up in sort order
     */
    public function moveUp($id)
    {
        $category = $this->find($id);
        $previous = $this->model
            ->where('parent_id', $category->parent_id)
            ->where('sort_order', '<', $category->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previous) {
            $temp = $category->sort_order;
            $category->sort_order = $previous->sort_order;
            $previous->sort_order = $temp;
            
            $category->save();
            $previous->save();
            
            Cache::forget('active_categories');
            Cache::forget('parent_categories');
            Cache::forget('category_tree');
        }

        return $category;
    }

    /**
     * Move category down in sort order
     */
    public function moveDown($id)
    {
        $category = $this->find($id);
        $next = $this->model
            ->where('parent_id', $category->parent_id)
            ->where('sort_order', '>', $category->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            $temp = $category->sort_order;
            $category->sort_order = $next->sort_order;
            $next->sort_order = $temp;
            
            $category->save();
            $next->save();
            
            Cache::forget('active_categories');
            Cache::forget('parent_categories');
            Cache::forget('category_tree');
        }

        return $category;
    }

    /**
     * Get category counts
     */
    public function getCategoryCounts()
    {
        return Cache::remember('category_counts', 3600, function () {
            return $this->model
                ->withCount('products')
                ->get()
                ->pluck('products_count', 'id')
                ->toArray();
        });
    }

    /**
     * Search categories
     */
    public function searchCategories($keyword, $limit = 10)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->where('status', true)
            ->limit($limit)
            ->get();
    }
}