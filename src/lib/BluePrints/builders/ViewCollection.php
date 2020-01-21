<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsViewBuilderBase;

class ViewCollection extends BluePrintsViewBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }

    private function GenerateComponent($contrlDefinition){
        return '
                                <td class="collection_component ' . ($contrlDefinition->container_class ?? '') . '" ' . ($contrlDefinition->container_attr ?? '') . '>
                                    <div class="collection_item ' . ($contrlDefinition->class ?? '') . '" ' . ($contrlDefinition->attr ?? '') . ' >
                                        {{$row'. $contrlDefinition->name . '??""}}
                                    </div>
                                </td>
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
                            <tr class="hiddenItems shadow_tr">
                                <td class="hidden_content CL_' . $relationName . '" colspan="1000">
                                    <table class="table table-striped table-hover">
                                        ' . $thead . '
                                        @foreach($row["' . strtolower($relationName) . 's"] as $' . $relationName . '_row)
                                            <tr>
                                                ' . $tcontent . '
                                            </tr>
                                        @endforeach
                                    </table>
                                    <button class="btn btn-sm btn-mini btn-success pull-right closeHidden"> Close </button>
                                </td>
                            </tr>
        ';
        }
        return '';
    }

    public function BuildView(): string{
        $content = '';
        $header = '';
        $tableContent = '';
        $subComponents='';
        $baseModel=null;
        foreach (($this->ViewDefinition['FieldList'] ?? []) as $fieldDefinition) {
            $prefixModel = false;
            if (!isset($baseModel)) $baseModel = $this->ModelList[$fieldDefinition['modelName']];
            if (count($fieldDefinition['Fields'] ?? []) > 0) {
                if (($fieldDefinition['type'] ?? '') == 'with') {
                    if (in_array(strtolower($baseModel->getRelationType($fieldDefinition['modelName'])), ['onetomany', 'manytomany'])) {
                        $subComponents .= $this->CreateSubViewComponent($fieldDefinition['modelName'], $fieldDefinition['Fields']);
                        $tableContent .= '
                            <td class="collection_component">
                                <div class="collection_item" >
                                    <button class="btn btn-mini btn-primary btn-sm" target="CL_' . $fieldDefinition['modelName'] . '">View Details</button>
                                </div>
                            </td>
                        ';
                        $fieldDefinition['Fields']=[(object)['label'=> $fieldDefinition['modelName']]];
                    } else {
                        $prefixModel = true;
                    }
                }

                foreach ($fieldDefinition['Fields'] as $field) {
                    $header .= ('<th>' . ($field->label ?? $field->name) . '</th>');
                    if(isset($field->name)){
                        $newfield= clone($field);
                        $newfield->name= "['".$field->name."']";
                        if ($prefixModel == true) {
                            $newfield->label = $field->name;
                            $newfield->name = '["'.strtolower($fieldDefinition['modelName']) . 's"]' . $newfield->name;
                        }
                        $tableContent .= $this->GenerateComponent($newfield, 'row');
                    }
                }
            } 
        }
        $content = '
        <div class="container-fluid collections">
            <div class="row col-md-12 col-sm-12">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            ' . $header . '
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collection as $row)
                            <tr>
                                ' . $tableContent . '
                            </tr>
                            ' . $subComponents.'
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        ';

        return $content;
    }

}