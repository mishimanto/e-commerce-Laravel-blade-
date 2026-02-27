<?php
// app/Http/Controllers/Admin/PaymentMethodController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
        return view('admin.payment.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:payment_methods|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'instructions' => 'nullable|array',
            'type' => 'required|in:online,offline,cash',
            'fixed_fee' => 'numeric|min:0',
            'percentage_fee' => 'numeric|min:0|max:100',
            'minimum_fee' => 'nullable|numeric|min:0',
            'maximum_fee' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_order_amount' => 'nullable|numeric|min:0|gt:minimum_order_amount',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('images/payments', 'public');
            $validated['icon'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['instructions'] = $request->instructions ?? [];
        $validated['config'] = $request->config ?? [];

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment.index')
            ->with('success', 'Payment method created successfully');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'code' => 'required|unique:payment_methods,code,' . $paymentMethod->id,
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'instructions' => 'nullable|array',
            'type' => 'required|in:online,offline,cash',
            'fixed_fee' => 'numeric|min:0',
            'percentage_fee' => 'numeric|min:0|max:100',
            'minimum_fee' => 'nullable|numeric|min:0',
            'maximum_fee' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_order_amount' => 'nullable|numeric|min:0|gt:minimum_order_amount',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($paymentMethod->icon) {
                Storage::disk('public')->delete($paymentMethod->icon);
            }
            $path = $request->file('icon')->store('images/payments', 'public');
            $validated['icon'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['instructions'] = $request->instructions ?? [];
        $validated['config'] = $request->config ?? [];

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment.index')
            ->with('success', 'Payment method updated successfully');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Delete icon
        if ($paymentMethod->icon) {
            Storage::disk('public')->delete($paymentMethod->icon);
        }
        
        $paymentMethod->delete();
        return redirect()->route('admin.payment.index')
            ->with('success', 'Payment method deleted successfully');
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $index => $id) {
            PaymentMethod::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}