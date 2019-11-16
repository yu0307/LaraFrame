<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\BluePrints\BluePrintsBaseFactory;

class BluePrintsControllerFactory extends BluePrintsBaseFactory{

    private $ControllerClassPostfix;

    public function __construct($definition = null, $ModelList)
    {
        parent::__construct($definition, $ModelList);
        $this->ControllerClassPostfix = '_FeBp_Controller';
    }

    private function buildControllerMethods($routeDefinition){
        // $functionPrefix= strtoupper(($routeDefinition->type??'S_')[0]).'_';
        switch(strtoupper($routeDefinition->type??'show')){
            case "GET":
                break;
            case "POST":
                break;
            default: //Show
                return '
                    public function ' . ('S_' . $routeDefinition->name) . ' (Request $request){
                        return view("fe_generated.fe_view_' . $this->Definition['name'] . '");
                    }
                ';
        }
        return '';
    }

    public function buildController(){

        if (!empty($this->Definition['name']) && !empty($this->Definition['routes'])) { //You wont need a controller if there's no route.
            $className= $this->Definition['name'].$this->ControllerClassPostfix;
            $target = self::controllerPath . $className . '.php';
            $methods='';
            foreach($this->Definition['routes'] as $route){
                $methods.=$this->buildControllerMethods($route);
            }
            $contents = '<?php
                namespace App\Http\Controllers\BluePrints;
                use Illuminate\Http\Request;
                class '. $className.' extends Controller
                {
                    '. $methods.'
                }
            ';
            if(!empty($methods)){
                $this->RootStorage->put($target, $contents);
                return true;
            }
        }

        return false;
    }
}
