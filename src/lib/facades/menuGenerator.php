<?php 
namespace FeIron\LaraFrame\lib\facades;

use Illuminate\Support\Facades\Facade;

class menuGenerator extends Facade{
    protected static function getFacadeAccessor()
    {
        return \FeIron\LaraFrame\lib\helper\menuGenerator::class;
    }
}

?>