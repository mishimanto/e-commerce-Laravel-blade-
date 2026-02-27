<?php

namespace App\View\Components\Icons;

use Illuminate\View\Component;

class Heroicon extends Component
{
    public $name;
    public $class;

    public function __construct($name, $class = 'w-5 h-5')
    {
        $this->name = $name;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.icons.heroicon');
    }
}