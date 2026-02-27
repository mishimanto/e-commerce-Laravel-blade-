<?php

namespace App\Http\Controllers;

use App\Services\Compare\CompareService;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    protected $compareService;

    public function __construct(CompareService $compareService)
    {
        $this->compareService = $compareService;
    }

    /**
     * Display compare page
     */
    public function index()
    {
        $comparisonData = $this->compareService->getComparisonData();
        
        return view('storefront.compare', $comparisonData);
    }

    /**
     * Add product to compare
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        try {
            $compare = $this->compareService->addProduct($request->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Product added to comparison.',
                'compare_count' => $this->compareService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove product from compare
     */
    public function remove($productId)
    {
        try {
            $removed = $this->compareService->removeProduct($productId);

            if (!$removed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in comparison list.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product removed from comparison.',
                'compare_count' => $this->compareService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear comparison list
     */
    public function clear()
    {
        try {
            $this->compareService->clearCompare();

            return response()->json([
                'success' => true,
                'message' => 'Comparison list cleared.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if product is in compare (AJAX)
     */
    public function check($productId)
    {
        $inCompare = $this->compareService->isInCompare($productId);

        return response()->json([
            'success' => true,
            'in_compare' => $inCompare
        ]);
    }

    /**
     * Get comparison table (AJAX)
     */
    public function getTable()
    {
        $comparisonData = $this->compareService->getComparisonData();

        return response()->json([
            'success' => true,
            'html' => view('storefront.compare-table', $comparisonData)->render()
        ]);
    }
}