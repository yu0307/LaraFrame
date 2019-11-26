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
            <div class="page_component_container '. ($contrlDefinition->container_class ?? (in_array(strtolower($contrlDefinition->dataType ?? ''), ['text', 'longtext', 'mediumtext', 'tinytext']) ? 'col-md-12' : 'col-md-3 col-sm-6')).'" ' . ($contrlDefinition->container_attr??'') . '>
                <div class="page_component">
                    <div class="field_label">
                        ' . ($contrlDefinition->label ?? $contrlDefinition->name) . ' :
                    </div>
                    <div class="field_data' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . '>
                        ' . (empty($contrlDefinition->caption) ? "" : ('
                        <div>
                            <h5 class="alert alert-info p-5 m-5">
                                ' . $contrlDefinition->caption . '
                            </h5>
                        </div>
                        ')) . '
                        {{$' . $contrlDefinition->name . '??""}}
                    </div>
                </div>
            </div>
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

    private function generateCollectionPage(){
        $content = '';
        $header='';
        $tableContent='';
        if (strtolower($this->Definition['usage'] ?? 'display') === 'display') {
            foreach (($this->Definition['FieldList'] ?? []) as $fieldDefinition) {
                if (isset($fieldDefinition['type']) && $fieldDefinition['type'] == 'with') {
                    //handle multi-view (Many-to-Many)
                } else {
                    foreach ($fieldDefinition['Fields'] as $field){
                        $header .= ('<th>' . ($field->label ?? $field->name) . '</th>');
                        $tableContent .= $this->GenerateCollectionComponent($field, 'row');
                    }
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
        $subComponents='';
        if(strtolower($this->Definition['usage']??'display')=== 'display'){
            $baseModel=null;
            foreach(($this->Definition['FieldList']??[]) as $fieldDefinition){
                if(!isset($baseModel)) $baseModel= $this->AvailableModels[$fieldDefinition['modelName']];
                if (count($fieldDefinition['Fields'])>0 && isset($fieldDefinition['type']) && $fieldDefinition['type'] == 'with') {
                    $subComponents.=$this->CreateSubViewComponent(
                        $fieldDefinition['modelName'],
                        $fieldDefinition['Fields'], 
                        $baseModel->getRelationType($fieldDefinition['modelName']),
                        function($type,$fields) use ($fieldDefinition){
                            if($type== 'onetoone' || $type== 'manytoone'){
                                return $this->GenerateSingularComponent($fields);
                            }else{
                                $thead='';
                                $tcontent='';
                                foreach ($fields as $field) {
                                    $thead .= '
                                        <th>
                                            ' . ($field->label ?? $field->name) . '
                                        </th>';
                                    $field = (object)$this->AvailableModels[$fieldDefinition['modelName']]->getFieldDefinition($field->name);
                                    $tcontent.='
                                        <td>
                                            {{$'. ($fieldDefinition['modelName'] . "_row['" . $field->name . "']").'??""}}
                                        </td>
                                    ';
                                }
                                $thead='
                                    <tr>
                                        '. $thead. '
                                    </tr>';
                                return ' 
                                <table class="table table-striped table-hover">
                                    '. $thead.'
                                    @foreach($'. strtolower($fieldDefinition['modelName']).'s as $'. $fieldDefinition['modelName'].'_row)
                                        <tr>
                                            '. $tcontent. '
                                        </tr>
                                    @endforeach
                                </table>';
                            }
                            return '';
                        }
                    );
                }
                foreach($fieldDefinition['Fields'] as $field){
                    $definition=$this->AvailableModels[$fieldDefinition['modelName']]->getFieldDefinition($field->name);
                    $content .= $this->GenerateSingularComponent((object)array_merge((array)($definition??[]),(array)$field));
                }
            }
            $content= '<div class="container-fluid singular"><div class="row">'. $content. '</div></div>';
        }else{//CRUD

        }
        if(strlen($subComponents)>0){
            $content.= '
                <div class="container-fluid">
                    <div class="row">
                        <div class="panel-group" id="sub_component_Accordion" class="Accordions">
                            ' . $subComponents . '
                        </div>
                    </div>
                </div>
            ';
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