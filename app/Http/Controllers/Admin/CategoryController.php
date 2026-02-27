<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageOptimizerService;
use App\Http\Requests\Admin\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CategoryController extends Controller
{

    protected $imageOptimizer;
    public function __construct(ImageOptimizerService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
        
        // Configure image optimization for categories
        $this->imageOptimizer->setConfig([
            'resize_mode' => 'aspect_ratio',
            'max_width' => 800,
            'max_height' => 800,
            'quality' => 80,
            'format' => 'webp',
        ]);
        
        // Middleware for permissions
        // $this->middleware('permission:view-categories')->only(['index', 'show']);
        // $this->middleware('permission:create-categories')->only(['create', 'store']);
        // $this->middleware('permission:edit-categories')->only(['edit', 'update']);
        // $this->middleware('permission:delete-categories')->only(['destroy']);
        // $this->middleware('permission:manage-categories')->only(['moveUp', 'moveDown', 'updateSortOrder', 'toggleStatus']);
    }

    public function index(Request $request)
    {
        $categoryList = Category::with(['parent', 'children' => function($query) {
                $query->withCount('products');
            }])
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        // Calculate additional stats
        $totalCategories = $categoryList->count();
        $activeCategories = $categoryList->where('status', 1)->count();
        $inactiveCategories = $categoryList->where('status', 0)->count();
        $subcategoriesCount = $categoryList->whereNotNull('parent_id')->count();
        $featuredCategoriesCount = $categoryList->where('is_featured', 1)->count();
        $totalProducts = $categoryList->sum('products_count');

        return view('admin.categories.index', compact(
            'categoryList', 
            'totalCategories', 
            'activeCategories', 
            'inactiveCategories',
            'subcategoriesCount', 
            'featuredCategoriesCount',
            'totalProducts'
        ));
    }
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        
        return view('admin.categories.create', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();

            // Handle image upload with optimization
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageOptimizer->upload(
                    $request->file('image'), 
                    'categories'
                );
            }

            $category = Category::create($data);
            
            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
            // Handle each checkbox independently
            // If the checkbox is present in the request (checked), set to true, otherwise false
            $data['status'] = $request->has('status') ? true : false;
            $data['is_featured'] = $request->has('is_featured') ? true : false;
            $data['show_in_menu'] = $request->has('show_in_menu') ? true : false;
            
            // Handle featured_order - set to 0 if not featured
            if (!$data['is_featured']) {
                $data['featured_order'] = 0;
            } else {
                // Keep the provided featured_order if featured, default to 0 if not set
                $data['featured_order'] = $request->input('featured_order', 0);
            }

            // Handle image upload with optimization
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageOptimizer->upload(
                    $request->file('image'), 
                    'categories',
                    $category->image // Old image path for deletion
                );
            }

            $category->update($data);
            
            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();
            
            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete category with associated products.');
            }

            // Check if category has children
            if ($category->children()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete category with subcategories.');
            }

            // Delete image
            $this->imageOptimizer->delete($category->image);

            $category->delete();
            
            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
    public function moveUp(Category $category)
    {
        try {
            DB::beginTransaction();
            
            // Get the previous category with lower sort order
            $previousCategory = Category::where('parent_id', $category->parent_id)
                ->where('sort_order', '<', $category->sort_order)
                ->orderBy('sort_order', 'desc')
                ->first();
            
            if ($previousCategory) {
                // Swap sort orders
                $tempOrder = $category->sort_order;
                $category->sort_order = $previousCategory->sort_order;
                $previousCategory->sort_order = $tempOrder;
                
                $category->save();
                $previousCategory->save();
            }
            
            DB::commit();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Category moved up successfully'
                ]);
            }
            
            return redirect()
                ->back()
                ->with('success', 'Category moved up successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to move category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Failed to move category: ' . $e->getMessage());
        }
    }
    
    public function moveDown(Category $category)
    {
        try {
            DB::beginTransaction();
            
            // Get the next category with higher sort order
            $nextCategory = Category::where('parent_id', $category->parent_id)
                ->where('sort_order', '>', $category->sort_order)
                ->orderBy('sort_order', 'asc')
                ->first();
            
            if ($nextCategory) {
                // Swap sort orders
                $tempOrder = $category->sort_order;
                $category->sort_order = $nextCategory->sort_order;
                $nextCategory->sort_order = $tempOrder;
                
                $category->save();
                $nextCategory->save();
            }
            
            DB::commit();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Category moved down successfully'
                ]);
            }
            
            return redirect()
                ->back()
                ->with('success', 'Category moved down successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to move category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Failed to move category: ' . $e->getMessage());
        }
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:categories,id',
            'orders.*.sort_order' => 'required|integer|min:0'
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($request->orders as $order) {
                Category::where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Sort order updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update sort order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Category $category)
    {
        try {
            $category->update(['status' => !$category->status]);

            return response()->json([
                'success' => true,
                'status' => $category->status,
                'message' => 'Status updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSelect()
    {
        try {
            $categories = Category::orderBy('name')
                ->get(['id', 'name', 'parent_id']);
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getImage(Category $category)
    {
        if (!$category->image) {
            return response()->json([
                'success' => false,
                'message' => 'No image found'
            ], 404);
        }
        
        $url = $this->imageOptimizer->getUrl($category->image);
        $dimensions = $this->imageOptimizer->getDimensions($category->image);
        
        return response()->json([
            'success' => true,
            'data' => [
                'url' => $url,
                'dimensions' => $dimensions,
                'path' => $category->image
            ]
        ]);
    }
    
    public function optimizeExistingImages()
    {
        try {
            DB::beginTransaction();
            
            $categories = Category::whereNotNull('image')->get();
            $optimized = 0;
            
            foreach ($categories as $category) {
                $oldPath = $category->image;
                $fullPath = storage_path('app/public/' . $oldPath);
                
                if (file_exists($fullPath) && !str_contains($oldPath, '.webp')) {
                    // Create temporary UploadedFile
                    $file = new \Illuminate\Http\UploadedFile(
                        $fullPath,
                        basename($fullPath),
                        mime_content_type($fullPath),
                        null,
                        true
                    );
                    
                    // Upload with optimization
                    $newPath = $this->imageOptimizer->upload(
                        $file,
                        'categories',
                        $oldPath
                    );
                    
                    $category->update(['image' => $newPath]);
                    $optimized++;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "{$optimized} images optimized successfully"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize images: ' . $e->getMessage()
            ], 500);
        }
    }
}