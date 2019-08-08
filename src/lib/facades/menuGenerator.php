<?php 
namespace feiron\felaraframe\lib\facades;

use Illuminate\Support\Facades\Facade;

class menuGenerator extends Facade{
    protected static function getFacadeAccessor()
    {
        return \feiron\felaraframe\lib\helper\menuGenerator::class;
    }
}

?>