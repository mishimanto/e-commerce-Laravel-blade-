<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $items;
    public $home;

    public function __construct($items = [], $home = true)
    {
        $this->items = $items;
        $this->home = $home;
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}