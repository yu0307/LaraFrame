<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsViewBuilderBase;

class ViewAccordion extends BluePrintsViewBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }
    
    private function GenerateComponent($contrlDefinition){
        return '
                            <div class="accordion_component ' . ($contrlDefinition->container_class ?? '') . '" ' . ($contrlDefinition->container_attr ?? '') . '>
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
            <div class="accordion_component accr_subitems">
                <div class="accordion_item ">
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
        ';
        }
        return '';
    }

    public function BuildView(): string{
        $content = '';
        if (strtolower($this->ViewDefinition['usage'] ?? 'display') === 'display') {
            $baseModel = null;
            foreach (($this->ViewDefinition['FieldList'] ?? []) as $fieldDefinition) {
                $prefixModel=false;
                $subViewContents='';
                if (!isset($baseModel)) $baseModel = $this->ModelList[$fieldDefinition['modelName']];
                if( ($fieldDefinition['type']??'') == 'with') {
                    if(in_array(strtolower($baseModel->getRelationType($fieldDefinition['modelName'])), ['onetomany', 'manytomany'])){
                        $subViewContents= $this->CreateSubViewComponent($fieldDefinition['modelName'], $fieldDefinition['Fields']);
                    }else{
                        $prefixModel=true;
                    }
                }
                $content.= '
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="collapsed" data-toggle="collapse" data-parent="#My_Accordion" href="#AC_' . $fieldDefinition['modelName'] . '"> ' . ($fieldDefinition['label']??$fieldDefinition['modelName']) . '</a>
                            </h4>
                        </div>
                        <div id="AC_' . $fieldDefinition['modelName'] . '" class="panel-collapse collapse">
                            <div class="panel-body">
                                '.(strlen($subViewContents)>0?$subViewContents:(join('
                                ',array_map(function($field) use ($fieldDefinition, $prefixModel){ 
                                        if ($prefixModel == true) {
                                            $field->label = $field->name;
                                            $field->name = '["'.strtolower($fieldDefinition['modelName']) . 's"]' . $field->name;
                                        }
                                        return $this->GenerateComponent($field); 
                                    },($fieldDefinition['Fields']??[]))))).'
                            </div>
                        </div>
                    </div>
                ';
            }
        }else { //CRUD

        }

        return $content;
    }

    public function BuildCRUD(): string{
        
        return '';
    }

}