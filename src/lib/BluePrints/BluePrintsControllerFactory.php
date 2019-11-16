<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\BluePrints\BluePrintsBaseFactory;

class BluePrintsControllerFactory extends BluePrintsBaseFactory{

    private $ControllerClassPostfix;
    private $MyRoutes;

    public function __construct($definition = null, $ModelList)
    {
        parent::__construct($definition, $ModelList);
        $this->ControllerClassPostfix = '_FeBp_Controller';
        $MyRoutes=[];
        $this->ControllerName = ($this->Definition['name']??'').$this->ControllerClassPostfix;
        if(empty($this->Definition['routes']))
            $this->Definition['routes']=[(object)["name"=>$this->Definition['name']]]; //if route's not defined, create one for the page
    }

    private function buildControllerMethods($routeDefinition){
        // $functionPrefix= strtoupper(($routeDefinition->type??'S_')[0]).'_';
        $contents='';
        $routeDefinition->name=($routeDefinition->name??$this->Definition['name']);
        switch(strtoupper($routeDefinition->type??'show')){
            
            case "POST":
                break;

            case "GET":
            default: //Show
                $contents= '
                        public function ' . ('S_' . $routeDefinition->name) . ' (Request $request){
                            return view("fe_generated.fe_view_' . $this->Definition['name'] . '")'.$this->getControllerInput($routeDefinition).';
                        }
                    ';
                $routeDefinition->targetFunction=('S_' . $routeDefinition->name);
                $this->MyRoutes[$routeDefinition->name]=$routeDefinition;
        }
        return $contents;
    }

    private function getControllerInput($routeDefinition){
        if(($routeDefinition->input??false)!==false){
            return '->with($request->all())';
        }
    }

    public function buildController(){
        
        if (!empty($this->Definition['name'])) {
            $target = self::controllerPath . $this->ControllerName . '.php';
            $methods='';
            
            foreach($this->Definition['routes'] as $route){ 
                
                $methods.=$this->buildControllerMethods($route);

            }
            $contents = '<?php
                namespace App\Http\Controllers\BluePrints;
                use Illuminate\Http\Request;
                use App\Http\Controllers\Controller;
                class '. $this->ControllerName.' extends Controller
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

    private function GetURLwithInputs($RouteDefinition){
        if(strtoupper($RouteDefinition->type??'GET')=='GET'){
            $route='/';
            foreach($RouteDefinition->input as $inputDef){
                $route.=('{'.$inputDef->name.(($inputDef->optional??false)===false?'':'?').'}/');
            }
            return rtrim($route,'/');
        }
        return '';
    }

    private function url($url){
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }

    public function buildRoutes(){
        $routes='';
        foreach($this->MyRoutes as $routeName=>$Definition){
            $routes.='
                Route::'.strtolower($Definition->type??'GET'.'("'.$this->url($Definition->slug??$routeName)).$this->GetURLwithInputs($Definition).'", "'.$this->ControllerName.'@'.$Definition->targetFunction.'")->name("bp_'.$Definition->name.'");
            ';
        }
        return $routes;
    }
}
