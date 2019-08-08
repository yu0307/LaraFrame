<?php 
namespace \felaraframe\lib\facades;

use Illuminate\Support\Facades\Facade;

class menuGenerator extends Facade{
    protected static function getFacadeAccessor()
    {
        return \\felaraframe\lib\helper\menuGenerator::class;
    }
}

?>