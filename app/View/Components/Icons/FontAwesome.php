<?php

namespace App\View\Components\Icons;

use Illuminate\View\Component;

class FontAwesome extends Component
{
    public $name;
    public $class;
    public $type;

    public function __construct($name, $class = '', $type = 'fas')
    {
        $this->name = $name;
        $this->class = $class;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.icons.font-awesome');
    }
}