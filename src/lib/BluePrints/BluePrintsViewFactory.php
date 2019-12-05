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
            case "crud":
                $methodName .= 'ViewCrudTable';
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
                            'class'=>'blueprints',
                            'headerText'=>'".(empty($this->Definition['title'])?'': ("<h3>". $this->Definition['title']."</h3>")). "'
                            ])
                    " . (empty($this->Definition['subtext']) ? '' : ("<h5 class='alert alert-info'>" . $this->Definition['subtext'] . "</h5>")) . "
                    " . $this->getPageContents() . "
                @endfePortlet
            @endsection
            ". ((strtolower($this->Definition['style']) ?? 'singular')=='crud'? "
            @push('footerscripts')
                <script type='text/javascript' src='{{asset('/feiron/felaraframe/components/BluePrints/js/blueprintCrud.js')}}'></script>
            @endpush
            @push('DocumentReady')
                    if(my_dataTable!=undefined){
                        MyDataTable=my_dataTable;
                        MyDataTable.CrudURL={
                            'create':'{{route('bpr_bp_crud_". $this->Definition['name']."_Create')}}',
                            'edit':'{{route('bpr_bp_crud_". $this->Definition['name']. "_Update')}}',
                            'delete':'{{route('bpr_bp_crud_". $this->Definition['name']. "_Delete')}}'
                        };
                    }
                    CrudInterface=$('#crud_controlpage');
            @endpush
            ":'')."
            ";
            $this->RootStorage->put($target, $contents);
            return $viewName;
        }
        return false;
    }
    
}