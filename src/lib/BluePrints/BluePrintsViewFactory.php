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

    private function GenerateAccordionComponent($contrlDefinition){
        return '
                            <td class="accordion_component ' . ($contrlDefinition->container_class ?? '') . '" ' . ($contrlDefinition->container_attr ?? '') . '>
                                <div class="accordion_item ' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . ' >
                                    <div class="row ' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . ' >
                                        ' . (empty($contrlDefinition->caption) ? "" : ('
                                        <div class="col-md-12">
                                            <h5 class="alert alert-info">
                                                ' . $contrlDefinition->caption . '
                                            </h5>
                                        </div>
                                        ')) . '
                                        <div class="col-md-2 col-sm-12">
                                            <label>
                                                ' . ($contrlDefinition->label ?? $contrlDefinition->name) . ' :
                                            </label>
                                        </div>
                                        <div class="col-md-10 col-sm-12">
                                            {{$' . $contrlDefinition->name . '??""}}
                                        </div>
                                    </div>
                                </div>
                            </td>
        ';
    }

    private function GenerateCollectionComponent($contrlDefinition,$prefix){
        return '
                            <td class="collection_component ' . ($contrlDefinition->container_class ?? '') . '" ' . ($contrlDefinition->container_attr ?? '') . '>
                                <div class="collection_item ' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . ' >
                                    {{$' . $prefix .'["'. $contrlDefinition->name . '"]??""}}
                                </div>
                            </td>
        ';
    }

    private function GenerateSingularComponent($contrlDefinition){
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
                        {{$'.$contrlDefinition->name. '??""}}
                    </div>
                </div>
            </div>
        ';
    }

    private function generateCollectionPage(){
        $content = '';
        $header='';
        $tableContent='';
        if (strtolower($this->Definition['usage'] ?? 'display') === 'display') {
            foreach (($this->Definition['FieldList'] ?? []) as $fieldDefinition) {
                foreach ($fieldDefinition['Fields'] as $field){
                    $header .= ('<th>' . ($field->label ?? $field->name) . '</th>');
                    $tableContent .= $this->GenerateCollectionComponent($field, 'row');
                }
            }
        } else { //CRUD

        }
        $content= '
        <div class="container-fluid">
            <div class="row col-md-12 col-sm-12">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            '.$header. '
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collection as $row)
                            <tr>
                                '. $tableContent. '
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        ';
        
        return $content;
    }

    private function generateSingularPage(){
        $content = '';
        if(strtolower($this->Definition['usage']??'display')=== 'display'){
            foreach(($this->Definition['FieldList']??[]) as $fieldDefinition){
                foreach($fieldDefinition['Fields'] as $field){
                    $content .= $this->GenerateSingularComponent($field);
                }
            }
            $content= '<div class="container-fluid"><div class="row">'. $content. '</div></div>';
        }else{//CRUD

        }
        return $content;
    }

    private function generateAccordionPage(){
        $content = '';
        foreach (($this->Definition['FieldList'] ?? []) as $fieldDefinition) {
            $content.= '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="collapsed" data-toggle="collapse" data-parent="#My_Accordion" href="#AC_' . $fieldDefinition['modelName'] . '"> ' . ($fieldDefinition['label']??$fieldDefinition['modelName']) . '</a>
                        </h4>
                    </div>
                    <div id="AC_' . $fieldDefinition['modelName'] . '" class="panel-collapse collapse">
                        <div class="panel-body">
                            '.join('
                            ',array_map(function($field){ return $this->GenerateAccordionComponent($field);},($fieldDefinition['Fields']??[]))).'
                        </div>
                    </div>
                </div>
            ';
        }
        $content = '<div class="container-fluid">
                        <div class="row">
                            <div class="panel-group" id="My_Accordion" class="Accordions">
                                ' . $content . '
                            </div>
                        </div>
                    </div>';
        return $content;
    }

    private function generateTablePage(){
        $content = '<div class="container-fluid">
                        <div class="row">
                            <div class="panel-group" id="My_DataTable">
                                <table id="my_dataTable">
                                </table>
                            </div>
                        </div>
                    </div>';
        return $content;
    }

    private function getPageContents(){

        switch(strtolower($this->Definition['style'])??'singular'){
            case "table":
                return $this->generateTablePage();
                break;
            case "accordion":
                return $this->generateAccordionPage();
                break;
            case "collection":
                return $this->generateCollectionPage();
                break;
            default://singular
                return $this->generateSingularPage();
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