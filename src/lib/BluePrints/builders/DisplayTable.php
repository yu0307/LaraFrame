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
        return '';
    }

    public function BuildCRUD(): string{
        
        return '';
    }

    private function BuildFilter(){
        $filterList=[];
        if(isset($this->MethodDefinition['tableFilter']) && is_array($this->MethodDefinition['tableFilter'])){
            foreach($this->MethodDefinition['tableFilter'] as $filter){
                if($filter->fields!==false){
                    if($filter->fields=='all'){
                        foreach(($this->ModelList[$filter->name]->getFieldNames()??[]) as $field){
                            array_push($filterList, ("'" . $filter->name . "." . $field->name . "'"));
                        }
                    }else{
                        foreach($filter->fields as $field){
                            array_push($filterList,("'".$filter->name.".".$field."'"));
                        }
                    }
                }
            }
        }
        return $filterList;
    }

}