<?php

namespace feiron\felaraframe\lib\contracts;
interface feMenuItemInterface
{

    public function addMenu($menuItem);

    public function outputMenu():array;

}
