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

    private function CreateSubViewComponent($relationName,$fieldList, $type='onetoone',$componentFormater=null){
        $content='';
        $type= strtolower($type);
        switch ($type) {
            case "onetoone":
            case "manytoone":
                foreach ($fieldList as $field) {
                    $field = (object) $this->AvailableModels[$relationName]->getFieldDefinition($field->name);
                    $field->name=($relationName."['". $field->name."']");
                    if (is_callable($componentFormater)){
                        $content .= $componentFormater($type,$field);
                    }
                }
                break;
            case "onetomany":
            case "manytomany":
                if(!empty($fieldList)){
                    if (is_callable($componentFormater)) {
                        $content .= $componentFormater($type, $fieldList);
                    }
                }
                break;
        }
        return '
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#sub_component_Accordion" href="#AC_' . $relationName . '"> Additional Information</a>
                    </h4>
                </div>
                <div id="AC_' . $relationName . '" class="panel-collapse collapse">
                    <div class="panel-body">
                        '. $content.'
                    </div>
                </div>
            </div>
        ';
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
        $headers=[];
        $headerDef=[];
        foreach (($this->Definition['FieldList'] ?? []) as $fieldDefinition){
            if(isset($fieldDefinition['type']) && $fieldDefinition['type']=='with'){
               //handle multi-view 
            }else{
                foreach($fieldDefinition['Fields'] as $field){
                    array_push($headers, ("'".($field->label ?? $field->name)."'"));
                    array_push($headerDef, ("
                                                ['data'=>'". $fieldDefinition['modelName'].'~'. $field->name."']"));
                }
            }
        }

        $content = '<div class="container-fluid">
                        <div class="row">
                            <div class="panel-group" id="My_DataTable">
                                @feDataTable([
                                    "tableID"=>"DataTable_'. $this->Definition['name']. '",
                                    "header_bg"=>"none",
                                    '.((($this->Definition['headerSearch']??false)===true)? '"enableHeaderSearch"=>true,':'').'
                                    "headerList"=>[
                                        '.join(',',$headers). '
                                    ],
                                    "JsSettins"=>[
                                        "serverSide" => true,
                                        "ajax" => [
                                            "url" => route("bpr_dTable_sr_'. $this->Definition["name"]. '"),
                                            "type" => "POST"
                                        ],
                                    "columns" => [
                                                ' . join(',', $headerDef) . '
                                            ]
                                    ]
                                ])
                                @endfeDataTable
                            </div>
                        </div>
                    </div>';
        return $content;
    }

    private function getPageContents(){
        $methodName = 'feiron\\felaraframe\\lib\\BluePrints\\builders\\';
        switch(strtolower($this->Definition['style'])??'singular'){
            case "table":
                // return $this->generateTablePage();
                break;
            case "accordion":
                // return $this->generateAccordionPage();
                break;
            case "collection":
                // return $this->generateCollectionPage();
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