<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class MenuItem extends Component
{
    public $icon;
    public $title;
    public $route;
    public $badge;
    public $active;

    public function __construct($icon, $title, $route = null, $badge = null, $active = false)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->route = $route;
        $this->badge = $badge;
        $this->active = $active || ($route && request()->routeIs($route));
    }

    public function render()
    {
        return view('components.admin.menu-item');
    }
}