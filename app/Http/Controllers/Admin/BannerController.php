<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageOptimizerService;
use App\Http\Requests\Admin\BannerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    protected $imageOptimizer;

    public function __construct(ImageOptimizerService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
        
        // Configure image optimization for banners
        $this->imageOptimizer->setConfig([
            'max_width' => 1920,        // Desktop banner
            'max_height' => 1080,
            'quality' => 85,
            'format' => 'webp',
            'strip_exif' => true,
        ]);
        
        // Middleware for permissions
        // $this->middleware('permission:view-banners')->only(['index', 'show']);
        // $this->middleware('permission:create-banners')->only(['create', 'store']);
        // $this->middleware('permission:edit-banners')->only(['edit', 'update']);
        // $this->middleware('permission:delete-banners')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            }
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('subtitle', 'like', '%' . $request->search . '%');
            });
        }

        $banners = $query->orderBy('priority', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        $positions = Banner::POSITIONS;
        $stats = [
            'total' => Banner::count(),
            'active' => Banner::where('is_active', true)->count(),
            'inactive' => Banner::where('is_active', false)->count(),
            'expired' => Banner::where('end_date', '<', now())->count(),
        ];
        
        return view('admin.banners.index', compact('banners', 'positions', 'stats'));
    }

    public function create()
    {
        $positions = Banner::POSITIONS;
        $types = Banner::TYPES;
        $targets = Banner::TARGETS;
        
        return view('admin.banners.create', compact('positions', 'types', 'targets'));
    }

    public function store(BannerRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();

            // Handle image upload with optimization
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageOptimizer->upload(
                    $request->file('image'), 
                    'banners'
                );
            }

            // Handle mobile image upload with optimization
            if ($request->hasFile('mobile_image')) {
                $data['mobile_image'] = $this->imageOptimizer->upload(
                    $request->file('mobile_image'), 
                    'banners/mobile'
                );
            }

            $banner = Banner::create($data);
            
            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Banner creation failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create banner. ' . $e->getMessage());
        }
    }

    public function edit(Banner $banner)
    {
        $positions = Banner::POSITIONS;
        $types = Banner::TYPES;
        $targets = Banner::TARGETS;
        
        return view('admin.banners.edit', compact('banner', 'positions', 'types', 'targets'));
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();

            // Handle image upload with optimization
            if ($request->hasFile('image')) {
                // Delete old image using optimizer
                if ($banner->image) {
                    $this->imageOptimizer->delete($banner->image);
                }
                
                $data['image'] = $this->imageOptimizer->upload(
                    $request->file('image'), 
                    'banners'
                );
            }

            // Handle mobile image upload with optimization
            if ($request->hasFile('mobile_image')) {
                // Delete old mobile image using optimizer
                if ($banner->mobile_image) {
                    $this->imageOptimizer->delete($banner->mobile_image);
                }
                
                $data['mobile_image'] = $this->imageOptimizer->upload(
                    $request->file('mobile_image'), 
                    'banners/mobile'
                );
            }

            $banner->update($data);
            
            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Banner update failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update banner. ' . $e->getMessage());
        }
    }

    public function destroy(Banner $banner)
    {
        try {
            DB::beginTransaction();
            
            // Delete images using optimizer
            if ($banner->image) {
                $this->imageOptimizer->delete($banner->image);
            }
            if ($banner->mobile_image) {
                $this->imageOptimizer->delete($banner->mobile_image);
            }

            $banner->delete();
            
            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Banner deletion failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete banner. ' . $e->getMessage());
        }
    }

    public function toggleStatus(Banner $banner)
    {
        try {
            $banner->update(['is_active' => !$banner->is_active]);

            return response()->json([
                'success' => true,
                'status' => $banner->is_active,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Status toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePriority(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.priority' => 'required|integer|min:0|max:999'
        ]);

        try {
            foreach ($request->banners as $item) {
                Banner::where('id', $item['id'])->update(['priority' => $item['priority']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Priority update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority. ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id'
        ]);

        try {
            DB::beginTransaction();
            
            $deletedCount = 0;
            
            foreach ($request->ids as $id) {
                $banner = Banner::find($id);
                if ($banner) {
                    // Delete images
                    if ($banner->image) {
                        $this->imageOptimizer->delete($banner->image);
                    }
                    if ($banner->mobile_image) {
                        $this->imageOptimizer->delete($banner->mobile_image);
                    }
                    
                    $banner->delete();
                    $deletedCount++;
                }
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} banners deleted successfully."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete banners. ' . $e->getMessage()
            ], 500);
        }
    }
}