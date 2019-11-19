<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsControllerBuilderBase;

class DisplaySingularInfo extends BluePrintsControllerBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct($MethodDefinition, $ModelList);
    }

    public function BuildControllerMethod():array{
        $functionSignature=['functionName'=>('S_' . $this->MethodDefinition->name)];
        $whereModel=[];
        $with=[];
        $functionContent = '';
        $paramRegister=[];
        foreach($this->MethodDefinition->input as $input){
            array_push($paramRegister, ('$' . $input->name . (($input->optional ?? false) == false ? '' : '=null')));
            if(isset($input->onModel)){
                if(false===in_array($input->onModel, $whereModel)){
                    array_push($whereModel, $input->onModel);
                    $functionContent = '
                $where_' . $input->onModel . '=[];
                '. $functionContent;
                }
                $functionContent .= '
                if(isset($'. $input->name. ')){array_push($where_'. $input->onModel.',["' . $input->name . '",$' . $input->name . ']);}
                ';
            }else{
                array_push($with, ('"' . $input->name . '"=>($' . $input->name . '??"")'));
            }
        }

        $filter='';
        if(!empty($with)){
            $filter.='['.join(',', $with).']';
        }
        if (!empty($whereModel)) {
            $filter.=(empty($with)?'':',').join(',',array_map(function($modelName){
                return ('(empty($where_'. $modelName. ')?[]:((fe_bp_'. $modelName. '::where($where_'. $modelName. ')->first()??new Collection([]))->toArray()))');
            }, $whereModel));
        }
        if((!empty($with) && !empty($whereModel)) || count($whereModel)>1){
            $filter="array_merge(". $filter.")";
        }

        if(strlen($filter)>0){
            $filter= "->with(". $filter. ")";
        }
        $functionSignature['content']='
            public function ' . ('S_' . $this->MethodDefinition->name) . ' (Request $request'.((empty($paramRegister)?'':(','.join(',', $paramRegister)))).'){
                '.(!empty($whereModel)? $functionContent:'').'
                return view("fe_generated.' . $this->MethodDefinition->targetView . '")' . $filter . ';
            }
        ';
        return $functionSignature;
    }

}