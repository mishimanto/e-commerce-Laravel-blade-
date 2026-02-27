<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\ImageOptimizerService;
use App\Http\Requests\Admin\BrandRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    protected $imageOptimizer;
    
    public function __construct(ImageOptimizerService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
        
        // Configure image optimization for brands
        $this->imageOptimizer->setConfig([
            'max_width' => 300,        
            'max_height' => 300,       
            'quality' => 80,            
            'format' => 'webp',         
        ]);
        
        // Middleware for permissions
        // $this->middleware('permission:view-brands')->only(['index', 'show', 'getSelect']);
        // $this->middleware('permission:create-brands')->only(['create', 'store']);
        // $this->middleware('permission:edit-brands')->only(['edit', 'update']);
        // $this->middleware('permission:delete-brands')->only(['destroy', 'bulkDelete']);
        // $this->middleware('permission:manage-brands')->only(['toggleStatus']);
    }

    public function index(Request $request)
    {
        $brands = Brand::withCount('products')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(BrandRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();

            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = str()->slug($data['name']);
            }

            // Handle logo upload with optimization
            if ($request->hasFile('logo')) {
                $data['logo'] = $this->imageOptimizer->upload(
                    $request->file('logo'), 
                    'brands'
                );
            }

            $brand = Brand::create($data);
            
            DB::commit();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Brand created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Brand creation failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create brand: ' . $e->getMessage());
        }
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            DB::beginTransaction();
            
            // Get validated data
            $data = $request->validated();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = str()->slug($data['name']);
            }
            
            // Handle image removal (explicit removal button)
            if ($request->has('remove_logo') && $request->remove_logo == '1') {
                if ($brand->logo) {
                    // Delete file from storage using optimizer
                    $this->imageOptimizer->delete($brand->logo);
                    
                    // Set logo to null in database
                    $data['logo'] = null;
                }
            }
            // Handle new logo upload (this will replace existing logo)
            elseif ($request->hasFile('logo')) {
                // Upload new logo with optimization (old will be auto-deleted)
                $data['logo'] = $this->imageOptimizer->upload(
                    $request->file('logo'), 
                    'brands',
                    $brand->logo // Pass old path for deletion
                );
            }
            // If no logo change, keep existing logo
            else {
                // Keep existing logo (don't modify)
                unset($data['logo']);
            }
            
            // Update brand in database
            $brand->update($data);
            
            DB::commit();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Brand updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Brand update failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            DB::beginTransaction();
            
            // Check if brand has products
            if ($brand->products()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete brand with associated products.');
            }

            // Delete logo using optimizer
            $this->imageOptimizer->delete($brand->logo);

            // Soft delete
            $brand->delete();
            
            DB::commit();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Brand deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Brand deletion failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete brand: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Brand $brand)
    {
        try {
            $brand->update(['status' => !$brand->status]);

            return response()->json([
                'success' => true,
                'status' => $brand->status,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Status toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSelect()
    {
        try {
            $brands = Brand::orderBy('name')
                ->get(['id', 'name', 'logo']);

            // Add logo URL to each brand
            $brands->each(function ($brand) {
                $brand->logo_url = $this->imageOptimizer->getUrl($brand->logo);
            });

            return response()->json([
                'success' => true,
                'data' => $brands
            ]);

        } catch (\Exception $e) {
            Log::error('Get brands failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load brands: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:brands,id'
        ]);

        try {
            DB::beginTransaction();
            
            $deletedCount = 0;
            $skippedCount = 0;
            $failedBrands = [];

            foreach ($request->ids as $id) {
                $brand = Brand::find($id);
                
                // Skip brands with products
                if ($brand->products()->count() > 0) {
                    $skippedCount++;
                    $failedBrands[] = $brand->name;
                    continue;
                }

                // Delete logo file using optimizer
                $this->imageOptimizer->delete($brand->logo);

                // Permanent delete (not soft delete)
                $brand->forceDelete();
                $deletedCount++;
            }

            DB::commit();

            $message = "{$deletedCount} brands deleted successfully.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} brands were skipped because they have associated products: " . implode(', ', $failedBrands);
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
                'message' => 'Failed to delete brands: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getLogo(Brand $brand)
    {
        try {
            if (!$brand->logo) {
                return response()->json([
                    'success' => false,
                    'message' => 'No logo found'
                ], 404);
            }

            $url = $this->imageOptimizer->getUrl($brand->logo);
            $dimensions = $this->imageOptimizer->getDimensions($brand->logo);

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'dimensions' => $dimensions,
                    'path' => $brand->logo
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get logo failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load logo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function optimizeExistingLogos()
    {
        try {
            DB::beginTransaction();
            
            $brands = Brand::whereNotNull('logo')->get();
            $optimized = 0;
            $failed = 0;

            foreach ($brands as $brand) {
                try {
                    $oldPath = $brand->logo;
                    $fullPath = storage_path('app/public/' . $oldPath);
                    
                    if (!file_exists($fullPath)) {
                        continue;
                    }
                    
                    // Skip if already WebP
                    if (str_ends_with($oldPath, '.webp')) {
                        continue;
                    }
                    
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
                        'brands',
                        $oldPath
                    );
                    
                    $brand->update(['logo' => $newPath]);
                    $optimized++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to optimize brand {$brand->id} logo: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$optimized} logos optimized successfully. {$failed} failed.",
                'optimized' => $optimized,
                'failed' => $failed
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk optimization failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize logos: ' . $e->getMessage()
            ], 500);
        }
    }
}