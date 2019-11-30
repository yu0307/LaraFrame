<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsMethodBuilderBase;

class DisplayTable extends BluePrintsMethodBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
        $this->prefixTableName = true;
    }

    public function BuildMethod(): string{

        if(strtoupper($this->MethodDefinition['type'])=='POST'){
            return $this->PrepareModels(). '
                    $searchingFields = ['.join(',',$this->BuildFilter()). '];
                    $withData=$this->get_results($request, $query, $searchingFields);
            ';
        }
        // function ($eagerContent, $baseModel, $withModel) {
        //     if (!in_array(strtolower($baseModel->getRelationType($withModel->name)), ['onetomany', 'manytomany'])) {
        //         return $eagerContent .= '
        //                 if($request->filled("columns")){
        //                     foreach($request->input("columns") as &$filterColumn){
        //                         if (isset($filterColumn["search"]["value"])) {
        //                             if(in_array(strtolower($filterColumn["search"]["data"]),$eagerFilter)){
        //                                 $q->where($filterColumn["search"]["data"],"=",$filterColumn["search"]["value"]);
        //                             }
        //                         }
        //                     }
        //                 }
        //             ';
        //     }
        //     return $eagerContent;
        // }
        return '';
    }

    public function BuildCRUD(): string{
        
        return '';
    }

    private function BuildFilter(){
        $filterList=[];
        if(isset($this->MethodDefinition['tableFilter']) && is_array($this->MethodDefinition['tableFilter'])){
            foreach($this->MethodDefinition['tableFilter'] as $filter){
                if(!in_array($filter->name, ($this->MethodDefinition['model']->eager??[]))){
                    if ($filter->fields !== false) {
                        if ($filter->fields == 'all') {
                            foreach (($this->ModelList[$filter->name]->getFieldNames() ?? []) as $field) {
                                array_push($filterList, ("'" . $filter->name . "." . $field->name . "'"));
                            }
                        } else {
                            foreach ($filter->fields as $field) {
                                array_push($filterList, ("'" . $filter->name . "." . $field . "'"));
                            }
                        }
                    }
                }
            }
        }
        return $filterList;
    }

}