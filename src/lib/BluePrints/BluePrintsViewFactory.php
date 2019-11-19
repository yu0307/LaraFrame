<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\BluePrintsBaseFactory;

class BluePrintsViewFactory extends BluePrintsBaseFactory {

    private const FormControlGroups=[
        'textarea' => [],
        'options' => []
    ];

    public function __construct($definition = null,$ModelList){
        parent::__construct($definition, $ModelList);
    }

    private function GenerateFormComponent($contrlDefinition){

    }

    private function GenerateDisplayComponent($contrlDefinition){
        return '
            <div class="page_component '. ($contrlDefinition->container_class ?? 'col-md-6 col-sm-12').'" ' . ($contrlDefinition->container_attr??'') . '>
                <div class="row ' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . ' >
                    ' . (empty($contrlDefinition->caption) ? "" : ('
                    <div class="col-md-12">
                        <h5 class="alert alert-info">
                            '. $contrlDefinition->caption.'
                        </h5>
                    </div>
                    ')) . '
                    <div class="col-md-4 col-sm-12">
                        <label>
                            '. ($contrlDefinition->label?? $contrlDefinition->name).' :
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        {{$'. $contrlDefinition->name. '??""}}
                    </div>
                </div>
            </div>
        ';
    }

    private function generateNormalPage(){
        $content = '';
        if(strtolower($this->Definition['usage']??'display')=== 'display'){
            foreach($this->Definition['models'] as $model=>$fields){
                foreach($fields as $field){
                    $content.=$this->GenerateDisplayComponent($field);
                }
            }
            $content= '<div class="container-fluid"><div class="row">'. $content. '</div></div>';
        }else{//CRUD

        }
        return $content;
    }

    private function getPageContents(){

        switch($this->Definition['style']??'normal'){
            case "table":
                break;
            case "accordian":
                break;
            default://normal
                return $this->generateNormalPage();
        }
        return "";
    }

    public function buildView(){
        if(!empty($this->Definition['name'])){
            $viewName = self::ViewClassPrefix . $this->Definition['name'];
            $target = self::viewPath . $viewName . '.blade.php';
            $contents = "
            @extends('page')
            @section('content')
                @fePortlet([
                            'id'=>'panel_". $this->Definition['name']."'
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