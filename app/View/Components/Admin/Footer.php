<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Footer extends Component
{
    public $year;
    public $storeName;

    public function __construct()
    {
        $this->year = date('Y');
        $this->storeName = setting('store_name', 'Admin Panel');
    }

    public function render()
    {
        return view('components.admin.footer');
    }
}