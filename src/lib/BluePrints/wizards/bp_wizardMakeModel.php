<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardbase;

class bp_wizardMakeModel extends bp_wizardbase
{
    protected $command;
    protected $storage;
    private $path;
    private $modelName;
    private $modelDefinition;
    private $FieldList;
    private $Guarded;
    private $tableName;
    private $PrimaryKey;
    public function __construct($Command)
    {
        parent::__construct($Command);
        $this->path= ((empty($this->command->option('path')))?'/App/Model': $this->command->option('path'));
        $this->modelName=$this->command->argument('name')??null;
        $this->Guarded = [];
    }

    public function Build(){
        if (empty($this->modelName)&& ($this->command->option('wizard') === false)) {
            $this->command->error('Model name is required or using flag -W to use wizard.');
        }else{
            if($this->command->option('wizard') !== false){
                $this->Wizard(true);
            }else{
                $this->PrimaryKey = $this->command->option('key') ?? null;
                $this->tableName = $this->command->option('table') ?? strtolower($this->modelName) . 's';
                if (!empty($this->command->option('datafields'))) {
                    foreach ((explode(',', $this->command->option('datafields')) ?? []) as $field) {
                        $fieldDef = explode(':', $field);
                        $field = [];
                        if (array_key_exists($fieldDef[0], $this->FieldList)) {
                            $field = $this->FieldList[$fieldDef[0]];
                        }
                        $field['name'] = $fieldDef[0];
                        array_push($this->Guarded, $field['name']);
                        if (count($fieldDef) <= 1) {
                            $field['dataType'] = 'string';
                            $field['size'] = '100';
                        } else {
                            preg_match('/\((.*?)\)/i', $fieldDef[1], $option);
                            if (empty($option)) {
                                $field['dataType'] = $fieldDef[1];
                            } else {
                                $field['dataType'] = str_replace('(' . $option[1] . ')', '', $fieldDef[1]);
                                if (in_array($field['dataType'], ['string', 'char'])) {
                                    $field['size'] = $option[1];
                                }
                            }
                        }
                        $this->FieldList[$field['name']] = $field;
                    }
                }
                if (!empty($this->command->option('fillable'))) {
                    foreach ((explode(',', $this->command->option('fillable')) ?? []) as $fieldName) {
                        if (!array_key_exists($fieldName, $this->FieldList)) {
                            $this->FieldList[$fieldName] = [
                                "name" => $fieldName,
                                "dataType" => "string",
                                "size" => 100,
                                "editable" => true
                            ];
                        } else {
                            $this->FieldList[$fieldName]['editable'] = true;
                            if (false !== array_search($fieldName, $this->Guarded)) {
                                unset($this->Guarded[array_search($fieldName, $this->Guarded)]);
                            }
                        }
                    }
                }
            }
            $this->modelDefinition = (object) [
                "modelName" => $this->modelName,
                "modelFields" => [],
                "index" => $this->PrimaryKey
            ];
            
            $path = '/' . str_replace('\\', "/", trim($this->path, '/')) . '/';
            $this->command->info('Building model ' . $this->modelName . ' ...');
            $this->storage->put(($path . $this->modelName . '.php'), $this->BuildModel());
            $this->command->info('Model file is created and stored at ' . $path . $this->modelName . '.php');
        }
    }

    private function Wizard($banner=false){
        if($banner===true){
            $this->command->comment("=====Welcome to BluePrints Model building utility.======");
            $this->command->comment("This wizard will walk you through the steps to create a model file.\nLet's get started...");
        }
        while(empty($this->modelName)){
            $this->modelName= $this->command->ask('Model Name(without ".php"):');
            if(empty($this->modelName)){
                $this->command->error('Model Name is required.');
            }
        }
        $this->tableName = $this->command->ask('Table Name( "'.(strtolower($this->modelName)).'s" will be used if left empty):');
        if (empty($this->tableName)) {
            $this->tableName=strtolower($this->modelName).'s';
            $this->command->info($this->tableName.'is used as table name.');
        }
        $In_DB= $this->command->confirm('Is the table already defined/created in database?');
        $this->PrimaryKey = $this->command->ask('What is the primary key name:');
        if ($this->command->confirm($In_DB?'Define a fillable list?(Only fields in the list are editable)': 'Define a field list?')){
            while(true){
                $field=[];
                $input=$this->command->ask('Field name:(Press "Enter" to exit)');
                if(empty($input)) break 1;
                $field['name']= $input;
                $input = $this->command->ask('Field type:(Format: [dataType(option)] Example: [string(100),integer].Press "Enter" to exit)');
                if (empty($input)) break 1;
                preg_match('/\((.*?)\)/i', $input, $option);
                if (empty($option)) {
                    $field['dataType'] = $input;
                } else {
                    $field['dataType'] = str_replace('(' . $option[1] . ')', '', $input);
                    if (in_array($field['dataType'], ['string', 'char'])) {
                        $field['size'] = $option[1];
                    }
                }
                
            }
        }
        if ($banner === true) {
            $this->command->comment("=====Thank you for using BluePrint Wizard.======");
        }
    }

    private function BuildModel(){
        return '<?php
namespace '. join(
        '\\',
        array_map(
            function ($word) {
                return ucwords($word);
            },
            explode('/', trim($this->path, '/'))
        )
    ). ';

    use Illuminate\Database\Eloquent\Model;

    class '. $this->modelName.' extends Model
    {

        protected $table = '. $this->tableName.';
        '.(empty($this->PrimaryKey)?'': ('protected $primaryKey = ' . $this->PrimaryKey)).';
        '.(empty($this->Guarded)?'':('protected $guarded = ['.array_map(function($guardedField){return "'$guardedField'";}, $this->Guarded).'];')).'
                
    }
?>
        ';
    }
}
