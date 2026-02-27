<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Navbar extends Component
{
    public $unreadNotifications;
    public $notifications;

    public function __construct()
    {
        try {
            $user = auth()->user();
            $this->unreadNotifications = $user->unreadNotifications->count();
            $this->notifications = $user->notifications->take(5);
        } catch (\Exception $e) {
            $this->unreadNotifications = 0;
            $this->notifications = collect([]);
        }
    }

    public function render()
    {
        return view('components.admin.navbar');
    }
}