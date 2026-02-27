<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get orders with filters
     */
    public function getFilteredOrders(array $filters, $perPage = 15)
    {
        $query = $this->model->with(['user', 'items']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('billing_name', 'LIKE', "%{$search}%")
                  ->orWhere('billing_email', 'LIKE', "%{$search}%")
                  ->orWhere('billing_phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get order with details
     */
    public function getOrderWithDetails($id)
    {
        return $this->model
            ->with(['user', 'items.product', 'items.variant', 'payment'])
            ->findOrFail($id);
    }

    /**
     * Get orders by user
     */
    public function getOrdersByUser($userId, $limit = 10)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders($limit = 10)
    {
        return $this->model
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics($startDate = null, $endDate = null)
    {
        $query = $this->model->query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'average_order' => $query->avg('total'),
            'by_status' => $query->clone()
                ->groupBy('status')
                ->select('status', DB::raw('count(*) as count'))
                ->pluck('count', 'status')
                ->toArray(),
            'by_payment' => $query->clone()
                ->groupBy('payment_method')
                ->select('payment_method', DB::raw('count(*) as count'))
                ->pluck('count', 'payment_method')
                ->toArray(),
            'daily' => $query->clone()
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'), DB::raw('sum(total) as total'))
                ->orderBy('date')
                ->get()
        ];
    }

    /**
     * Find by order number
     */
    public function findByOrderNumber($orderNumber)
    {
        return $this->model
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Get pending orders count
     */
    public function getPendingCount()
    {
        return $this->model
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get orders requiring action
     */
    public function getOrdersRequiringAction()
    {
        return $this->model
            ->whereIn('status', ['pending', 'processing'])
            ->where('payment_status', 'paid')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get sales report data
     */
    public function getSalesReport($startDate, $endDate)
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with(['items.product'])
            ->get();
    }
}