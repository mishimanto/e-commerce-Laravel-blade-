<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Models\Order;

class Sidebar extends Component
{
    public $pendingOrders;

    public function __construct()
    {
        try {
            $this->pendingOrders = Order::where('status', 'pending')->count();
        } catch (\Exception $e) {
            $this->pendingOrders = 0;
        }
    }

    public function render()
    {
        return view('components.admin.sidebar');
    }
}