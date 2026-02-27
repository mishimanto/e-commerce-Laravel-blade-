<?php
// app/Http/Controllers/Admin/AttributeValueController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the attribute values.
     */
    public function index(Attribute $attribute)
    {
        $values = $attribute->values()->orderBy('sort_order')->get();
        return view('admin.attributes.values.index', compact('attribute', 'values'));
    }

    /**
     * Show the form for creating a new attribute value.
     */
    public function create(Attribute $attribute)
    {
        return view('admin.attributes.values.create', compact('attribute'));
    }

    /**
     * Store a newly created attribute value in storage.
     */
    public function store(Request $request, Attribute $attribute)
    {
        $rules = [
            'value' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
        ];

        // Add color code validation for color type
        if ($attribute->type === 'color') {
            $rules['color_code'] = 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/';
        }

        $validated = $request->validate($rules);

        $validated['attribute_id'] = $attribute->id;
        $validated['slug'] = Str::slug($request->value);
        $validated['sort_order'] = $request->input('sort_order', 0);

        AttributeValue::create($validated);

        return redirect()->route('admin.attributes.values.index', $attribute)
            ->with('success', 'Attribute value created successfully.');
    }

    /**
     * Show the form for editing the specified attribute value.
     */
    public function edit(Attribute $attribute, AttributeValue $value)
    {
        return view('admin.attributes.values.edit', compact('attribute', 'value'));
    }

    /**
     * Update the specified attribute value in storage.
     */
    public function update(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $rules = [
            'value' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
        ];

        // Add color code validation for color type
        if ($attribute->type === 'color') {
            $rules['color_code'] = 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/';
        }

        $validated = $request->validate($rules);

        $validated['slug'] = Str::slug($request->value);
        $validated['sort_order'] = $request->input('sort_order', 0);

        $value->update($validated);

        return redirect()->route('admin.attributes.values.index', $attribute)
            ->with('success', 'Attribute value updated successfully.');
    }

    /**
     * Remove the specified attribute value from storage.
     */
    public function destroy(Attribute $attribute, AttributeValue $value)
    {
        // Check if value is used in products
        if ($value->products()->count() > 0) {
            return redirect()->route('admin.attributes.values.index', $attribute)
                ->with('error', 'Cannot delete attribute value that is used in products.');
        }

        $value->delete();

        return redirect()->route('admin.attributes.values.index', $attribute)
            ->with('success', 'Attribute value deleted successfully.');
    }

    /**
     * Update the sort order of attribute values.
     */
    public function updateOrder(Request $request, Attribute $attribute)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|min:0'
        ]);

        foreach ($request->orders as $id => $order) {
            AttributeValue::where('id', $id)
                ->where('attribute_id', $attribute->id)
                ->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}