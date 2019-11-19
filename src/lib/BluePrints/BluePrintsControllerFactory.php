<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\BluePrints\BluePrintsBaseFactory;

class BluePrintsControllerFactory extends BluePrintsBaseFactory{

    private $ControllerClassPostfix;
    private $MyRoutes;
    private $MyModels;

    public function __construct($definition = null, $ModelList)
    {
        parent::__construct($definition, $ModelList);
        $this->ControllerClassPostfix = '_FeBp_Controller';
        $this->MyRoutes=[];
        $this->MyModels=[];
        $this->ControllerName = ($this->Definition['name']??'').$this->ControllerClassPostfix;
        if(empty($this->Definition['routes']))
            $this->Definition['routes']=[(object)["name"=>$this->Definition['name']]]; //if route's not defined, create one for the page
        $this->ExtractInfo();
    }

    public function buildController(){
        
        if (!empty($this->Definition['name'])) {
            $target = self::controllerPath . $this->ControllerName . '.php';
            $contents = '<?php
        namespace App\Http\Controllers\BluePrints;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use Illuminate\Support\Collection;
        '.(empty($this->MyModels)?'':(join('',array_map(function($model){return '
        use App\model\fe_bp_'. $model.';';},array_keys($this->MyModels))))).'
        class '. $this->ControllerName.' extends Controller
        {
                    '. $this->buildControllerMethods().'
        }
            ';

            $this->RootStorage->put($target, $contents);
            return true;
        }
        return false;
    }

    private function BuildDisplayMethodContent($routeDefinition){
        $this->MyRoutes[$routeDefinition->name]->targetFunction = ('S_' . $routeDefinition->name);
        $whereModel=[];
        $with=[];
        $functionContent = '';
        $paramRegister=[];
        foreach($routeDefinition->input as $input){
            array_push($paramRegister, ('$' . $input->name . (($input->optional ?? false) == false ? '' : '=null')));
            if(isset($input->fromModel)){
                if(false===in_array($input->fromModel, $whereModel)){
                    array_push($whereModel, $input->fromModel);
                    $functionContent = '
                $where_' . $input->fromModel . '=[];
                '. $functionContent;
                }
                $functionContent .= '
                if(isset($'. $input->name. ')){array_push($where_'. $input->fromModel.',["' . $input->name . '",$' . $input->name . ']);}
                ';
            }else{
                array_push($with, ('"' . $input->name . '"=>($' . $input->name . '??"")'));
            }
        }

        $filter='';
        if(!empty($with)){
            $filter.='['.join(',', $with).']';
        }
        if (!empty($whereModel)) {
            $filter= "array_merge(".$filter;
            $filter.=(strlen($filter)>0?',':'').join(',',array_map(function($modelName){
                return ('(empty($where_'. $modelName. ')?[]:((fe_bp_'. $modelName. '::where($where_'. $modelName. ')->first()??new Collection([]))->toArray()))');
            }, $whereModel)).')';
        }
        if(strlen($filter)>0){
            $filter= "->with(". $filter. ")";
        }
        return '
            public function ' . ('S_' . $routeDefinition->name) . ' (Request $request'.((empty($paramRegister)?'':(','.join(',', $paramRegister)))).'){
                '.(!empty($whereModel)? $functionContent:'').'
                return view("fe_generated.' . $routeDefinition->targetView . '")' . $filter . ';
            }
        ';
    }

    private function buildControllerMethods(){
        
        foreach($this->MyRoutes as $routeName=>$route){
            switch (strtoupper($route->type ?? 'GET')) {
                case "POST":
                    break;

                case "GET":
                default: //Show
                    return $this->BuildDisplayMethodContent($route);
            }
        }

        return '';
    }

    private function ExtractInfo(){
        foreach(($this->Definition['routes']??[]) as $route){
            $route->targetView= (self::ViewClassPrefix . $this->Definition['name']);
            $route->name= ($route->name ?? $this->Definition['name']);
            $this->registerRoute($route);
        }
        
        foreach (($this->Definition['models'] ?? []) as $ModelName=>$ModelInfo){
            if (false === array_key_exists($ModelName, $this->MyModels)) {
                $this->MyModels[$ModelName] = [];
            }
            $this->MyModels[$ModelName]= $ModelInfo ?? [];
        }
    }

    public function registerRoute($routeDefinition){
        if(false===array_key_exists($routeDefinition->name,$this->MyRoutes)){
            $this->MyRoutes[$routeDefinition->name]= $routeDefinition;
        }else{
            $this->MyRoutes[$routeDefinition->name] = $routeDefinition;
        }
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
