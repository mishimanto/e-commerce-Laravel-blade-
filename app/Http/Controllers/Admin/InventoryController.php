<?php
// app/Http/Controllers/Admin/InventoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Calculate stock status for a product
     */
    private function calculateStockStatus($product)
    {
        $hasVariants = $product->variants->count() > 0;
        
        if ($hasVariants) {
            $totalStock = 0;
            $inStockCount = 0;
            $lowStockCount = 0;
            $outOfStockCount = 0;
            
            foreach ($product->variants as $variant) {
                $stock = $variant->stock ?? 0;
                $totalStock += $stock;
                
                if ($stock <= 0) {
                    $outOfStockCount++;
                } elseif ($stock <= 5) {
                    $lowStockCount++;
                } else {
                    $inStockCount++;
                }
            }
            
            if ($totalStock <= 0) {
                return [
                    'status' => 'out_of_stock',
                    'quantity' => 0,
                    'has_variants' => true,
                    'variant_count' => $product->variants->count(),
                    'tooltip' => 'All variants are out of stock'
                ];
            } elseif ($totalStock <= 5) {
                return [
                    'status' => 'low_stock',
                    'quantity' => $totalStock,
                    'has_variants' => true,
                    'variant_count' => $product->variants->count(),
                    'tooltip' => 'Total stock is very low'
                ];
            } elseif ($outOfStockCount > 0 || $lowStockCount > 0) {
                return [
                    'status' => 'limited_stock',
                    'quantity' => $totalStock,
                    'has_variants' => true,
                    'variant_count' => $product->variants->count(),
                    'tooltip' => "Some variants have limited stock (In Stock: {$inStockCount}, Low Stock: {$lowStockCount}, Out of Stock: {$outOfStockCount})"
                ];
            } else {
                return [
                    'status' => 'in_stock',
                    'quantity' => $totalStock,
                    'has_variants' => true,
                    'variant_count' => $product->variants->count(),
                    'tooltip' => 'All variants are in stock'
                ];
            }
        } else {
            $stock = $product->stock ?? 0;
            
            if ($stock <= 0) {
                return [
                    'status' => 'out_of_stock',
                    'quantity' => 0,
                    'has_variants' => false,
                    'variant_count' => 0,
                    'tooltip' => 'Product is out of stock'
                ];
            } elseif ($stock <= 5) {
                return [
                    'status' => 'low_stock',
                    'quantity' => $stock,
                    'has_variants' => false,
                    'variant_count' => 0,
                    'tooltip' => 'Stock is running low'
                ];
            } else {
                return [
                    'status' => 'in_stock',
                    'quantity' => $stock,
                    'has_variants' => false,
                    'variant_count' => 0,
                    'tooltip' => 'Product is in stock'
                ];
            }
        }
    }

    /**
     * Display inventory listing
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->withCount('variants');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Get products first (without stock status filter)
        $products = $query->orderBy('name')->get();
        
        // Calculate stock status for each product
        foreach ($products as $product) {
            $product->stock_status = $this->calculateStockStatus($product);
        }

        // Filter by stock status (after calculation)
        if ($request->filled('stock_status')) {
            $filteredProducts = $products->filter(function($product) use ($request) {
                return $product->stock_status['status'] == $request->stock_status;
            });
            
            // Paginate manually
            $perPage = 15;
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $filteredProducts->slice($offset, $perPage)->values(),
                $filteredProducts->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            $products = $paginator;
        } else {
            // Regular pagination
            $products = $query->orderBy('name')->paginate(15);
            
            // Re-calculate stock status for paginated products
            foreach ($products as $product) {
                $product->stock_status = $this->calculateStockStatus($product);
            }
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.inventory.index', compact('products', 'categories'));
    }

    /**
     * Show product details with variants
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'variants']);
        
        // Calculate stock status
        $stockStatus = $this->calculateStockStatus($product);
        
        return view('admin.inventory.show', compact('product', 'stockStatus'));
    }

    /**
     * Update variant stock
     */
    public function updateStock(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $variant->update([
            'stock' => $request->stock
        ]);

        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    /**
     * Export inventory data
     */
    public function export(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        // Generate CSV
        $filename = 'inventory-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w');
        
        // Add headers
        fputcsv($handle, [
            'Product ID',
            'Product Name',
            'SKU',
            'Category',
            'Base Price',
            'Total Stock',
            'Variants Count',
            'Status',
            'Stock Status'
        ]);

        // Add data
        foreach ($products as $product) {
            $stockStatus = $this->calculateStockStatus($product);
            
            fputcsv($handle, [
                $product->id,
                $product->name,
                $product->sku,
                $product->category->name ?? 'N/A',
                $product->base_price,
                $stockStatus['quantity'],
                $product->variants->count(),
                $product->status,
                $stockStatus['status']
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get low stock products (for dashboard)
     */
    public function getLowStock()
    {
        $products = Product::with(['category', 'variants'])->get();
        
        $lowStockProducts = $products->filter(function($product) {
            $stockStatus = $this->calculateStockStatus($product);
            return in_array($stockStatus['status'], ['low_stock', 'limited_stock']);
        })->take(10);

        return response()->json($lowStockProducts->values());
    }
}