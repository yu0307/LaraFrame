<?php

namespace feiron\felaraframe\lib\components;

use Illuminate\View\Component;

class feDatePicker extends Component
{
    public $label;

    public function __construct()
    {

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('felaraframe::components.form.DatePicker');
    }
}
