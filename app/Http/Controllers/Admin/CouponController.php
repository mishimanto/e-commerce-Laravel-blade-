<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\Admin\CouponRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:manage-coupons');
    // }

    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            switch($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->latest()->paginate(15);
        
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(100)->get();
        
        return view('admin.coupons.create', compact('categories', 'products'));
    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        
        // Generate unique coupon code if not provided
        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::random(8));
        }

        $coupon = Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(100)->get();
        
        return view('admin.coupons.edit', compact('coupon', 'categories', 'products'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json([
            'success' => true,
            'status' => $coupon->is_active
        ]);
    }

    public function duplicate(Coupon $coupon)
    {
        $newCoupon = $coupon->replicate();
        $newCoupon->code = $coupon->code . '-COPY-' . Str::random(4);
        $newCoupon->total_used = 0;
        $newCoupon->created_at = now();
        $newCoupon->save();

        return redirect()->route('admin.coupons.edit', $newCoupon)
            ->with('success', 'Coupon duplicated successfully.');
    }
}