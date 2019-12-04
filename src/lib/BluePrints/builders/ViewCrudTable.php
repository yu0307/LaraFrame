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
                </div>'.parent::BuildView();
    }
}