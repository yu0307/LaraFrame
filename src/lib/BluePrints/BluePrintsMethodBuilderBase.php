<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\contracts\BluePrintMethodBuilderContract;

abstract class BluePrintsMethodBuilderBase implements BluePrintMethodBuilderContract {

    protected $MethodDefinition;
    protected $ModelList;
    protected $prefixTableName;
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

    protected function PrepareInputs(){
        $content = '';
        $modelFilter = [];
        $PassInVariables = [];
        foreach (($this->MethodDefinition['params'] ?? []) as $param) {
            if (isset($param->onModel) && !empty($param->onModel)) {
                array_push($modelFilter, $param);
            } else {
                array_push($PassInVariables, ('["' . $param->name . '"=>$' . $param->name . ']'));
            }
        }
        if (count($PassInVariables) > 0) {
            $content .= ((count($PassInVariables) > 1) ? ('
                    $withData=array_merge($withData,' . (join(',', $PassInVariables)) . ')') : '
                    array_push($withData,' . join('', $PassInVariables) . ')') . ';';
        }
        if (count($modelFilter) > 0) {
            $content .= '
                    $whereFilter=[];';
            foreach ($modelFilter as $filter) {
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
                    array_push($eagerLoad, ("'".$withModel->name. "s'"));
                    // foreach($withModel->fields??[] as $field){
                    //     array_push($selects, ($withModel->name . '.' . $field->name));
                    // }
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
                        return ("'".$s." as ".str_replace('.','_', $s)."'");
                    },$selects)).']);
                ';
            }
        }
        return $contents;
    }

}