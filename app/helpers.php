<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        try {
            return \App\Models\Setting::get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        };
    }
}

if (!function_exists('format_price')) {
    /**
     * Format price with currency
     *
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    function format_price($amount, $currency = null)
    {
        $currency = $currency ?? setting('currency_code', 'BDT');
        $symbol = setting('currency_symbol', '৳');
        $position = setting('currency_position', 'left');
        $decimalPlaces = setting('number_of_decimals', 2);
        $decimalSeparator = setting('decimal_separator', '.');
        $thousandSeparator = setting('thousand_separator', ',');

        $formatted = number_format($amount, $decimalPlaces, $decimalSeparator, $thousandSeparator);

        if ($position === 'left') {
            return $symbol . $formatted;
        } else {
            return $formatted . $symbol;
        }
    }
}

if (!function_exists('get_cart_count')) {
    /**
     * Get cart item count
     *
     * @return int
     */
    function get_cart_count()
    {
        if (auth()->check()) {
            return \App\Models\Cart::where('user_id', auth()->id())
                ->where('status', 'active')
                ->withCount('items')
                ->first()?->items_count ?? 0;
        }

        $sessionId = session()->getId();
        return \App\Models\Cart::where('session_id', $sessionId)
            ->where('status', 'guest')
            ->withCount('items')
            ->first()?->items_count ?? 0;
    }
}

if (!function_exists('get_wishlist_count')) {
    /**
     * Get wishlist item count
     *
     * @return int
     */
    function get_wishlist_count()
    {
        if (auth()->check()) {
            return auth()->user()->wishlist()->count();
        }

        $sessionId = session()->getId();
        return \App\Models\Wishlist::where('session_id', $sessionId)->count();
    }
}

if (!function_exists('get_compare_count')) {
    /**
     * Get compare list count
     *
     * @return int
     */
    function get_compare_count()
    {
        if (auth()->check()) {
            return auth()->user()->compares()->count();
        }

        $sessionId = session()->getId();
        return \App\Models\Compare::where('session_id', $sessionId)->count();
    }
}

if (!function_exists('active_route')) {
    /**
     * Check if current route matches
     *
     * @param string|array $route
     * @param string $class
     * @return string
     */
    function active_route($route, $class = 'active')
    {
        if (is_array($route)) {
            return request()->routeIs(...$route) ? $class : '';
        }
        return request()->routeIs($route) ? $class : '';
    }
}

if (!function_exists('get_rating_stars')) {
    /**
     * Get rating stars HTML
     *
     * @param float $rating
     * @param int $max
     * @return string
     */
    function get_rating_stars($rating, $max = 5)
    {
        $html = '<div class="rating-stars">';
        for ($i = 1; $i <= $max; $i++) {
            if ($i <= $rating) {
                $html .= '<i class="fas fa-star text-yellow-400"></i>';
            } elseif ($i - 0.5 <= $rating) {
                $html .= '<i class="fas fa-star-half-alt text-yellow-400"></i>';
            } else {
                $html .= '<i class="far fa-star text-gray-300"></i>';
            }
        }
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('generate_order_number')) {
    /**
     * Generate unique order number
     *
     * @return string
     */
    function generate_order_number()
    {
        $prefix = setting('order_prefix', 'ORD');
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
}

if (!function_exists('generate_invoice_number')) {
    /**
     * Generate unique invoice number
     *
     * @return string
     */
    function generate_invoice_number()
    {
        $prefix = setting('invoice_prefix', 'INV');
        return $prefix . '-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}