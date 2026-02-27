<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        $query = Attribute::withCount('values')->orderBy('sort_order');
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by filterable
        if ($request->filled('filterable')) {
            $query->where('is_filterable', $request->filterable === 'yes');
        }
        
        $attributes = $query->paginate(15);
        
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        $types = Attribute::TYPES;
        return view('admin.attributes.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Attribute::TYPES)),
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_required'] = $request->boolean('is_required');
        $validated['is_filterable'] = $request->boolean('is_filterable');
        $validated['sort_order'] = $request->input('sort_order', 0);

        Attribute::create($validated);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully.');
    }

    public function edit(Attribute $attribute)
    {
        $types = Attribute::TYPES;
        return view('admin.attributes.edit', compact('attribute', 'types'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Attribute::TYPES)),
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_required'] = $request->boolean('is_required');
        $validated['is_filterable'] = $request->boolean('is_filterable');
        $validated['sort_order'] = $request->input('sort_order', 0);

        $attribute->update($validated);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        // Check if attribute has values
        if ($attribute->values()->count() > 0) {
            return redirect()->route('admin.attributes.index')
                ->with('error', 'Cannot delete attribute with existing values. Delete the values first.');
        }

        $attribute->delete();

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|min:0'
        ]);

        foreach ($request->orders as $id => $order) {
            Attribute::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}