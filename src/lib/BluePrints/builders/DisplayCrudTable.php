<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsMethodBuilderBase;

class DisplayCrudTable extends BluePrintsMethodBuilderBase {

    private $inputList;

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
        $this->prefixTableName = true;
        $this->inputList=[];
    }

    public function BuildMethod(): string{
        switch($this->MethodDefinition['usage']??''){
            case "crud_Create":
                return $this->buildCreateEdit();
            case "crud_Update":
                return $this->buildCreateEdit(true);
            case "crud_Delete":
                return $this->buildDelete();
        }
        return '';
    }

    private function buildCreateEdit($isUpdate=false){
        return $this->buildValidator($isUpdate). '
                    $res=$this->validateRequest($validator, $request);
                    if($res===true){
                        $keyID=$request->input("td_identification");
                        $request->replace($request->only('. (count($this->inputList)>1?('['.join(',',array_map(function($input){return ("'".$input."'");}, $this->inputList)).']'):"'". $this->inputList[0]."'").'));
                        '.($isUpdate===false? '
                        $withData=$this->CRUD_Create($request,'. self::modelClassPrefix . $this->MethodDefinition['model']->name . '::class);
                        ': '
                        $request->merge(["' . $this->ModelList[$this->MethodDefinition['model']->name]->getPrimary() . '"=>$keyID]);
                        $withData=$this->CRUD_Update($request,"' . $this->ModelList[$this->MethodDefinition['model']->name]->getPrimary() . '",'. self::modelClassPrefix . $this->MethodDefinition['model']->name . '::class);'). '
                    }else{
                        return $res;
                    }
                    
        ';
    }

    private function buildDelete(){
        return '
                    if($request->filled("td_identification")){
                        $request->merge(["' . $this->ModelList[$this->MethodDefinition['model']->name]->getPrimary() . '"=>$request->input("td_identification")]);
                    } 
                    $validator = Validator::make($request->all(), [
                        "' . $this->ModelList[$this->MethodDefinition['model']->name]->getPrimary() . '"=>["required"]
                    ]);
                    $res=$this->validateRequest($validator, $request);
                    if($res===true){
                        $withData=$this->CRUD_Delete($request,"' . $this->ModelList[$this->MethodDefinition['model']->name]->getPrimary() . '",' . self::modelClassPrefix . $this->MethodDefinition['model']->name . '::class);
                    }else{
                        return $res;
                    }
                    
        ';
    }

    private function buildValidator($needIdentification=false){
        $rules=[];
        $message=[];
        $targetModel= $this->ModelList[$this->MethodDefinition['model']->name];
        if(!empty($this->MethodDefinition['model']->fields??[])){
            foreach(($this->MethodDefinition['model']->fields??[]) as $field){
                $fieldDef = $targetModel->getFieldDefinition($field->name);                
                if($targetModel->getPrimary()== $field->name && $needIdentification===true){
                    array_push($rules, ("'" . $field->name . "'=>['required']"));
                    array_push($this->inputList, $field->name);
                    array_push($message, ("'" . $field->name . ".required'=>'Identification is missing.'"));
                    continue;
                }
                if(($fieldDef['autoIncrement']??false)===true || ($fieldDef['editable'] ?? true) === false){
                    continue;
                }
                $fieldRule = [];
                if (($fieldDef['nullable']??true)===false) {
                    array_push($fieldRule, "'required'");
                    array_push($message, ("'" . $field->name . ".required'=>'" . $field->name . " is required.'"));
                }else{
                    array_push($fieldRule, "'nullable'");
                }
                if (($fieldDef['unique'] ?? false) === true) {
                    array_push($fieldRule, ("'unique:". $this->MethodDefinition['model']->name.','. $field->name."'"));
                    array_push($message, ("'" . $field->name . ".unique'=>'" . $field->name . " is duplicated.'"));
                }
                if(!empty($fieldDef['size'])){
                    array_push($fieldRule, ("'max:". $fieldDef['size']."'"));
                    array_push($message, ("'" . $field->name . ".max'=>'" . $field->name . " exceeded the max amount of length allowed(". $fieldDef['size'].").'"));
                }
                if (in_array($fieldDef['dataType'], ['bigIncrements', 'tinyIncrements', 'smallIncrements', 'mediumIncrements', 'increments'])) {
                    continue;
                }elseif(in_array($fieldDef['dataType'],['date','dateTime', 'dateTimeTz'])){
                    array_push($fieldRule, "'date'");
                    array_push($message, ("'" . $field->name . ".date'=>'" . $field->name . " is not a correct date format.'"));
                } elseif (in_array($fieldDef['dataType'], ['binary'])) {
                    array_push($fieldRule, "'boolean'");
                    array_push($message, ("'" . $field->name . ".binary'=>'" . $field->name . " should be true or false.'"));
                } elseif (in_array($fieldDef['dataType'], ['ipAddress'])) {
                    array_push($fieldRule, "'ip'");
                    array_push($message, ("'" . $field->name . ".ip'=>'" . $field->name . " is not a valid IP address.'"));
                } elseif (in_array($fieldDef['dataType'], ['json'])) {
                    array_push($fieldRule, "'JSON'");
                    array_push($message, ("'" . $field->name . ".JSON'=>'" . $field->name . " is not a valid JSON object.'"));
                }elseif (in_array($fieldDef['dataType'], ['bigInteger', 'decimal', 'double', 'decimal', 'float', 'integer', 'smallInteger', 'mediumInteger', 'tinyInteger', 'unsignedBigInteger', 'unsignedDecimal', 'unsignedInteger', 'unsignedMediumInteger', 'unsignedSmallInteger', 'unsignedTinyInteger', 'year'])) {
                    array_push($fieldRule, "'numeric'");
                    array_push($message, ("'" . $field->name . ".numeric'=>'" . $field->name . " is not a numeric value.'"));
                    if(in_array($fieldDef['dataType'], ['bigInteger', 'integer', 'smallInteger', 'mediumInteger', 'tinyInteger', 'unsignedBigInteger', 'unsignedInteger', 'unsignedMediumInteger', 'unsignedSmallInteger', 'unsignedTinyInteger'])){
                        array_push($fieldRule, "'integer'");
                        array_push($message, ("'" . $field->name . ".integer'=>'" . $field->name . " is not an integer.'"));
                    }
                }else{
                    array_push($fieldRule,"'string'");
                    array_push($message, ("'" . $field->name . ".integer'=>'Only string is allowed for " . $field->name . ".'"));
                }
                if (!empty($fieldRule)) {
                    array_push($rules, ("'" . $field->name . "'=>[" . join(',', $fieldRule) . ']'));
                }
                array_push($this->inputList, $field->name);
            }
            
        }

        if(!empty($rules)){
            return '
                    $customMessages = [
                        '.join(',
                        ', $message).'
                    ];
                    $validator = Validator::make($request->all(), [
                        '.join(',
                        ', $rules). '
                    ],$customMessages);
            ';
        }
        return '';
    }
}