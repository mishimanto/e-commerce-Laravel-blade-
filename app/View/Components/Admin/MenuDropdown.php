<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class MenuDropdown extends Component
{
    public $icon;
    public $title;
    public $active;
    public $items;

    public function __construct($icon, $title, $items = [], $active = false)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->items = $items;
        $this->active = $active;
    }

    public function render()
    {
        return view('components.admin.menu-dropdown');
    }
}