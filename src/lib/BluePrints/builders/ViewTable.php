<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsViewBuilderBase;

class ViewTable extends BluePrintsViewBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }

    private function GenerateComponent($contrlDefinition){
        
    }

    private function CreateSubViewComponent($relationName,$fieldList){
        
    }

    public function BuildView(): string{
        $content ='';
        if (strtolower($this->ViewDefinition['usage'] ?? 'display') === 'display') {
            $headers=[];
            $headerDef=[];
            foreach (($this->ViewDefinition['FieldList'] ?? []) as $fieldDefinition){
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
                                        "tableID"=>"DataTable_'. $this->ViewDefinition['name']. '",
                                        "header_bg"=>"none",
                                        '.((($this->ViewDefinition['headerSearch']??false)===true)? '"enableHeaderSearch"=>true,':'').'
                                        "headerList"=>[
                                            '.join(',',$headers). '
                                        ],
                                        "JsSettins"=>[
                                            "serverSide" => true,
                                            "ajax" => [
                                                "url" => route("bpr_dTable_sr_'. $this->ViewDefinition["name"]. '"),
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
        } else { //CRUD

        }

        return $content;
    }

    public function BuildCRUD(): string{
        
        return '';
    }

}