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
    public abstract function BuildCRUD(): string;

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
            $contents='                    
                    $query='.self::modelClassPrefix. $modelDefinition->name.'::query();' ;
            // if(isset($this->madeVisible[$modelDefinition->name]) && !empty(($this->madeVisible[$modelDefinition->name]))){
            //     $contents .= '
            //         $query->getModel()->makeVisible([' . join(',', $this->madeVisible[$modelDefinition->name]) . ']);
            //     ';
            // }
            foreach(($modelDefinition->fields??[]) as $field){
                array_push($selects,($modelDefinition->name.'.'. $field->name));
            }
            if(isset($modelDefinition->with) && !empty($modelDefinition->with)) {//eager-loading
                $eagerLoad=[];
                foreach (($modelDefinition->with) as $withModel) {
                    $eager= ("'" . $withModel->name . "s'");
                    if(isset($withModel->params) && !empty($withModel->params)){
                        $using=[];
                        $eagerContent='';
                        foreach($withModel->params as $param){
                            array_push($using,'$'. $param->name);
                            $eagerContent.= 'if(!empty($' . $param->name . ') ){ $q->where("'. $param->name. '","=",$' . $param->name . ');}';
                        }
                        $eager .= '=> function($q) use ('.join(',',$using).'){'. $eagerContent.'}';
                    }
                    array_push($eagerLoad, $eager );
                }
                if(!empty($eagerLoad)){
                    $contents.= '
                    $query->with('.(count($eagerLoad)>1?("[".join(',', $eagerLoad)."]"):$eagerLoad[0]).');';
                }
            }

            if (isset($modelDefinition->join) && !empty($modelDefinition->join)) {
                $join=[];
                foreach (($modelDefinition->join) as $joinDefinition) {
                    if(isset($joinDefinition->name) && !empty($joinDefinition->name)){
                        array_push($join, (
                                            "->".(($joinDefinition->type??'')."Join(").
                                            ("'". $joinDefinition->name."'"). ', function($join){
                                                $join->'.
                                                
                                                join('->',array_map(function($onDef) use ($modelDefinition, $joinDefinition) {
                                                    return 'on("' . $modelDefinition->name . '.' . $onDef . '","=","' . $joinDefinition->name . '.' . $onDef . '")';
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
            if($this->prefixTableName && !empty($selects)){
                $contents .= '
                    $query->select(['.join(',
                    ',array_map(function($s){
                        return ("'".$s." as ".str_replace('.','~', $s)."'");
                    },$selects)).']);
                ';
            }
        }
        return $contents;
    }

}