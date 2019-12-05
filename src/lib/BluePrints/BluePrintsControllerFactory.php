<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\BluePrints\BluePrintsBaseFactory;

class BluePrintsControllerFactory extends BluePrintsBaseFactory{

    private $ControllerName;

    public function __construct($definition = null, $ModelList=null){
        parent::__construct((array) $definition, $ModelList);
        $this->ControllerName = ($this->Definition['name']??'').self::ControllerClassPostfix;
    }

    public function buildController(){
        $this->ControllerName = ($this->Definition['name'] ?? '') . self::ControllerClassPostfix;
        if (!empty($this->Definition['name'])) {
            $target = self::controllerPath . $this->ControllerName . '.php';
            $using='';
            $contents = '<?php
        namespace App\Http\Controllers\BluePrints;
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use Illuminate\Support\Collection;
        '.(empty($this->Definition['useLib'])?'':(join('',array_map(function($model){return '
        use '.$model.';';}, $this->Definition['useLib'])))).'

        '.(empty($this->Definition['uses'])?'':(join('',array_map(function($use) use (&$using){
                $using.='
                use '. $use['name'].';';
                return '
        use '. $use['target'].';';
            }, $this->Definition['uses'])))).'

        '.(empty($this->Definition['useModels'])?'':(join('',array_map(function($model){return '
        use App\model\\'.self::ModelClassPrefix. $model.';';}, $this->Definition['useModels'])))).'

        

        class '. $this->ControllerName.' extends Controller
        {
            '. $using.'
            '. $this->buildControllerMethods().'
        }
            ';

            $this->RootStorage->put($target, $contents);
            return true;
        }
        return false;
    }

    private function buildControllerMethods(){
        $method='';
        foreach(($this->Definition['methods']??[]) as $methodDefinition){
            switch (strtolower($methodDefinition['style'] ?? 'singular')) {
                case "table":
                    $method .= $this->buildMethod('DisplayTable', $methodDefinition);
                    break;
                case "crud":
                    $method .= $this->buildMethod('DisplayCrudTable', $methodDefinition);
                    break;
                case "collection":
                    $method.=$this->buildMethod('DisplayCollection', $methodDefinition);
                    break;
                case "singular":
                default: //Show
                    $method.=$this->buildMethod('DisplaySingularInfo',$methodDefinition);
            }
        }
        return $method;
    }

    private function buildMethod($methodName,$methodDefinition){
        $methodName='feiron\\felaraframe\\lib\\BluePrints\\builders\\'.$methodName;
        if (class_exists($methodName)) {
            if (!empty($methodDefinition['name'])) {
                return '
                public function ' . ($methodDefinition['name']) . ' (Request $request ' . (empty($methodDefinition['params']) ? ''
                    : (',' . join(',', array_map(function ($p) {
                        return ('$' . $p->name.(($p->optional??false)===false?'':'=null'));
                    }, $methodDefinition['params'])))) . '){

                    $withData=[];
                    ' . (new $methodName($methodDefinition, $this->AvailableModels))->BuildMethod() . '
                    return ' . ((strtoupper($methodDefinition['type'] ?? 'GET') == 'GET') ? ('view("'.self::ViewPackage.'.' .self::ViewClassPrefix . $methodDefinition['view'] . '")->with($withData)') : ('response()->json($withData)')) . ';
                }
                ';
            }            
        }        
        return '';
    }  
}
