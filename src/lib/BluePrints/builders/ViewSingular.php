<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsViewBuilderBase;

class ViewSingular extends BluePrintsViewBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }
    protected function GenerateComponent($contrlDefinition){
        return '
            <div class="page_component_container '. ($contrlDefinition->container_class ?? (in_array(strtolower($contrlDefinition->dataType ?? ''), ['text', 'longtext', 'mediumtext', 'tinytext']) ? 'col-md-12' : 'col-md-3 col-sm-6')).'" ' . ($contrlDefinition->container_attr??'') . '>
                <div class="page_component">
                    <div class="field_label">
                        ' . ($contrlDefinition->label ?? $contrlDefinition->name) . ' :
                        ' . (empty($contrlDefinition->caption) ? "" : ('
                        <div class="subtext">
                            ' . $contrlDefinition->caption . '
                        </div>
                        ')) . '
                    </div>
                    <div class="field_data' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . '>
                        {{$' . $contrlDefinition->name . '??""}}
                    </div>
                </div>
            </div>
        ';
    }

    private function CreateSubViewComponent($relationName,$fieldList){
        if (!empty($fieldList)) {
            $thead = '';
            $tcontent = '';
            foreach ($fieldList as $field) {
                $thead .= '
                                    <th>
                                        ' . ($field->label ?? $field->name) . '
                                    </th>';
                // $field = (object) $this->ModelList[$relationName]->getFieldDefinition($field->name);
                $tcontent .= '
                                    <td>
                                        {{$' . ($relationName . "_row['" . $field->name . "']") . '??""}}
                                    </td>
                                    ';
            }
            $thead = '
                                <tr>
                                    ' . $thead . '
                                </tr>';
            return '
            <div class="panel panel-default">
                <div class="panel-heading bg-dark">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#sub_component_Accordion" href="#AC_' . $relationName . '"> Additional Information</a>
                    </h4>
                </div>
                <div id="AC_' . $relationName . '" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-striped table-hover">
                            ' . $thead . '
                            @foreach($' . strtolower($relationName) . 's as $' . $relationName . '_row)
                                <tr>
                                    ' . $tcontent . '
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        ';
        }
        return '';
    }

    public function BuildView(): string{
        $content = '';
        $subComponents = '';
        $baseModel = null;
        foreach (($this->ViewDefinition['FieldList'] ?? []) as $fieldDefinition) {
            $prefixModel=false;
            if (!isset($baseModel)) $baseModel = $this->ModelList[$fieldDefinition['modelName']];
            if (count($fieldDefinition['Fields']??[]) > 0){
                if( ($fieldDefinition['type']??'') == 'with') {
                    if(in_array(strtolower($baseModel->getRelationType($fieldDefinition['modelName'])), ['onetomany', 'manytomany'])){
                        $subComponents .= $this->CreateSubViewComponent($fieldDefinition['modelName'], $fieldDefinition['Fields']);
                        continue;
                    }else{
                        $prefixModel = true;
                    }
                    
                }

                foreach ($fieldDefinition['Fields'] as $field) {
                    $definition = $this->ModelList[$fieldDefinition['modelName']]->getFieldDefinition($field->name);
                    $newfield = clone ($field);
                    if($prefixModel==true){
                        $newfield->label= $field->name;
                        $newfield->name= $fieldDefinition['modelName'].'s->'. $newfield->name;
                    }
                    $content .= $this->GenerateComponent((object) array_merge((array) ($definition ?? []), (array) $newfield));
                }
            } 
        }
        $content = '<div class="container-fluid singular"><div class="row">' . $content . '</div></div>';
        
        if (strlen($subComponents) > 0) {
            $content .= '
                <div class="container-fluid">
                    <div class="row">
                        <div class="panel-group Accordions sub_info" id="sub_component_Accordion">
                            ' . $subComponents . '
                        </div>
                    </div>
                </div>
            ';
        }
        return $content;
    }
}