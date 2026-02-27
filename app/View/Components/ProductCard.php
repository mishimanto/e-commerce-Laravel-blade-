<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Product;

class ProductCard extends Component
{
    public $product;
    public $layout;

    public function __construct(Product $product, $layout = 'grid')
    {
        $this->product = $product->load(['brand', 'images', 'reviews']);
        $this->layout = $layout;
    }

    public function getDiscountPercentage()
    {
        if (!$this->product->sale_price) {
            return null;
        }
        
        return round((($this->product->base_price - $this->product->sale_price) / $this->product->base_price) * 100);
    }

    public function getAverageRating()
    {
        return $this->product->reviews->avg('rating') ?? 0;
    }

    public function getReviewCount()
    {
        return $this->product->reviews->count();
    }

    public function render()
    {
        return view('components.product-card', [
            'discount' => $this->getDiscountPercentage(),
            'rating' => $this->getAverageRating(),
            'reviewCount' => $this->getReviewCount()
        ]);
    }
}