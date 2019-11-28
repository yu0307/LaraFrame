<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\BluePrintsBaseFactory;

class BluePrintsViewFactory extends BluePrintsBaseFactory {

    protected const Defaults =[
        "name" => "",
        "style" => "singular",
        "usage" => "display",
        "title" => "",
        "subtext" => "",
        "html" => "",
        "FieldList"=>[]
    ];
    private const FormControlGroups=[
        'textarea' => [],
        'options' => []
    ];

    public function __construct($definition = null,$ModelList=null){
        parent::__construct(array_merge(self::Defaults, (array)$definition), $ModelList);
    }

    private function GenerateFormComponent($contrlDefinition){
    }

    private function getPageContents(){
        $methodName = 'feiron\\felaraframe\\lib\\BluePrints\\builders\\';
        switch(strtolower($this->Definition['style'])??'singular'){
            case "table":
                $methodName.= 'ViewTable';
                break;
            case "accordion":
                $methodName .= 'ViewAccordion';
                break;
            case "collection":
                $methodName .= 'ViewCollection';
                break;
            default: //singular
                $methodName.= 'ViewSingular';
        }
        if (class_exists($methodName)) {
            return (new $methodName($this->Definition, $this->AvailableModels))->BuildView();
        }
        return "";
    }

    public function buildView(){
        if(!empty($this->Definition['name'])){
            $viewName = self::ViewClassPrefix . $this->Definition['name'];
            $target = self::viewPath . $viewName . '.blade.php';
            $contents = "
            @extends('page')
            @push('headerstyles')
                <link href='{{asset('/feiron/felaraframe/components/BluePrints/css/blueprintDisplay.css')}}' rel='stylesheet' type='text/css'>
            @endpush
            @push('footerscripts')
                <script type='text/javascript' src='{{asset('/feiron/felaraframe/components/BluePrints/js/blueprintDisplay.js')}}'></script>
            @endpush
            @section('content')
                @fePortlet([
                            'id'=>'panel_". $this->Definition['name']. "',
                            'class'=>'blueprints'
                            ".(empty($this->Definition['title'])?'': (",'headerText'=>'<h3>". $this->Definition['title']."</h3>'")). "
                            ])
                    " . (empty($this->Definition['subtext']) ? '' : ("<h5 class='alert alert-info'>" . $this->Definition['subtext'] . "</h5>")) . "
                    " . $this->getPageContents() . "
                @endfePortlet
            @endsection

            ";
            $this->RootStorage->put($target, $contents);
            return $viewName;
        }
        return false;
    }
    
}