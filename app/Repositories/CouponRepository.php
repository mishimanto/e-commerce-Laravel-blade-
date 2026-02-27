<?php

namespace App\Repositories;

use App\Models\Coupon;
use Carbon\Carbon;

class CouponRepository extends BaseRepository
{
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active coupons
     */
    public function getActiveCoupons()
    {
        return $this->model
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get coupons with filters
     */
    public function getFilteredCoupons(array $filters, $perPage = 15)
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true)
                      ->where('starts_at', '<=', now())
                      ->where('expires_at', '>=', now());
            } elseif ($filters['status'] === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find valid coupon by code
     */
    public function findValidByCode($code, $userId = null, $total = null)
    {
        $query = $this->model
            ->where('code', $code)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now());

        // Check usage limit
        $coupon = $query->first();

        if (!$coupon) {
            return null;
        }

        // Check global usage limit
        if ($coupon->usage_limit && $coupon->total_used >= $coupon->usage_limit) {
            return null;
        }

        // Check per-user usage limit
        if ($userId && $coupon->usage_per_user) {
            $userUsage = $coupon->orders()
                ->where('user_id', $userId)
                ->count();

            if ($userUsage >= $coupon->usage_per_user) {
                return null;
            }
        }

        // Check minimum order amount
        if ($total && $total < $coupon->min_order_amount) {
            return null;
        }

        return $coupon;
    }

    /**
     * Get coupons applicable for user
     */
    public function getApplicableCoupons($userId = null)
    {
        $query = $this->model
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now());

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->whereJsonContains('applicable_users', $userId)
                  ->orWhereNull('applicable_users');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Increment usage count
     */
    public function incrementUsage($id)
    {
        return $this->model
            ->where('id', $id)
            ->increment('total_used');
    }

    /**
     * Check if coupon is valid for product
     */
    public function isValidForProduct($coupon, $productId)
    {
        // Check excluded products
        if (!empty($coupon->excluded_products) && 
            in_array($productId, $coupon->excluded_products)) {
            return false;
        }

        // Check applicable products
        if (!empty($coupon->applicable_products) && 
            !in_array($productId, $coupon->applicable_products)) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon is valid for category
     */
    public function isValidForCategory($coupon, $categoryId)
    {
        // Check excluded categories
        if (!empty($coupon->excluded_categories) && 
            in_array($categoryId, $coupon->excluded_categories)) {
            return false;
        }

        // Check applicable categories
        if (!empty($coupon->applicable_categories) && 
            !in_array($categoryId, $coupon->applicable_categories)) {
            return false;
        }

        return true;
    }

    /**
     * Get expired coupons
     */
    public function getExpiredCoupons()
    {
        return $this->model
            ->where('expires_at', '<', now())
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get expiring soon coupons
     */
    public function getExpiringSoon($days = 7)
    {
        return $this->model
            ->where('expires_at', '>=', now())
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get coupon usage statistics
     */
    public function getUsageStatistics()
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'expired' => $this->model->where('expires_at', '<', now())->count(),
            'total_used' => $this->model->sum('total_used'),
            'by_type' => $this->model
                ->groupBy('type')
                ->select('type', DB::raw('count(*) as count'))
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
}