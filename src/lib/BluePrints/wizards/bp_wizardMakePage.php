<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;

use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardbase;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardMakeMigration;

class bp_wizardMakePage extends bp_wizardbase
{
    protected $command;
    protected $storage;
    private $PageName;
    private $CacheModelList;
    private $pageTemplate;
    private $relatedModels;

    public function __construct($Command,$modelList=null){
        parent::__construct($Command);
        $this->CacheModelList = $modelList??[];
        $this->pageTemplate = parent::PAGETEMPLATE;
        $this->relatedModels = [];
    }

    public function Build(){
        $this->PageName = $this->command->argument('name') ?? null;
        $this->Wizard(true);
    }

    public function ModelSetup(){
        $this->command->comment("-----> Let's setup some models");
        while (true) { //Process flow for creating a model definition
            $ModelDefinition = [
                'modelName' => null,
                'index' => null,
                'withTimeStamps' => false,
                'modelFields' => []
            ];
            $counter = 0;
            while (true) {
                $ModelDefinition['modelName'] = $this->command->ask('Model name:');
                $counter++;
                if (empty($ModelDefinition['modelName'])) {
                    $this->command->error('Model name is required.');
                    if ($counter > 2 && ($this->command->confirm('Do you wish to exit model management?'))) break 2;
                } else {
                    break 1;
                }
            }
            $ModelDefinition['index'] = $this->command->ask('Primary Key name ("idx" will be used if left empty):');
            if (empty($ModelDefinition['index'])) {
                $ModelDefinition['index'] = 'idx';
            }
            $ModelDefinition['withTimeStamps'] = $this->command->confirm('With timestamp fields?') ?? false;

            while (true) { //Process flow for adding fields
                $FieldDefinition = [
                    'name' => null,
                    'dataType' => 'string',
                ];
                $FieldDefinition['name'] = $this->command->ask('Field name (Enter empty to exit this section):');
                if (empty($FieldDefinition['name'])) break 1;
                $FieldDefinition['dataType'] = $this->command->anticipate('Data Type (empty to use "string"):', ['string', 'integer', 'date', 'double', 'text']) ?? 'string';

                if (false !== ($this->command->confirm('Nullable?') ?? false)) {
                    $FieldDefinition['nullable'] = true;
                }

                if (in_array($FieldDefinition['dataType'], parent::OPTIONLIST['unsingn'])) {
                    if (false !== ($this->command->confirm('Unsigned?') ?? false)) {
                        $FieldDefinition['unsigned'] = true;
                    }
                }

                if (in_array($FieldDefinition['dataType'], parent::OPTIONLIST['autoIncrement'])) {
                    if (false !== ($this->command->confirm('AutoIncrement?') ?? false)) {
                        $FieldDefinition['autoIncrement'] = true;
                    }
                }

                if (array_key_exists($FieldDefinition['dataType'], parent::OPTIONLIST['hasOption'])) {
                    foreach (parent::OPTIONLIST['hasOption'][$FieldDefinition['dataType']] as $option => $default) {
                        if (in_array($FieldDefinition['dataType'], ['char', 'string'])) {
                            $FieldDefinition['size'] = $this->command->ask(ucfirst($option) . '(Default:' . $default . '):') ?? $default;
                        } else {
                            $FieldDefinition['modifier'] = $this->command->ask(ucfirst($option) . '(Default: ' . $default . ' ):') ?? $default;
                        }
                        if (in_array($FieldDefinition['dataType'], ['set', 'enum'])) {
                            $FieldDefinition['modifier'] = explode(',', $FieldDefinition['modifier']);
                        }
                    }
                }
                if (!in_array($FieldDefinition['dataType'], parent::OPTIONLIST['NonDefault'])) {
                    $default = $this->command->ask('Default value? (boolean use 1,0):');
                    if (!empty($default)) {
                        $FieldDefinition['default'] =  $default;
                    }
                }
                if($this->command->confirm('Is this field in a relationship with other tables?')){
                    $this->command->comment('Which model is the field related to:');
                    if(!empty($this->CacheModelList)){
                        $this->command->info('Available Models are : ['.join(",\t",array_keys($this->CacheModelList)).']');
                    }
                    $FieldDefinition['relation']['target']= $this->command->ask('Choose from the available model list or type one model name that is planed to be created later in the process.');
                    $FieldDefinition['relation']['targetReference'] = $this->command->ask('What is the associated field name on the target model?');
                    $FieldDefinition['relation']['onDelete'] = $this->command->choice('On delete operation:',['cascade', 'restrict', 'set null','set default','no action'],0);
                    $FieldDefinition['relation']['type'] = $this->command->choice('Relation Type:', ['OneToOne', 'ManyToMany', 'OneToMany', 'ManyToOne'], 0);
                    $FieldDefinition['nullable'] = false;
                    if (in_array($FieldDefinition['dataType'], parent::OPTIONLIST['unsingn'])) {
                        $FieldDefinition['unsigned'] = true;
                    }
                }
                array_push($ModelDefinition['modelFields'], $FieldDefinition);
            }
            $this->CacheModelList[$ModelDefinition['modelName']] = $ModelDefinition;
            if ($this->command->confirm('Finished with models design? Choose "No" to continue adding more models.')) break 1;
        }
        return $this->CacheModelList;
    }

