<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Services\Admin\ProductService;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;
    protected $imageOptimizer;

    public function __construct(ProductService $productService, ImageOptimizerService $imageOptimizer)
    {
        $this->productService = $productService;
        $this->imageOptimizer = $imageOptimizer;
        
        // Configure image optimization for products
        $this->imageOptimizer->setConfig([
            'max_width' => 1200,
            'max_height' => 1200,
            'quality' => 85,
            'format' => 'webp',
            'strip_exif' => true,
        ]);
        
        // Middleware for permissions
        // $this->middleware('permission:view-products')->only(['index', 'show']);
        // $this->middleware('permission:create-products')->only(['create', 'store']);
        // $this->middleware('permission:edit-products')->only(['edit', 'update']);
        // $this->middleware('permission:delete-products')->only(['destroy', 'bulkDelete']);
    }

    public function index(Request $request)
    {
        $products = $this->productService->getFilteredProducts($request);
        $stats = $this->productService->getProductStats();
        
        // Get categories and brands for filters
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        
        return view('admin.products.index', compact('products', 'stats', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $attributes = Attribute::with('values')->orderBy('name')->get();
        
        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,draft',
            'weight' => 'nullable|numeric|min:0',
            'warranty' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'tags' => 'nullable|string',
            'specifications' => 'nullable|array',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Generate slug from name
            $slug = Str::slug($validated['name']);
            
            // Make sure slug is unique
            $count = 1;
            $originalSlug = $slug;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            
            // Add slug to validated data
            $validated['slug'] = $slug;
            
            // ========== FIX: Process specifications to proper array format ==========
            if ($request->has('specifications') && is_array($request->specifications)) {
                $processedSpecs = [];
                
                foreach ($request->specifications as $spec) {
                    // Skip empty specifications
                    if (empty($spec['key']) && empty($spec['value'])) {
                        continue;
                    }
                    
                    // Only include if key is not empty
                    if (!empty(trim($spec['key']))) {
                        $processedSpecs[] = [
                            'key' => trim($spec['key']),
                            'value' => trim($spec['value'] ?? '')
                        ];
                    }
                }
                
                $validated['specifications'] = $processedSpecs;
            } else {
                $validated['specifications'] = [];
            }
            // =======================================================================
            
            $product = $this->productService->createProduct($validated, $request, $this->imageOptimizer);
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to create product. ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'attributes']);
        
        return view('admin.products.show', compact('product'));
    }
    
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $attributes = Attribute::with('values')->orderBy('name')->get();
        
        // Load product attributes and images
        $product->load('attributes', 'images');
        
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,draft',
            'weight' => 'nullable|numeric|min:0',
            'warranty' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'tags' => 'nullable|string',
            'specifications' => 'nullable|array',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'primary_image' => 'nullable|exists:product_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Check if name changed and update slug accordingly
            if ($product->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                
                // Make sure slug is unique (excluding current product)
                $count = 1;
                $originalSlug = $slug;
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $validated['slug'] = $slug;
            }
            
            // ========== FIX: Process specifications to proper array format ==========
            if ($request->has('specifications') && is_array($request->specifications)) {
                $processedSpecs = [];
                
                foreach ($request->specifications as $spec) {
                    // Skip empty specifications
                    if (empty($spec['key']) && empty($spec['value'])) {
                        continue;
                    }
                    
                    // Only include if key is not empty
                    if (!empty(trim($spec['key']))) {
                        $processedSpecs[] = [
                            'key' => trim($spec['key']),
                            'value' => trim($spec['value'] ?? '')
                        ];
                    }
                }
                
                $validated['specifications'] = $processedSpecs;
            } else {
                $validated['specifications'] = [];
            }
            // =======================================================================
            
            // ========== IMAGE DELETE LOGIC ==========
            // Handle image deletions (BrandController এর মত)
            if ($request->has('delete_images') && !empty($request->delete_images)) {
                foreach ($request->delete_images as $imageId) {
                    $this->productService->deleteImage($imageId, $this->imageOptimizer);
                }
            }
            // =======================================
            
            // Update product with new images
            $this->productService->updateProduct($product, $validated, $request, $this->imageOptimizer);
            
            // Set primary image
            if ($request->has('primary_image')) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $request->primary_image)->update(['is_primary' => true]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to update product. ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            
            // Check if product has orders
            if ($product->orderItems()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete product with associated orders.');
            }
            
            // Delete all images using optimizer
            foreach ($product->images as $image) {
                // Get original storage path
                $storagePath = $image->getRawOriginal('url');
                $this->imageOptimizer->delete($storagePath);
            }
            
            // Detach attributes
            $product->attributeValues()->detach();
            
            // Delete the product
            $product->delete();
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete product. ' . $e->getMessage());
        }
    }
    
    public function duplicate(Product $product)
    {
        try {
            DB::beginTransaction();
            
            $newProduct = $product->replicate();
            $newProduct->sku = $product->sku . '-copy';
            $newProduct->name = $product->name . ' (Copy)';
            
            // Generate unique slug for duplicate
            $slug = Str::slug($newProduct->name);
            $count = 1;
            $originalSlug = $slug;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $newProduct->slug = $slug;
            
            $newProduct->save();
            
            // Replicate images if any (same file path, no need to duplicate file)
            foreach ($product->images as $image) {
                $newProduct->images()->create([
                    'url' => $image->url,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order
                ]);
            }
            
            // Replicate attributes
            foreach ($product->attributes as $attribute) {
                $newProduct->attributes()->attach($attribute->id, [
                    'attribute_value_id' => $attribute->pivot->attribute_value_id
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product duplicated successfully.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product duplication failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate product. ' . $e->getMessage()
            ], 500);
        }
    }

    public function variants($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $variants = $product->variants()->paginate(15);
        
        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    public function deleteImage($imageId)
    {
        try {
            DB::beginTransaction();
            
            $this->productService->deleteImage($imageId, $this->imageOptimizer);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Image deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image. ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);
        
        try {
            DB::beginTransaction();
            
            $deletedCount = 0;
            $skippedCount = 0;
            $failedProducts = [];
            
            foreach ($request->ids as $id) {
                $product = Product::find($id);
                
                // Skip products with orders
                if ($product->orderItems()->count() > 0) {
                    $skippedCount++;
                    $failedProducts[] = $product->name;
                    continue;
                }
                
                // ========== BULK DELETE IMAGES ==========
                // Delete all images using optimizer
                foreach ($product->images as $image) {
                    $this->imageOptimizer->delete($image->url);
                }
                
                // Detach attributes
                $product->attributeValues()->detach();
                
                // Force delete the product
                $product->forceDelete();
                $deletedCount++;
                // ========================================
            }
            
            DB::commit();
            
            $message = "{$deletedCount} products deleted successfully.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} products were skipped because they have associated orders: " . implode(', ', $failedProducts);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'skipped_count' => $skippedCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete products. ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function export(Request $request)
    {
        $csvData = $this->productService->exportProducts($request);
        
        // Generate and download CSV
        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Get product images
     */
    public function getImages(Product $product)
    {
        try {
            $images = $product->images()->orderBy('sort_order')->get();
            
            $imageData = $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $this->imageOptimizer->getUrl($image->url),
                    'is_primary' => $image->is_primary,
                    'dimensions' => $this->imageOptimizer->getDimensions($image->url),
                    'sort_order' => $image->sort_order
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $imageData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get images failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load images: ' . $e->getMessage()
            ], 500);
        }
    }
}