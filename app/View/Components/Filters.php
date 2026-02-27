<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Filters extends Component
{
    public $categories;
    public $brands;
    public $attributes;
    public $priceRange;
    public $selected;

    /**
     * Create a new component instance.
     */
    public function __construct($selected = [])
    {
        $this->selected = $selected;
        $this->priceRange = [
            'min' => 0,
            'max' => 200000
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.filters');
    }
}