<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\builders\ViewTable;

class ViewCrudTable extends ViewTable {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
        array_push($this->headers, ("'Options'"));
        array_push($this->headerDef, ("
                                        ['data'=>null,'width'=> '100px', 'defaultContent'=>'<button class=\"crud_edit btn btn-sm btn-mini btn-primary\">Edit</button><button class=\"crud_delete btn btn-sm btn-mini btn-danger\">Delete</button>','className'=>'disableFilter bp_crud_col','searchable'=>false,'orderable'=>false]"));
    }

    public function BuildView(): string{
        return '<div class="bp_crud_controls">
                    <button class="btn_crud_add btn btn-success pull-right">Add New</button>
                </div>'.parent::BuildView().$this->buildCrudView();
    }

    private function buildCrudView(){
        $targetModel= $this->ViewDefinition['FieldList'][0];
        $targetModel= $this->ModelList[$targetModel['modelName']];

        return '
            @feModal([
                "modal_ID"=>"crud_controlpage",
                "footer"=>"<button class=\'btn btn-success btn_crud_update pull-left \'>Save</button> <button class=\'btn btn-danger pull-right \' data-dismiss=\'modal\'>Cancel</button>"
            ])
                <div class="crud_area row" role="form">
                    <input type="hidden" class="form-control" name="td_identification" id="td_identification"  value=""/>
                    '.join('
                    ',array_map(function($fieldDef){
                        if(
                            ($fieldDef['editable']??true)===false ||
                            ($fieldDef['primary'] ?? false) ===true ||
                            ($fieldDef['autoIncrement'] ?? false) === true ||
                            (in_array($fieldDef['dataType'], ['bigIncrements', 'tinyIncrements', 'smallIncrements', 'mediumIncrements', 'increments']))
                            ){
                                return '';    
                        }
                        return $this->generateFormControls($fieldDef);
                    }, ($targetModel->getFields()??[]))).'
                </div>
            @endfeModal
        ';
    }
}