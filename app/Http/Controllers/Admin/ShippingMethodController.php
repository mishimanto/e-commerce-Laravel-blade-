<?php
// app/Http/Controllers/Admin/ShippingMethodController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $shippingMethods = ShippingMethod::orderBy('sort_order')->get();
        return view('admin.shipping.index', compact('shippingMethods'));
    }

    public function create()
    {
        return view('admin.shipping.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:shipping_methods|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|max:100',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_order_amount' => 'nullable|numeric|min:0|gt:minimum_order_amount',
            'is_free_shipping' => 'boolean',
            'free_shipping_threshold' => 'nullable|required_if:is_free_shipping,1|numeric|min:0',
            'available_countries' => 'nullable|array',
            'available_cities' => 'nullable|array',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_free_shipping'] = $request->has('is_free_shipping');

        ShippingMethod::create($validated);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping method created successfully');
    }

    public function edit(ShippingMethod $shippingMethod)
    {
        return view('admin.shipping.edit', compact('shippingMethod'));
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'code' => 'required|unique:shipping_methods,code,' . $shippingMethod->id,
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|max:100',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_order_amount' => 'nullable|numeric|min:0|gt:minimum_order_amount',
            'is_free_shipping' => 'boolean',
            'free_shipping_threshold' => 'nullable|required_if:is_free_shipping,1|numeric|min:0',
            'available_countries' => 'nullable|array',
            'available_cities' => 'nullable|array',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_free_shipping'] = $request->has('is_free_shipping');

        $shippingMethod->update($validated);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping method updated successfully');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();
        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping method deleted successfully');
    }

    public function toggleStatus(ShippingMethod $shippingMethod)
    {
        $shippingMethod->update(['is_active' => !$shippingMethod->is_active]);
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $index => $id) {
            ShippingMethod::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}