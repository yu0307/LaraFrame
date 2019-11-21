<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsControllerBuilderBase;

class DisplayCollection extends BluePrintsControllerBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct($MethodDefinition, $ModelList);
    }

    public function BuildControllerMethod():array{
        dd($this->MethodDefinition);
        $renderModel = [];
        $functionContent = '';
        $paramRegister = [];
        $functionSignature=['functionName'=>((($this->MethodDefinition->type ?? 'GET').'_') . $this->MethodDefinition->name)];
        foreach($this->models as $model){
            $renderModel[$model]=false;
        }
        foreach (($this->MethodDefinition->input ?? []) as $input) {
            array_push($paramRegister, ('$' . $input->name . (($input->optional ?? false) == false ? '' : '=null')));
            if (isset($input->onModel)) {
                if (false === array_key_exists($input->onModel, $renderModel)) { //additional model other than page defined.
                    $renderModel[$input->onModel]=true;
                    $functionContent = '
                $where_' . $input->onModel . '=[];
                ' . $functionContent;
                }
                $functionContent .= '
                if(isset($' . $input->name . ')){array_push($where_' . $input->onModel . ',["' . $input->name . '",$' . $input->name . ']);}
                ';
            }
        }


        
        $whereModel=[];
        
        

        $filter='';
        if(!empty($with)){
            $filter.='['.join(',', $with).']';
        }
        if (!empty($whereModel)) {
            $filter.=(empty($with)?'':',').join(',',array_map(function($modelName){
                return ('(empty($where_'. $modelName. ')?[]:((fe_bp_'. $modelName. '::where($where_'. $modelName. ')->get()??new Collection([]))->toArray()))');
            }, $whereModel));
        }
        if((!empty($with) && !empty($whereModel)) || count($whereModel)>1){
            $filter="array_merge(". $filter.")";
        }

        if(strlen($filter)>0){
            $filter= "->with(". $filter. ")";
        }
        $functionSignature['content']='
            public function ' . ((($this->MethodDefinition->type ?? 'GET').'_') . $this->MethodDefinition->name) . ' (Request $request'.((empty($paramRegister)?'':(','.join(',', $paramRegister)))).'){
                '.(!empty($whereModel)? $functionContent:''). '
                return response()->json("' . $filter . '");
            }
        ';
        return $functionSignature;
    }

}