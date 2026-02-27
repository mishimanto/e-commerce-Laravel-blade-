<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $message;
    public $dismissible;

    public function __construct($type = 'info', $message = null, $dismissible = false)
    {
        $this->type = $type;
        $this->message = $message ?? session($type);
        $this->dismissible = $dismissible;
    }

    public function getTypeClass()
    {
        return match($this->type) {
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700',
            default => 'bg-gray-100 border-gray-400 text-gray-700'
        };
    }

    public function render()
    {
        return view('components.alert');
    }
}