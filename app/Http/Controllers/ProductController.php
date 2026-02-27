<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\RecentlyViewed;
use App\Services\Product\ProductService;
use App\Services\Search\SearchService;
use App\Services\Product\SidebarService;
use App\Services\Seo\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $productService;
    protected $searchService;
    protected $seoService;
    protected $sidebarService;

    public function __construct(ProductService $productService, SearchService $searchService, SeoService $seoService, SidebarService $sidebarService) 
    {
        $this->productService = $productService;
        $this->searchService = $searchService;
        $this->seoService = $seoService;
        $this->sidebarService = $sidebarService;
    }

    public function index(Request $request)
    {
        $filters = $this->buildFilters($request);
        
        // Get products with filters
        $products = $this->searchService->search($filters);

        $sidebar = $this->sidebarService->get();
        
        // // Get categories for filter sidebar
        // $categories = Category::with('children')
        //     ->whereNull('parent_id')
        //     ->where('status', true)
        //     ->orderBy('sort_order')
        //     ->get();
        
        // // Get brands for filter sidebar
        // $brands = Brand::where('status', true)
        //     ->orderBy('name')
        //     ->get();

        // // Get attributes for filtering
        // $attributes = Attribute::with('values')
        //     ->where('is_filterable', true)
        //     ->orderBy('sort_order')
        //     ->get();

        // Set SEO for products listing page
        $this->seoService->setProductsPage();

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $sidebar['categories'],
            'brands' => $sidebar['brands'],
            'attributes' => $sidebar['attributes']
        ]);
    }

    public function byCategory($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $filters = $this->buildFilters($request);
        $filters['category'] = $category->id;
        
        $products = $this->searchService->search($filters);

        $sidebar = $this->sidebarService->get();
        
        // $categories = Category::with('children')
        //     ->whereNull('parent_id')
        //     ->where('status', true)
        //     ->orderBy('sort_order')
        //     ->get();
        
        // $brands = Brand::where('status', true)
        //     ->orderBy('name')
        //     ->get();

        // // Get attributes for filtering
        // $attributes = Attribute::with('values')
        //     ->where('is_filterable', true)
        //     ->orderBy('sort_order')
        //     ->get();

        // Set SEO for category page
        $this->seoService->setCategoryPage($category);

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $sidebar['categories'],
            'brands' => $sidebar['brands'],
            'attributes' => $sidebar['attributes'],
            'category' => $category
        ]);
    }

    public function byBrand($slug, Request $request)
    {
        $brand = Brand::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $filters = $this->buildFilters($request);
        $filters['brand'] = $brand->id;

        $products = $this->searchService->search($filters);

        $sidebar = $this->sidebarService->get();

        $this->seoService->setBrandPage($brand);

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $sidebar['categories'],
            'brands' => $sidebar['brands'],
            'attributes' => $sidebar['attributes'],
            'brand' => $brand
        ]);
    }

    public function show($slug)
    {
        $product = Product::with([
            'category:id,name,slug',
            'brand:id,name,slug,logo',
            'images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'),
            'activeVariants',
            'reviews' => fn($q) => $q->with('user:id,name')->latest()->limit(10)
        ])
        ->where('slug', $slug)
        ->where('status', 'active')
        ->firstOrFail();

        // async view increment
        $this->incrementViewCountAsync($product);

        // related products optimized (no random sort)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->latest()
            ->with(['brand:id,name', 'images'])
            ->limit(4)
            ->get();

        return view('storefront.products.show', compact(
            'product',
            'relatedProducts'
        ));
    }

    public function quickView($id)
    {
        $cacheKey = 'quick_view_product_' . $id;

        return Cache::remember($cacheKey, 3600, function () use ($id) {

            $product = Product::select([
                    'id','name','slug','base_price','sale_price',
                    'stock','short_description','specifications','brand_id'
                ])
                ->with([
                    'brand:id,name,slug',
                    'images:id,product_id,url,is_primary'
                ])
                ->findOrFail($id);

            $discount = null;
            if ($product->sale_price && $product->sale_price < $product->base_price) {
                $discount = (int) round((($product->base_price - $product->sale_price) / $product->base_price) * 100);
            }

            $imageUrl = $this->getOptimizedImageUrl($product);
            $specifications = $this->parseSpecifications($product->specifications);

            $html = view('storefront.products.quick-view', compact(
                'product',
                'discount',
                'imageUrl',
                'specifications'
            ))->render();

            return [
                'success' => true,
                'html' => $html
            ];
        });
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q');

        if (!$keyword) {
            return redirect()->route('product.index');
        }

        $filters = $this->buildFilters($request);
        $filters['keyword'] = $keyword;

        $products = $this->searchService->search($filters);
        $this->searchService->logSearch($keyword, $products->total());

        $sidebar = $this->sidebarService->get();

        $this->seoService->setSearchPage($keyword);

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $sidebar['categories'],
            'brands' => $sidebar['brands'],
            'attributes' => $sidebar['attributes'],
            'keyword' => $keyword
        ]);
    }

    public function suggestions(Request $request)
    {
        $keyword = $request->get('q');
        
        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->searchService->getSuggestions($keyword);

        return response()->json($suggestions);
    }

    public function featured()
    {
        $products = Product::select('id','name','slug','stock','thumbnail','brand_id')
            ->with([
                'brand:id,name',
                'images',
                'variants' => fn($q)=>$q->where('stock','>',0)
            ])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->whereHas('variants', fn($q)=>$q->where('stock','>',0))
            ->paginate(12);

        $this->seoService->setProductsPage('Featured Products');

        return view('storefront.products.featured', compact('products'));
    }

    public function newArrivals()
    {
        $products = Product::with(['brand:id,name', 'images'])
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        $this->seoService->setProductsPage('New Arrivals');

        return view('storefront.products.new', compact('products'));
    }

    public function sale()
    {
        $products = Product::with(['brand:id,name', 'images'])
            ->whereColumn('sale_price', '<', 'base_price')
            ->where('status', 'active')
            ->paginate(12);

        $this->seoService->setProductsPage('Sale Products');

        return view('storefront.products.sale', compact('products'));
    }

    protected function buildFilters(Request $request)
    {
        $filters = [];
        
        // Handle categories
        if ($request->has('categories')) {
            $filters['categories'] = $request->input('categories');
        }
        
        // Handle brands
        if ($request->has('brands')) {
            $filters['brands'] = $request->input('brands');
        }
        
        // Handle price range
        if ($request->has('min_price') && $request->min_price !== null) {
            $filters['min_price'] = (float) $request->input('min_price');
        }
        if ($request->has('max_price') && $request->max_price !== null) {
            $filters['max_price'] = (float) $request->input('max_price');
        }
        
        // Handle attributes
        $attributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)->get();
        });
        foreach ($attributes as $attribute) {
            $paramName = 'attr_' . $attribute->id;
            if ($request->has($paramName)) {
                $values = $request->input($paramName);
                if (!empty($values)) {
                    $filters['attributes'][$attribute->id] = $values;
                }
            }
        }
        
        // Handle sorting
        if ($request->has('sort')) {
            $filters['sort'] = $request->input('sort');
        }
        
        // Handle pagination
        if ($request->has('per_page')) {
            $filters['per_page'] = (int) $request->input('per_page');
        }
        
        return $filters;
    }

    private function getOptimizedImageUrl($product)
    {
        // Get primary image efficiently
        $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
        
        if (!$primaryImage) {
            return asset('images/no-image.jpg');
        }
        
        $url = $primaryImage->url;
        
        // Return early if it's already a full URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        // Handle storage paths efficiently
        if (str_starts_with($url, 'storage/')) {
            return asset($url);
        }
        
        if (str_starts_with($url, '/storage/')) {
            return asset($url);
        }
        
        return asset('storage/' . ltrim($url, '/'));
    }

    private function parseSpecifications($specs)
    {
        if (empty($specs)) {
            return [];
        }
        
        // If it's already an array, process it
        if (is_array($specs)) {
            return $this->formatSpecifications($specs);
        }
        
        // If it's a string, try to decode it
        if (is_string($specs)) {
            $decoded = json_decode($specs, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->formatSpecifications($decoded);
            }
        }
        
        return [];
    }

    private function formatSpecifications(array $specs)
    {
        $formatted = [];
        $maxSpecs = 8; // Limit to 8 specifications for quick view
        
        foreach ($specs as $key => $value) {
            if (count($formatted) >= $maxSpecs) {
                break;
            }
            
            // Handle different specification formats
            if (is_numeric($key) && is_array($value)) {
                if (isset($value['key']) && isset($value['value'])) {
                    $formatted[$value['key']] = $value['value'];
                }
            } elseif (!is_numeric($key) && !is_array($value)) {
                $formatted[$key] = $value;
            } elseif (!is_numeric($key) && is_array($value) && isset($value['value'])) {
                $formatted[$key] = $value['value'];
            }
        }
        
        return $formatted;
    }

    protected function incrementViewCountAsync($product)
    {
        try {
            DB::statement("UPDATE products SET views = views + 1 WHERE id = ?", [$product->id]);
        } catch (\Exception $e) {
            \Log::error('Failed to increment view count: ' . $e->getMessage());
        }
    }

    protected function recordRecentlyViewedAsync($product)
    {
        try {
            $data = [
                'product_id' => $product->id,
                'viewed_at' => now()
            ];

            if (auth()->check()) {
                $data['user_id'] = auth()->id();
                $data['session_id'] = null;
            } else {
                $data['session_id'] = session()->getId();
                $data['user_id'] = null;
            }

            RecentlyViewed::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'session_id' => $data['session_id'],
                    'product_id' => $product->id
                ],
                ['viewed_at' => now()]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to record recently viewed: ' . $e->getMessage());
        }
    }

    protected function recordRecentlyViewed(Product $product)
    {
        $this->recordRecentlyViewedAsync($product);
    }
}