<?php

namespace feiron\felaraframe\lib\components;

use Illuminate\View\Component;

class feSidebarMenu extends Component
{
    public $href;
    public $icon;
    public $subMenus;
    public $active;

    public function __construct($menu)
    {
        $this->href=$this->href??$menu['href'];
        $this->icon=$this->icon??($menu['title']=='home'?'home':'angle-right');
        $this->subMenus=$this->subMenus??$menu['subMenus']??null;
        $this->active=$this->active??$menu['active']??false;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('felaraframe::components.sidebarMenu');
    }
}
