<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsMethodBuilderBase;

class DisplayTable extends BluePrintsMethodBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
        $this->prefixTableName = true;
    }

    private function PreModelProcess(){
        $modelDefinition = $this->MethodDefinition['model'] ?? false;
        if (false !== $modelDefinition && isset($modelDefinition->with) && !empty($modelDefinition->with)) { //eager-loading
            foreach (($modelDefinition->with) as $index=>$withModel) {
                $relatedModel= $this->ModelList[$modelDefinition->name];
                if (in_array(strtolower($relatedModel->getRelationType($withModel->name)), ['manytoone', 'onetoone'])) {
                    if(!isset($modelDefinition->join)){
                        $modelDefinition->join=[];
                    }
                    array_push($modelDefinition->join,(object)[
                        "type" => "left",
                        "name" => $withModel->name,
                        "fields" => $withModel->fields,
                        "on" => [$relatedModel->getRelationRemoteTarget($withModel->name).','. $relatedModel->getRelationTarget($withModel->name)],
                    ]);
                    unset($modelDefinition->with[$index]);
                }
            }
        }
    }

    public function BuildMethod(): string{
        if(strtoupper($this->MethodDefinition['type'])=='POST'){
            $this->PreModelProcess();
            return  $this->PrepareModels(). '
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