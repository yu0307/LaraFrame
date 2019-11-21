<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsMethodBuilderBase;

class DisplaySingularInfo extends BluePrintsMethodBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }

    public function BuildMethod(): string{

        $content='';
        $modelFilter = [];
        $PassInVariables = [];
        foreach(($this->MethodDefinition['params']??[]) as $param){
            if(isset($param->onModel) && !empty($param->onModel)){
                array_push($modelFilter, $param);
            }else{
                array_push($PassInVariables,('["'. $param->name .'"=>$'.$param->name.']'));
            }
        }
        if(count($PassInVariables)>0){
            $content.= ((count($PassInVariables)>1)?('$withData=array_merge($withData,'.(join(',', $PassInVariables)).')'): 'array_push($withData,'. join('',$PassInVariables).')').';';
        }


        return $content;



        // $functionSignature=['functionName'=>((($this->MethodDefinition->type ?? 'GET').'_') . $this->MethodDefinition->name)];
        // $whereModel=[];
        // $with=[];
        // $functionContent = '';
        // $paramRegister=[];
        // foreach(($this->MethodDefinition->input??[]) as $input){
        //     array_push($paramRegister, ('$' . $input->name . (($input->optional ?? false) == false ? '' : '=null')));
        //     if(isset($input->onModel)){
        //         if(false===in_array($input->onModel, $whereModel)){
        //             array_push($whereModel, $input->onModel);
        //             $functionContent = '
        //         $where_' . $input->onModel . '=[];
        //         '. $functionContent;
        //         }
        //         $functionContent .= '
        //         if(isset($'. $input->name. ')){array_push($where_'. $input->onModel.',["' . $input->name . '",$' . $input->name . ']);}
        //         ';
        //     }else{
        //         array_push($with, ('"' . $input->name . '"=>($' . $input->name . '??"")'));
        //     }
        // }

        // $filter='';
        // if(!empty($with)){
        //     $filter.='['.join(',', $with).']';
        // }
        // if (!empty($whereModel)) {
        //     $filter.=(empty($with)?'':',').join(',',array_map(function($modelName){
        //         return ('(empty($where_'. $modelName. ')?[]:((fe_bp_'. $modelName. '::where($where_'. $modelName. ')->first()??new Collection([]))->toArray()))');
        //     }, $whereModel));
        // }
        // if((!empty($with) && !empty($whereModel)) || count($whereModel)>1){
        //     $filter="array_merge(". $filter.")";
        // }
    }

}