    public function Wizard($banner = false){
        if ($banner === true) {
            $this->command->comment("=====Welcome to BluePrints Page building utility======");
            $this->command->comment("This wizard will walk you through the steps to create a page.\nLet's get started...");
        }
        if($banner === false){
            $this->PageName=null;
            $this->relatedModels = [];
            $this->pageTemplate = parent::PAGETEMPLATE;
        }

        if(empty($this->PageName)){
            while (empty($this->PageName)) {
                $this->PageName = $this->command->ask('Page Name(without ".php"):');
            }
        }
        $this->pageTemplate['name']= $this->PageName;
        $this->pageTemplate['title'] = str_replace("'","\'",$this->command->ask('Page Title('. $this->PageName.' will be used if left empty):')?? $this->PageName);
        $this->pageTemplate['subtext'] = str_replace("'", "\'", $this->command->ask('Sub heading (Displayed at the top as h5):') ?? '');
        $this->pageTemplate['style'] = $this->command->choice('Page Style (default is "singular"):', ['singular', 'table', 'accordion', 'collection', 'crud', 'crudsingleton', 'crudsingletonlist'], 0);

        if(empty($this->CacheModelList) && $this->command->confirm("I don't see any models setup in the system. Pages may need models to work with database. Would you like to setup some models?")){
            $this->ModelSetup();
        }

        //Dealing with Page Model Definition and setups------------------------------------------
        if(!empty($this->CacheModelList) && $this->command->confirm('Any models used in this page?')===true){

            $this->pageTemplate['model']['name'] = $this->command->choice('Which model is used:', array_keys($this->CacheModelList),0);
            array_push($this->relatedModels, $this->pageTemplate['model']['name']);
            $this->command->comment("What fields are used from this model?");
            $fieldList='';
            foreach($this->CacheModelList[$this->pageTemplate['model']['name']]['modelFields']??[] as $fieldDef){
                $fieldList.= $fieldDef['name'].",\t";
            }

            $this->command->comment("Available Fields are: [". $fieldList.']' );
            $this->pageTemplate['model']['fields'] = $this->command->ask("Type 'all' to use all available fields or use syntax <fieldName:label,...> to define the list(eg: name:user name, age:user age,...).")??'all';
            $this->pageTemplate['model']['fields']=(($this->pageTemplate['model']['fields']=='all')?'all':array_map(function($field){
                $field= explode(':',$field);
                return ['name'=> $field[0],'caption'=>($field[1]?? $field[0])];
            },explode(',', $this->pageTemplate['model']['fields'])));

            if(count($this->CacheModelList) > 1){
                //Dealing with joins -------------------------------------------------
                if ($this->command->confirm('Joined with any other models?') === true) { 
                    while(true){
                        $joins=[
                            'on'=>[]
                        ];
                        $joins['name'] = $this->command->choice('Which model is to be joined with:', array_keys($this->CacheModelList), 0);
                        array_push($this->relatedModels, $joins['name']);
                        $joins['type'] = $this->command->choice('Join Type:', ['left','right','cross','inner'], 0);
                        $joins['type']= ($joins['type']=='inner')?'': $joins['type'];
                        $this->command->comment("What fields are used from this join?");
                        $fieldList = '';
                        foreach ($this->CacheModelList[$joins['name']]['modelFields'] ?? [] as $fieldDef) {
                            $fieldList .= $fieldDef['name'] . "\t";
                        }
                        $this->command->comment("Available Fields are: " . $fieldList);
                        $joins['fields'] = $this->command->ask("Type 'all' to use all available fields or use syntax <fieldName,...> to define the list(eg: name, age,...).") ?? 'all';
                        $joins['fields'] = (($joins['fields'] == 'all') ? 'all' : array_map(function($fieldName){return ['name'=> $fieldName,'caption'=> $fieldName];}, explode(',', $joins['fields'])));
                        while(true){
                            array_push($joins['on'],$this->command->ask("Join on keys (format: local,foreign)<eg: name,foreignName>:")??'');
                            if ($this->command->confirm('More joint keys?') === false) {
                                break 1;
                            }
                        }
                        if ($this->command->confirm('Some additional constrains?') === true) {
                            while(true){
                                $modifier=[];
                                $modifier['name'] = $this->command->ask("On which field? \n" . $fieldList.' :')??'';
                                if(empty($modifier['name'])){
                                    break 1;
                                }
                                $modifier['symbol'] = $this->command->choice("Operator:",['=','>','<','<>','LIKE'],0);
                                $modifier['value'] = $this->command->ask("Against Value (empty for NULL):")??NULL;
                                if(array_key_exists('modifier', $joins)===false){
                                    $joins['modifier']=[];
                                }
                                array_push($joins['modifier'], $modifier);
                                if ($this->command->confirm('No more constrains?') === true) {
                                    break 1;
                                }
                            }
                        }
                        if(array_key_exists('join', $this->pageTemplate['model'])===false){
                            $this->pageTemplate['model']['join']=[];
                        }
                        array_push($this->pageTemplate['model']['join'], $joins);
                        if($this->command->confirm('No more joins?') === true){
                            break 1;
                        }
                    }
                }
                //End with Join checks-------------------------------------------------
    
    
                //Dealing with model eager loadings------------------------------------
                if ($this->command->confirm('Loaded with any other models (Laravel Eager Loading)?') === true) {
                    while(true){
                        $with=[];
                        $with['name'] = $this->command->choice('Which model is to be loaded with:', array_keys($this->CacheModelList), 0);
                        array_push($this->relatedModels, $with['name']);
                        $this->command->comment("What fields are used?");
                        $fieldList = '';
                        foreach ($this->CacheModelList[$with['name']]['modelFields'] ?? [] as $fieldDef) {
                            $fieldList .= $fieldDef['name'] . "\t";
                        }
                        $this->command->comment("Available Fields are: " . $fieldList);
                        $with['fields'] = $this->command->ask("Type 'all' to use all available fields or use syntax <fieldName,...> to define the list(eg: name, age,...).") ?? 'all';
                        $with['fields'] = (($with['fields'] == 'all') ? 'all' : explode(',', $with['fields']));

                        array_push($this->pageTemplate['model']['with'], $with);
                        if ($this->command->confirm('No more loaded models?') === true) {
                            break 1;
                        }
                    }
                }
                //End with model eager loadings----------------------------------------
            }
        }
        //End with Page Model Definition and setups------------------------------------------

        //Dealing with Route Setups-----------------------------
        $this->command->info("Let's setup some routes for this page...");
        while(true){
            $route=[];
            while (true) {
                $route['name'] = $this->command->ask('Route Name:') ?? null;
                if (empty($route['name'])) {
                    $this->command->error('Route Name is required.');
                } else {
                    break 1;
                }
            }
            $route['input'] = $this->command->choice('Route type:',['GET','POST'],0);
            if ($this->command->confirm('Any inputs expected to this route?') === true) {
                $route['input'] = [];
                while (true) {
                    $input = [
                        'optional' => true
                    ];
                    if ($this->command->confirm('Is this input associated with a model?') === true) {
                        $input['onModel'] = $this->command->choice('Which model is used:', array_keys($this->CacheModelList), 0);
                        $fieldList = [];
                        foreach ($this->CacheModelList[$input['onModel']]['modelFields'] ?? [] as $fieldDef) {
                            array_push($fieldList, $fieldDef['name']);
                        }
                        $input['name'] = $this->command->choice('which field is used from this model?', $fieldList, 0);
                    } else {
                        while (true) {
                            $input['name'] = $this->command->ask('Parameter Name:') ?? null;
                            if (empty($input['name'])) {
                                $this->command->error('Parameter Name is required.');
                            } else {
                                break 1;
                            }
                        }
                    }
                    $input['optional'] = ($this->command->confirm('Optional Input?') === true) ?? false;
                    array_push($route['input'], $input);
                    if ($this->command->confirm('Finished with inputs?') === true) {
                        break 1;
                    }
                }
            }
            array_push($this->pageTemplate['routes'], $route);
            if ($this->command->confirm('Add another route?') === false) {
                break 1;
            }
        }
        //End Dealing with Route Setups-------------------------


        // Table Style page specific options--------------------
        if($this->pageTemplate['style']== 'table'){
            $this->pageTemplate['headerSearch']= $this->command->confirm('Enable table header search?')??false;
            if(true=== $this->command->confirm('Define table header filters?')){
                $this->pageTemplate['tableFilter']=[];
                foreach($this->relatedModels as $modelName){
                    $filter=[
                        'name'=> $modelName
                    ];
                    if($this->command->confirm("Use filter on model:$modelName?")===false){
                        $filter['fields']=false;
                    }else{
                        $fieldList = '';
                        foreach ($this->CacheModelList[$modelName]['modelFields'] ?? [] as $fieldDef) {
                            $fieldList .= $fieldDef['name'] . "\t";
                        }
                        $this->command->comment("Available Fields are: " . $fieldList);
                        $filter['fields'] = $this->command->ask("What fields are used as filters on this model?\n ->'all' for every field\n ->empty to disable filters on this model\n ->seperate each with comma(,) eg: name,age,... ")??false;
                        $filter['fields']=(($filter['fields']=='all')?'all':explode(',', $filter['fields']));
                    }
                    array_push($this->pageTemplate['tableFilter'], $filter);
                }
            }
        }
        // End with Table Style page specific options-----------
        
        if ($banner === true) {
            $this->command->comment("=====Thank you for using BluePrint Page Wizard======");
        }

        return $this->pageTemplate;
    }

    public function ExportMyModels($isJson=false){
        $export=[];
        foreach($this->CacheModelList as $modelName=>$ModelDef){
            array_push($export, $ModelDef);
        }
        return ($isJson)?json_encode($export, JSON_PRETTY_PRINT):$export;
    }
}
