<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $variants = $product->variants()
        ->with('product')  
        ->orderBy('id')
        ->paginate(15);
        
        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        // Get category slug
        $categorySlug = $product->category->slug ?? 'general';
        
        // Get attributes based on category
        $attributes = Attribute::getForCategory($categorySlug);
        
        // If no category-specific attributes, get all common ones
        if ($attributes->isEmpty()) {
            $attributes = Attribute::with('values')
                ->whereIn('slug', ['color', 'size', 'material', 'connectivity', 'warranty_period'])
                ->orderBy('sort_order')
                ->get();
        }
        
        return view('admin.products.variants.create', compact('product', 'attributes'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable',
            'price_adjustment' => 'required|numeric',
            'buying_price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:product_variants,sku',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Get attributes array from request - FIXED
            $attributes = $request->input('attributes', []);
            
            // Generate SKU if not provided
            $sku = $request->sku;
            if (empty($sku)) {
                $sku = $this->generateSku($product, $attributes);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('variants', 'public');
            }

            // Create variant
            $variant = $product->variants()->create([
                'sku' => $sku,
                'attributes' => $attributes,
                'price_adjustment' => $request->price_adjustment,
                'buying_price' => $request->buying_price,
                'stock' => $request->stock,
                'image' => $imagePath,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.products.variants', $product)
                ->with('success', 'Variant created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create variant. ' . $e->getMessage());
        }
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        // Get all attributes that can be used for variants
        $attributes = Attribute::with('values')
            ->whereIn('slug', ['color', 'storage', 'ram', 'size'])
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.products.variants.edit', compact('product', 'variant', 'attributes'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        // SKU validation - ignore current variant's SKU
        $request->validate([
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable',
            'price_adjustment' => 'required|numeric',
            'buying_price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                Rule::unique('product_variants')->ignore($variant->id),
            ],
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            // Handle image upload or removal
            $imagePath = $variant->image;
            
            // Check if we need to remove the image
            if ($request->has('remove_image') && $request->remove_image == 1) {
                // Delete old image if exists
                if ($variant->image) {
                    \Storage::disk('public')->delete($variant->image);
                }
                $imagePath = null;
            }
            
            // Handle new image upload (this overrides removal if both are present)
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($variant->image) {
                    \Storage::disk('public')->delete($variant->image);
                }
                $imagePath = $request->file('image')->store('variants', 'public');
            }

            $attributes = $request->input('attributes', []);
            
            // Prepare update data - only update fields that are provided
            $updateData = [
                'price_adjustment' => $request->price_adjustment,
                'buying_price' => $request->buying_price,
                'stock' => $request->stock,
                'image' => $imagePath,
                'status' => $request->status,
            ];

            // Only update SKU if provided and different
            if ($request->filled('sku') && $request->sku !== $variant->sku) {
                $updateData['sku'] = $request->sku;
            }

            // Only update attributes if provided and not empty
            if (!empty($attributes) && is_array($attributes)) {
                // Filter out empty values
                $filteredAttributes = array_filter($attributes, function($value) {
                    return !is_null($value) && $value !== '';
                });
                
                if (!empty($filteredAttributes)) {
                    $updateData['attributes'] = $filteredAttributes;
                }
            }

            // Update variant
            $variant->update($updateData);

            DB::commit();

            return redirect()
                ->route('admin.products.variants', $product)
                ->with('success', 'Variant updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update variant. ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        try {
            // Delete image if exists
            if ($variant->image) {
                \Storage::disk('public')->delete($variant->image);
            }
            
            $variant->delete();

            return redirect()
                ->route('admin.products.variants', $product)
                ->with('success', 'Variant deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete variant. ' . $e->getMessage());
        }
    }

    public function generate(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array',
            'prices' => 'required|array',
            'stocks' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $combinations = $this->generateCombinations($request->input('attributes', []));
            
            foreach ($combinations as $index => $combination) {
                $sku = $this->generateSku($product, $combination);
                
                // Check if combination already exists
                $exists = $product->variants()
                    ->where('attributes', json_encode($combination))
                    ->exists();
                
                if (!$exists) {
                    $product->variants()->create([
                        'sku' => $sku,
                        'attributes' => $combination,
                        'price_adjustment' => $request->prices[$index] ?? 0,
                        'stock' => $request->stocks[$index] ?? 0,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variants generated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate variants. ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVariant(Request $request, Product $product)
    {
        $attributes = $request->except('_token');
        
        $variant = $product->variants()
            ->where('attributes', json_encode($attributes))
            ->where('status', 'active')
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'stock' => $variant->stock,
                'in_stock' => $variant->in_stock,
                'image' => $variant->image ? asset('storage/' . $variant->image) : null,
                'display_name' => $variant->display_name,
            ]
        ]);
    }

    private function generateSku(Product $product, array $attributes)
    {
        $baseSku = $product->sku;
        $attributeString = '';
        
        foreach ($attributes as $key => $value) {
            if (!empty($value) && is_string($value)) {
                $attributeString .= '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $value), 0, 3));
            }
        }
        
        $sku = $baseSku . $attributeString;
        
        // Make sure SKU is unique
        $count = 1;
        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $baseSku . $attributeString . '-' . $count;
            $count++;
        }
        
        return $sku;
    }

    private function generateCombinations($arrays)
    {
        if (!is_array($arrays)) {
            return [[]];
        }
        
        $result = [[]];
        
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        
        return $result;
    }

    public function bulkCreate(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array',
            'base_price_adjustment' => 'nullable|numeric',
        ]);

        $combinations = $this->generateCombinations($request->input('attributes', []));
        $created = 0;
        $skipped = 0;

        foreach ($combinations as $combination) {
            // Check if combination already exists
            $exists = $product->variants()
                ->where('attributes', json_encode($combination))
                ->exists();
            
            if (!$exists) {
                $product->variants()->create([
                    'sku' => $this->generateSku($product, $combination),
                    'attributes' => $combination,
                    'price_adjustment' => $request->base_price_adjustment ?? 0,
                    'stock' => 0,
                    'status' => 'active',
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        return redirect()
            ->route('admin.products.variants', $product)
            ->with('success', "{$created} variants created. {$skipped} skipped (already exist).");
    }
}