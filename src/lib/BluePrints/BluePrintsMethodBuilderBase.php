<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\contracts\BluePrintMethodBuilderContract;

abstract class BluePrintsMethodBuilderBase implements BluePrintMethodBuilderContract {

    protected $MethodDefinition;
    protected $ModelList;
    protected $prefixTableName;
    protected $Modelparameters;
    private $PassInVariables;
    private $modelFilter;
    // protected $madeVisible;
    protected const modelClassPrefix= 'fe_bp_';
    private const DEFAULT=[
        "name" => "",
        "view" => "",
        "type" => "GET",
        "style" => "singular",
        "params" => [],
        "useModel" => [],
    ];

    public function __construct($MethodDefinition = null, $ModelList){
        $this->MethodDefinition=array_merge(self::DEFAULT,$MethodDefinition);
        $this->ModelList= $ModelList;
        $this->madeVisible=[];
        $this->prefixTableName=false;
        $this->extractParameters();
    }

    public abstract function BuildMethod():string;
    // public abstract function BuildCRUD(): string;

    // protected function SetVisible($modelName,$fieldName){
    //     if(!array_key_exists($modelName,$this->madeVisible)){
    //         $this->madeVisible[$modelName]= [("'" .$fieldName."'")];
    //     }else{
    //         if(!in_array($fieldName, $this->madeVisible[$modelName])){
    //             array_push($this->madeVisible[$modelName], ("'". $fieldName . "'"));
    //         }
    //     }
    // }

    private function extractParameters(){
        $this->PassInVariables = [];
        $this->modelFilter = [];
        $withModelList=[];
        if (isset($this->MethodDefinition['model'])) {
            foreach (($this->MethodDefinition['model']->with?? []) as &$withModel) {
                if (!array_key_exists($withModel->name, $withModelList)) {
                    $withModelList[$withModel->name]= &$withModel;
                }
            }
        }
        foreach ($this->MethodDefinition['params'] ?? [] as $param) {
            if (isset($param->onModel)) {
                if (array_key_exists($param->onModel, $withModelList)){
                    if(!isset($withModelList[$param->onModel]->params)){
                        $withModelList[$param->onModel]->params=[];
                    }
                    array_push($withModelList[$param->onModel]->params,$param);
                }else{
                    array_push($this->modelFilter, $param);
                }
            }else{
                array_push($this->PassInVariables, ('["' . $param->name . '"=>$' . $param->name . ']'));
            }
        }
    }

    protected function PrepareInputs(){
        $content = '';

        if (count($this->PassInVariables) > 0) {
            $content .= ((count($this->PassInVariables) > 1) ? ('
                    $withData=array_merge($withData,' . (join(',', $this->PassInVariables)) . ')') : '
                    array_push($withData,' . join('', $this->PassInVariables) . ')') . ';';
        }

        if (count($this->modelFilter) > 0) {
            $content .= '
                    $whereFilter=[];';
            foreach ($this->modelFilter as $filter) {
                if (($filter->optional ?? false) === true) {
                    $content .= '
                    if(!empty($' . $filter->name . ') ){array_push($whereFilter,["' . $filter->onModel . '.' . $filter->name . '","=",$' . $filter->name . ']);}
                            ';
                } else {
                    $content .= ('
                    array_push($whereFilter,["' . $filter->onModel . '.' . $filter->name . '","=",$' . $filter->name . ']);');
                }
            }
            $content .= '
                    $query->where($whereFilter);';
        }
        return $content;
    }

    protected function PrepareModels(){
        $contents ='';
        $modelDefinition= $this->MethodDefinition['model']??false;
        if(false!== $modelDefinition){
            $selects = [];
            $bridge=[];
            $contents='                    
                    $query='.self::modelClassPrefix. $modelDefinition->name.'::query();' ;
            foreach(($modelDefinition->fields??[]) as $field){
                array_push($selects,($modelDefinition->name.'.'. $field->name));
            }

            if(isset($modelDefinition->with) && !empty($modelDefinition->with)) {//eager-loading
                $eagerLoad=[];
                foreach (($modelDefinition->with) as $withModel) {
                    $eager= ("
                            '" . $withModel->name . "s'");
                    $using = [];
                    $eagerContent = (empty($withModel->fields ?? [])?'':('
                            $q->select([' . join(',',array_map(function($field){return ("'" . $field->name . "'"); }, $withModel->fields)). ']);
                        '));
                    if(!empty($this->MethodDefinition['params']) && isset($withModel->params) && !empty($withModel->params)){
                        foreach(($withModel->params??[]) as $param){
                            array_push($using,'$'. $param->name);
                            $eagerContent.= 'if(!empty($' . $param->name . ') ){ $q->where("'. $param->name. '","=",$' . $param->name . ');}';
                        }
                    }
                    if(strlen($eagerContent)>0){
                        $eager .= '=> function($q) use ($request'.(empty($using)?'': (',' . join(',', $using))).'){' . $eagerContent . '}';
                    }
                    array_push($eagerLoad, $eager );
                    $target= $this->ModelList[$modelDefinition->name]->getRelationTarget($withModel->name);
                    array_push($bridge, ("'".$modelDefinition->name . '.' . $target .' as '. $target."'"));
                }
                if(!empty($eagerLoad)){
                    $contents.= '
                    $query->with(['. join(',', $eagerLoad).']);';
                }
            }

            if (isset($modelDefinition->join) && !empty($modelDefinition->join)) { //Joining tables
                $join=[];
                foreach (($modelDefinition->join) as $joinDefinition) {
                    if(isset($joinDefinition->name) && !empty($joinDefinition->name)){
                        array_push($join, (
                                            "->".(($joinDefinition->type??'')."Join(").
                                            ("'". $joinDefinition->name."'"). ', function($join){
                                                $join->'.
                                                
                                                join('->',array_map(function($onDef) use ($modelDefinition, $joinDefinition) {
                                                    $onDef=explode(',', $onDef);
                                                    return 'on("' . $modelDefinition->name . '.' . ($onDef[1]?? $onDef[0]) . '","=","' . $joinDefinition->name . '.' . $onDef[0] . '")';
                                                }, $joinDefinition->on)).

                                                ((isset($joinDefinition->modifier)&& !empty(isset($joinDefinition->modifier)))?(
                                                    '
                                                    ->'.join('->',array_map(function($modDef) use ($joinDefinition) {
                                                    return 'where("' . $joinDefinition->name . '.' . $modDef->name . '","'. $modDef->symbol.'","' . $modDef->value . '")';
                                                }, $joinDefinition->modifier))
                                                ):'').
                                                ';
                                            })'
                                        ));
                        foreach ($joinDefinition->fields ?? [] as $field) {
                            array_push($selects, ($joinDefinition->name . '.' . $field->name));
                        }
                    }
                }
                if (!empty($join)) {
                    $contents .= '
                    $query'.join('->', $join).';';
                }
            }


            if($this->prefixTableName && (!empty($selects)|| !empty($bridge))){//Enabling select when duplicated column names are present or eager loading is present.
                $contents .= '
                    $query->select([
                                    "' . $modelDefinition->name . '.' . $this->ModelList[$modelDefinition->name]->getPrimary() . ' as tb_Identification",
                                    ' . join(',
                                        ', array_map(function ($s) {
                                        return ("'" . $s . " as " . str_replace('.', '~', $s) . "'");
                                    }, $selects)).((!empty($selects) && !empty($bridge))?',':''). join(', ', $bridge) . '
                                    
                                    ]);
                ';
            }
        }
        return $contents;
    }

}