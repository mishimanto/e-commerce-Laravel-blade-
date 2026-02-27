<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\CustomerReportExport;

class ReportController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view-reports');
    // }

    /**
     * Sales report with profit analysis
     */
    public function sales(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date', Carbon::now()->subMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        // Parse dates if they're strings
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        // Sales Overview
        $salesOverview = $this->getSalesOverview($startDate, $endDate);
        
        // Profit Overview
        $profitOverview = $this->getProfitOverview($startDate, $endDate);
        
        // Product Profit
        $productProfit = $this->getProductProfit($startDate, $endDate);
        
        // Variant Profit
        $variantProfit = $this->getVariantProfit($startDate, $endDate);
        
        // Category Profit
        $categoryProfit = $this->getCategoryProfit($startDate, $endDate);
        
        // Sales Chart Data
        $salesChartData = $this->getSalesChartData($period, $startDate, $endDate);
        
        // Top Products
        $topProducts = $this->getTopProducts($startDate, $endDate);
        
        // Payment Methods
        $paymentMethods = $this->getPaymentMethodStats($startDate, $endDate);
        
        // Order Status Distribution
        $orderStatuses = $this->getOrderStatusStats($startDate, $endDate);
        
        // Daily Sales
        $dailySales = $this->getDailySales($startDate, $endDate);
        
        // Category Performance
        $categoryPerformance = $this->getCategoryPerformance($startDate, $endDate);
        
        return view('admin.reports.sales', compact(
            'salesOverview',
            'profitOverview',
            'productProfit',
            'variantProfit',
            'categoryProfit',
            'salesChartData',
            'topProducts',
            'paymentMethods',
            'orderStatuses',
            'dailySales',
            'categoryPerformance',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Inventory report with cost analysis
     */
    public function inventory(Request $request)
    {
        // Stock Summary
        $stockSummary = $this->getStockSummary();
        
        // Inventory Value with Cost
        $inventoryValueWithCost = $this->getInventoryValueWithCost();
        
        // Low Stock Products
        $threshold = $request->get('threshold', 5);
        $lowStockProducts = $this->getLowStockProducts($threshold);
        
        // Out of Stock Products
        $outOfStockProducts = $this->getOutOfStockProducts();
        
        // Best Selling Products
        $bestSellingProducts = $this->getBestSellingProducts();
        
        // Category Stock Distribution
        $categoryStock = $this->getCategoryStockDistribution();
        
        // Brand Stock Distribution
        $brandStock = $this->getBrandStockDistribution();
        
        // Stock Movement
        $stockMovement = $this->getStockMovement();
        
        // Value by Category
        $inventoryValue = $this->getInventoryValue();
        
        return view('admin.reports.inventory', compact(
            'stockSummary',
            'inventoryValueWithCost',
            'lowStockProducts',
            'outOfStockProducts',
            'bestSellingProducts',
            'categoryStock',
            'brandStock',
            'stockMovement',
            'inventoryValue',
            'threshold'
        ));
    }

    public function customers(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date', Carbon::now()->subMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        // Parse dates if they're strings
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        // Customer Overview
        $customerOverview = $this->getCustomerOverview($startDate, $endDate);
        
        // Customer Growth Chart
        $customerGrowth = $this->getCustomerGrowth($period, $startDate, $endDate);
        
        // Top Customers
        $topCustomers = $this->getTopCustomers($startDate, $endDate);
        
        // New vs Returning
        $newVsReturning = $this->getNewVsReturningCustomers($startDate, $endDate);
        
        // Customer Location
        $customerLocations = $this->getCustomerLocations();
        
        // Customer Acquisition
        $customerAcquisition = $this->getCustomerAcquisition();
        
        // Customer Lifetime Value
        $customerLTV = $this->getCustomerLTV();
        
        return view('admin.reports.customers', compact(
            'customerOverview',
            'customerGrowth',
            'topCustomers',
            'newVsReturning',
            'customerLocations',
            'customerAcquisition',
            'customerLTV',
            'period',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'excel');
        $startDate = $request->get('start_date', Carbon::now()->subMonth());
        $endDate = $request->get('end_date', Carbon::now());

        switch ($type) {
            case 'sales':
                $export = new SalesReportExport($startDate, $endDate);
                $filename = 'sales_report_' . now()->format('Y_m_d') . '.xlsx';
                break;
            case 'inventory':
                $export = new InventoryReportExport();
                $filename = 'inventory_report_' . now()->format('Y_m_d') . '.xlsx';
                break;
            case 'customers':
                $export = new CustomerReportExport($startDate, $endDate);
                $filename = 'customer_report_' . now()->format('Y_m_d') . '.xlsx';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }

        return Excel::download($export, $filename);
    }

    public function print(Request $request)
    {
        $type = $request->get('type', 'sales');
        
        return view('admin.reports.print.' . $type, [
            'data' => $this->getReportData($type, $request)
        ]);
    }

    /**
     * Get profit overview with buying price
     */
    private function getProfitOverview($startDate, $endDate)
    {
        $orders = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->with(['items.product', 'items.variant'])
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalDiscount = 0;
        $totalShipping = 0;
        $totalTax = 0;

        foreach ($orders as $order) {
            $totalRevenue += $order->subtotal;
            $totalDiscount += $order->discount_amount + $order->coupon_discount;
            $totalShipping += $order->shipping_cost;
            $totalTax += $order->tax_amount;

            foreach ($order->items as $item) {
                // Calculate cost based on variant or product buying price
                $buyingPrice = 0;
                
                if ($item->variant_id && $item->variant && $item->variant->buying_price) {
                    $buyingPrice = $item->variant->buying_price;
                } elseif ($item->product && $item->product->buying_price) {
                    $buyingPrice = $item->product->buying_price;
                }
                
                $totalCost += $buyingPrice * $item->quantity;
            }
        }

        $grossProfit = $totalRevenue - $totalCost;
        $netProfit = $grossProfit - $totalDiscount;

        return [
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'profit_margin' => $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0,
            'total_discount' => $totalDiscount,
            'total_shipping' => $totalShipping,
            'total_tax' => $totalTax,
        ];
    }

    /**
     * Get product-wise profit
     */
    private function getProductProfit($startDate, $endDate, $limit = 10)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(
                    order_items.quantity * 
                    COALESCE(product_variants.buying_price, products.buying_price, 0)
                ) as total_cost'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                $item->total_profit = $item->total_revenue - $item->total_cost;
                $item->profit_margin = $item->total_revenue > 0 
                    ? ($item->total_profit / $item->total_revenue) * 100 
                    : 0;
                return $item;
            });
    }

    /**
     * Get variant-wise profit
     */
    private function getVariantProfit($startDate, $endDate, $limit = 10)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotNull('order_items.variant_id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'product_variants.id as variant_id',
                'product_variants.sku as variant_sku',
                'product_variants.attributes as variant_attributes',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(order_items.quantity * product_variants.buying_price) as total_cost'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy(
                'products.id', 'products.name', 
                'product_variants.id', 'product_variants.sku', 'product_variants.attributes'
            )
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                $item->total_profit = $item->total_revenue - $item->total_cost;
                $item->profit_margin = $item->total_revenue > 0 
                    ? ($item->total_profit / $item->total_revenue) * 100 
                    : 0;
                return $item;
            });
    }

    /**
     * Get profit by category
     */
    private function getCategoryProfit($startDate, $endDate)
    {
        return DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(
                    order_items.quantity * 
                    COALESCE(product_variants.buying_price, products.buying_price, 0)
                ) as total_cost')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function($item) {
                $item->total_profit = $item->total_revenue - $item->total_cost;
                $item->profit_margin = $item->total_revenue > 0 
                    ? ($item->total_profit / $item->total_revenue) * 100 
                    : 0;
                return $item;
            });
    }

    /**
     * Get inventory value with cost
     */
    private function getInventoryValueWithCost()
    {
        $products = Product::with('variants')->get();
        
        $totalValue = 0;
        $totalCost = 0;
        $potentialProfit = 0;

        foreach ($products as $product) {
            if ($product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    if ($variant->stock > 0) {
                        if ($variant->buying_price) {
                            $totalCost += $variant->stock * $variant->buying_price;
                        }
                        $sellingPrice = ($product->sale_price ?? $product->base_price) + ($variant->price_adjustment ?? 0);
                        $totalValue += $variant->stock * $sellingPrice;
                    }
                }
            } else {
                if ($product->stock > 0) {
                    if ($product->buying_price) {
                        $totalCost += $product->stock * $product->buying_price;
                    }
                    $sellingPrice = $product->sale_price ?? $product->base_price;
                    $totalValue += $product->stock * $sellingPrice;
                }
            }
        }

        $potentialProfit = $totalValue - $totalCost;

        return [
            'total_value' => $totalValue,
            'total_cost' => $totalCost,
            'potential_profit' => $potentialProfit,
            'profit_margin' => $totalValue > 0 ? ($potentialProfit / $totalValue) * 100 : 0,
        ];
    }

    private function getSalesOverview($startDate, $endDate)
    {
        $currentPeriod = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled');
            
        $previousStart = Carbon::parse($startDate)->subDays(Carbon::parse($endDate)->diffInDays($startDate));
        $previousEnd = Carbon::parse($startDate);
        
        $previousPeriod = Order::whereBetween('orders.created_at', [$previousStart, $previousEnd])
            ->where('orders.status', '!=', 'cancelled');

        return [
            'total_sales' => $currentPeriod->sum('total'),
            'total_orders' => $currentPeriod->count(),
            'average_order_value' => $currentPeriod->avg('total'),
            'total_items' => $currentPeriod->join('order_items', 'orders.id', '=', 'order_items.order_id')->sum('order_items.quantity'),
            
            'previous_sales' => $previousPeriod->sum('total'),
            'previous_orders' => $previousPeriod->count(),
            'sales_growth' => $this->calculateGrowth(
                $previousPeriod->sum('total'),
                $currentPeriod->sum('total')
            ),
            'orders_growth' => $this->calculateGrowth(
                $previousPeriod->count(),
                $currentPeriod->count()
            ),
        ];
    }

    private function getSalesChartData($period, $startDate, $endDate)
{
    $query = Order::where('orders.status', '!=', 'cancelled')
        ->whereBetween('orders.created_at', [$startDate, $endDate]);

    switch ($period) {
        case 'daily':
            $groupBy = DB::raw('DATE(orders.created_at)');
            $select = DB::raw('DATE(orders.created_at) as date');
            $orderBy = 'date';
            break;
        case 'weekly':
            $groupBy = DB::raw('YEARWEEK(orders.created_at)');
            $select = DB::raw('YEARWEEK(orders.created_at) as week');
            $orderBy = 'week';
            break;
        case 'monthly':
            $groupBy = DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m")');
            $select = DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month');
            $orderBy = 'month';
            break;
        case 'yearly':
            $groupBy = DB::raw('YEAR(orders.created_at)');
            $select = DB::raw('YEAR(orders.created_at) as year');
            $orderBy = 'year';
            break;
        default:
            $groupBy = DB::raw('DATE(orders.created_at)');
            $select = DB::raw('DATE(orders.created_at) as date');
            $orderBy = 'date';
            break;
    }

    $sales = $query->select(
            $select,
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total) as total_sales')
        )
        ->groupBy($groupBy)  // আলিয়াস না, মূল এক্সপ্রেশন ব্যবহার করুন
        ->orderBy($orderBy, 'asc')
        ->get();

    $labels = [];
    $salesData = [];
    $ordersData = [];

    foreach ($sales as $item) {
        // প্রথম কলামের নাম বের করা
        $keys = array_keys($item->toArray());
        $dateKey = $keys[0];
        $labels[] = $item->$dateKey;
        $salesData[] = $item->total_sales;
        $ordersData[] = $item->total_orders;
    }

    return [
        'labels' => $labels,
        'sales' => $salesData,
        'orders' => $ordersData,
    ];
}
    private function getTopProducts($startDate, $endDate, $limit = 10)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'categories.name as category',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'categories.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getPaymentMethodStats($startDate, $endDate)
    {
        return Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total) as total_amount'))
            ->groupBy('payment_method')
            ->get();
    }

    private function getOrderStatusStats($startDate, $endDate)
    {
        return Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();
    }

    private function getDailySales($startDate, $endDate)
    {
        $days = [];
        $current = Carbon::parse($startDate);
        
        while ($current <= $endDate) {
            $days[$current->format('Y-m-d')] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->format('D'),
                'orders' => 0,
                'sales' => 0,
                'items' => 0
            ];
            $current->addDay();
        }

        $orders = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->with('items')
            ->get();

        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d');
            if (isset($days[$date])) {
                $days[$date]['orders']++;
                $days[$date]['sales'] += $order->total;
                $days[$date]['items'] += $order->items->sum('quantity');
            }
        }

        return array_values($days);
    }

    private function getCategoryPerformance($startDate, $endDate)
    {
        return DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.quantity) as total_items'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    private function getStockSummary()
    {
        return [
            'total_products' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'total_value' => Product::select(DB::raw('SUM(stock * base_price) as total'))->first()->total,
            'low_stock' => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'in_stock' => Product::where('stock', '>', 0)->count(),
            'average_price' => Product::avg('base_price'),
        ];
    }

    private function getLowStockProducts($threshold = 5)
    {
        return Product::with('category', 'brand', 'images')
            ->where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->get();
    }

    private function getOutOfStockProducts()
    {
        return Product::with('category', 'brand', 'images')
            ->where('stock', 0)
            ->orderBy('name')
            ->get();
    }

    private function getBestSellingProducts($limit = 10)
    {
        return DB::table('products')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.stock',
                'products.base_price',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            )
            ->where('orders.status', '!=', 'cancelled')
            ->orWhereNull('orders.id')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock', 'products.base_price')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getCategoryStockDistribution()
    {
        return Category::withCount('products')
            ->withSum('products', 'stock')
            ->having('products_count', '>', 0)
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'product_count' => $category->products_count,
                    'stock_count' => $category->products_sum_stock ?? 0,
                ];
            });
    }

    private function getBrandStockDistribution()
    {
        return Brand::withCount('products')
            ->withSum('products', 'stock')
            ->having('products_count', '>', 0)
            ->get()
            ->map(function($brand) {
                return [
                    'name' => $brand->name,
                    'product_count' => $brand->products_count,
                    'stock_count' => $brand->products_sum_stock ?? 0,
                ];
            });
    }

    /**
     * Get stock movement statistics - FIXED VERSION
     */
    private function getStockMovement()
    {
        return [
            'last_7_days' => Order::where('orders.created_at', '>=', now()->subDays(7))
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 'cancelled')
                ->sum('order_items.quantity'),
                
            'last_30_days' => Order::where('orders.created_at', '>=', now()->subDays(30))
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 'cancelled')
                ->sum('order_items.quantity'),
                
            'last_90_days' => Order::where('orders.created_at', '>=', now()->subDays(90))
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 'cancelled')
                ->sum('order_items.quantity'),
        ];
    }

    private function getInventoryValue()
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('COUNT(products.id) as product_count'),
                DB::raw('SUM(products.stock) as total_stock'),
                DB::raw('SUM(products.stock * products.base_price) as total_value')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_value', 'desc')
            ->get();
    }

    private function getCustomerOverview($startDate, $endDate)
    {
        $totalCustomers = User::role('customer')->count();
        
        $newCustomers = User::role('customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $activeCustomers = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');
            
        // FIXED: Get repeat customers using a subquery
        $repeatCustomers = DB::table('orders')
            ->select('user_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        return [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'active_customers' => $activeCustomers,
            'repeat_customers' => $repeatCustomers,
            'customer_acquisition_rate' => $totalCustomers > 0 ? ($newCustomers / $totalCustomers) * 100 : 0,
            'repeat_customer_rate' => $activeCustomers > 0 ? ($repeatCustomers / $activeCustomers) * 100 : 0,
        ];
    }

    private function getCustomerGrowth($period, $startDate, $endDate)
{
    $query = User::role('customer')
        ->whereBetween('created_at', [$startDate, $endDate]);

    switch ($period) {
        case 'daily':
            $groupBy = DB::raw('DATE(created_at)');
            $select = DB::raw('DATE(created_at) as date');
            $orderBy = 'date';
            break;
        case 'weekly':
            $groupBy = DB::raw('YEARWEEK(created_at)');
            $select = DB::raw('YEARWEEK(created_at) as week');
            $orderBy = 'week';
            break;
        case 'monthly':
            $groupBy = DB::raw('DATE_FORMAT(created_at, "%Y-%m")');
            $select = DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month');
            $orderBy = 'month';
            break;
        case 'yearly':
            $groupBy = DB::raw('YEAR(created_at)');
            $select = DB::raw('YEAR(created_at) as year');
            $orderBy = 'year';
            break;
        default:
            $groupBy = DB::raw('DATE_FORMAT(created_at, "%Y-%m")');
            $select = DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month');
            $orderBy = 'month';
            break;
    }

    return $query->select(
            $select,
            DB::raw('COUNT(*) as count')
        )
        ->groupBy($groupBy)  // আলিয়াস না, মূল এক্সপ্রেশন ব্যবহার করুন
        ->orderBy($orderBy, 'asc')
        ->get();
}

    private function getTopCustomers($startDate, $endDate, $limit = 10)
    {
        return User::role('customer')
            ->with(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate])
                      ->where('orders.status', '!=', 'cancelled')
                      ->orderBy('orders.created_at', 'desc');
            }])
            ->withCount(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate])
                      ->where('orders.status', '!=', 'cancelled');
            }])
            ->withSum(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate])
                      ->where('orders.status', '!=', 'cancelled');
            }], 'total')
            ->having('orders_count', '>', 0)
            ->orderBy('orders_sum_total', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getNewVsReturningCustomers($startDate, $endDate)
{
    $firstTimeCustomers = Order::whereBetween('orders.created_at', [$startDate, $endDate])
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                ->from('orders as o2')
                ->whereRaw('o2.user_id = orders.user_id')
                ->whereRaw('o2.created_at < orders.created_at');
        })
        ->distinct('user_id')
        ->count('user_id');

    // FIXED: Get returning customers using a subquery with DB::table
    $returningCustomers = DB::table('orders')
        ->select('user_id')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereExists(function($query) use ($startDate) {
            $query->select(DB::raw(1))
                ->from('orders as o2')
                ->whereRaw('o2.user_id = orders.user_id')
                ->whereRaw('o2.created_at < ?', [$startDate]);
        })
        ->groupBy('user_id')
        ->get()
        ->count();

    return [
        'first_time' => $firstTimeCustomers,
        'returning' => $returningCustomers,
        'first_time_percentage' => $firstTimeCustomers + $returningCustomers > 0 
            ? ($firstTimeCustomers / ($firstTimeCustomers + $returningCustomers)) * 100 
            : 0,
        'returning_percentage' => $firstTimeCustomers + $returningCustomers > 0 
            ? ($returningCustomers / ($firstTimeCustomers + $returningCustomers)) * 100 
            : 0,
    ];
}

    private function getCustomerLocations()
    {
        return User::role('customer')
            ->whereNotNull('city')
            ->select('city', DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getCustomerAcquisition()
    {
        $totalCustomers = User::role('customer')->count();
        
        return [
            'last_7_days' => User::role('customer')->where('created_at', '>=', now()->subDays(7))->count(),
            'last_30_days' => User::role('customer')->where('created_at', '>=', now()->subDays(30))->count(),
            'last_90_days' => User::role('customer')->where('created_at', '>=', now()->subDays(90))->count(),
            'this_year' => User::role('customer')->whereYear('created_at', now()->year)->count(),
            'previous_year' => User::role('customer')->whereYear('created_at', now()->subYear()->year)->count(),
        ];
    }

    private function getCustomerLTV()
    {
        $customers = User::role('customer')
            ->has('orders')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->get();

        $averageLTV = $customers->avg('orders_sum_total');
        $medianLTV = $customers->median('orders_sum_total');

        $byOrderCount = [
            '1' => $customers->where('orders_count', 1)->avg('orders_sum_total') ?? 0,
            '2-3' => $customers->whereBetween('orders_count', [2, 3])->avg('orders_sum_total') ?? 0,
            '4-5' => $customers->whereBetween('orders_count', [4, 5])->avg('orders_sum_total') ?? 0,
            '6+' => $customers->where('orders_count', '>=', 6)->avg('orders_sum_total') ?? 0,
        ];

        return [
            'average' => $averageLTV,
            'median' => $medianLTV,
            'min' => $customers->min('orders_sum_total') ?? 0,
            'max' => $customers->max('orders_sum_total') ?? 0,
            'by_order_count' => $byOrderCount,
        ];
    }

    private function calculateGrowth($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    private function getReportData($type, $request)
    {
        switch ($type) {
            case 'sales':
                return [
                    'salesOverview' => $this->getSalesOverview($request->start_date, $request->end_date),
                    'topProducts' => $this->getTopProducts($request->start_date, $request->end_date),
                    'dailySales' => $this->getDailySales($request->start_date, $request->end_date),
                ];
            case 'inventory':
                return [
                    'stockSummary' => $this->getStockSummary(),
                    'lowStockProducts' => $this->getLowStockProducts($request->threshold ?? 5),
                    'outOfStockProducts' => $this->getOutOfStockProducts(),
                ];
            case 'customers':
                return [
                    'customerOverview' => $this->getCustomerOverview($request->start_date, $request->end_date),
                    'topCustomers' => $this->getTopCustomers($request->start_date, $request->end_date),
                ];
            default:
                return [];
        }
    }
}