<?php
// app/View/Components/Modal.php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $title;
    public $closeable;
    public $maxWidth;

    /**
     * Create a new component instance.
     */
    public function __construct($title = null, $closeable = true, $maxWidth = '2xl')
    {
        $this->title = $title;
        $this->closeable = $closeable;
        $this->maxWidth = $maxWidth;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.modal');
    }
}