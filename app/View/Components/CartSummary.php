<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\Cart\CartService;
use App\Models\Setting;

class CartSummary extends Component
{
    public $cart;
    public $items;
    public $subtotal;
    public $total;
    public $count;

    public function __construct(CartService $cartService)
    {
        $this->cart = $cartService->getCart();
        $this->items = $this->cart->items;
        $this->subtotal = $cartService->getSubtotal();
        $this->count = $this->items->count();
        
        $this->calculateTotal();
    }

    protected function calculateTotal()
    {
        try {
            // Use the Setting model correctly
            $shipping = Setting::where('key', 'shipping_flat_rate')->value('value') ?? 100;
            $taxRate = Setting::where('key', 'tax_rate')->value('value') ?? 15;
            
            $tax = $this->subtotal * ($taxRate / 100);
            
            $this->total = $this->subtotal + $shipping + $tax;
        } catch (\Exception $e) {
            // Fallback values if settings not available
            $this->total = $this->subtotal + 100 + ($this->subtotal * 0.15);
        }
    }

    public function render()
    {
        return view('components.cart-summary');
    }
}