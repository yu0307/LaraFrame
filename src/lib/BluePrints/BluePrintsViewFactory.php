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

    private function GenerateCollectionComponent($contrlDefinition,$prefix){
        return '
                            <td class="collection_component ' . ($contrlDefinition->container_class ?? '') . '" ' . ($contrlDefinition->container_attr ?? '') . '>
                                <div class="collection_item ' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . ' >
                                    {{$' . $prefix . $contrlDefinition->name . '??""}}
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

    private function generateCollectionlPage(){
        $content = '';
        $header='';
        if (strtolower($this->Definition['usage'] ?? 'display') === 'display') {
            foreach ($this->Definition['models'] as $model => $fields) {
                if (is_string($fields) === true && strtolower($fields) == 'all') {
                    $fields = $this->AvailableModels[$model]->getFieldNames();
                }
                foreach ($fields as $field) {
                    $header .= ('<th>' . ($field->label ?? $field->name). '</th>');
                    $content .= $this->GenerateCollectionComponent($field,'row->');
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
                                '. $content. '
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        ';
        
        return $content;
    }

    private function generateSingularlPage(){
        $content = '';
        if(strtolower($this->Definition['usage']??'display')=== 'display'){
            foreach(($this->Definition['FieldList']??[]) as $fieldDefinition){
                foreach($fieldDefinition['Fields'] as $field)
                $content .= $this->GenerateSingularComponent($field);
            }
            $content= '<div class="container-fluid"><div class="row">'. $content. '</div></div>';
        }else{//CRUD

        }
        return $content;
    }

    private function getPageContents(){

        switch(strtolower($this->Definition['style'])??'singular'){
            case "table":
                break;
            case "accordian":
                break;
            case "collection":
                return $this->generateCollectionlPage();
                break;
            default://singular
                return $this->generateSingularlPage();
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