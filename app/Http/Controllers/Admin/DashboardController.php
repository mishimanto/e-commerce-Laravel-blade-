<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // এইটা IMPORTANT

class DashboardController extends Controller
{
    protected $orderRepository;
    protected $productRepository;
    protected $userRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Show admin dashboard
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Get notifications for navbar - এই অংশটা IMPORTANT
        $unreadNotifications = $user ? $user->unreadNotifications->count() : 0;
        $notifications = $user ? $user->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            }) : [];

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Today's stats
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $yesterdaySales = Order::whereDate('created_at', $yesterday)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $salesGrowth = $yesterdaySales > 0 
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 100;

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();
        
        $orderGrowth = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
            : 100;

        $totalCustomers = User::role('customer')->count();
        $newCustomers = User::role('customer')
            ->whereDate('created_at', '>=', $today)
            ->count();

        $lowStockProducts = Product::where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->count();
        
        $outOfStock = Product::where('stock', 0)->count();

        // Chart data for last 7 days
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('M d');
            
            $sales = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
            
            $chartData[] = $sales;
        }

        // Order status distribution
        $statusLabels = [];
        $statusData = [];
        
        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'confirmed' => 'Confirmed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        foreach ($statuses as $key => $label) {
            $count = Order::where('status', $key)->count();
            if ($count > 0) {
                $statusLabels[] = $label;
                $statusData[] = $count;
            }
        }

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Add status colors to orders
        foreach ($recentOrders as $order) {
            $order->status_color = $this->getStatusColor($order->status);
            $order->payment_status_color = $this->getPaymentStatusColor($order->payment_status);
        }

        // Top products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->where('order_items.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Recent activities (simplified - you can expand this)
        $activities = Order::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return (object)[
                    'description' => "New order #{$order->order_number} placed",
                    'created_at' => $order->created_at,
                    'causer' => $order->user
                ];
            });

        $pendingOrders = Order::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'todaySales',
            'salesGrowth',
            'todayOrders',
            'orderGrowth',
            'totalCustomers',
            'newCustomers',
            'lowStockProducts',
            'outOfStock',
            'chartLabels',
            'chartData',
            'statusLabels',
            'statusData',
            'recentOrders',
            'topProducts',
            'activities',
            'pendingOrders',
            'unreadNotifications', // এইটা IMPORTANT
            'notifications' // এইটা IMPORTANT
        ));
    }

    /**
     * Get status color for badge
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'warning',
            'processing' => 'info',
            'confirmed' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get payment status color for badge
     */
    private function getPaymentStatusColor($status)
    {
        return match($status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }
}