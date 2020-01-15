<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardbase;
use feiron\felaraframe\lib\BluePrints\BluePrintsModelFactory;
use Illuminate\Support\Facades\Artisan;
use Exception;
class bp_wizardMakeMigration extends bp_wizardbase
{
    protected $command;
    protected $storage;
    private $tableName;
    private $FieldList;
    private $Modifers;
    private $PrimaryKey;
    private $options;

    private $dataTypes;
    private const OPTIONLIST=[
        'unsingn'=>['bigInteger', 'decimal', 'double', 'float', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'],
        'autoIncrement'=>['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'],
        'NonDefault' => ['uuid', 'tinyIncrements', 'smallIncrements', 'rememberToken', 'mediumIncrements', 'increments', 'bigIncrements'],
        'hasOption'=>[
            'char' => ['size' => 100],
            'string' => ['size' => 100],
            'decimal' => ['Decimal Points(Seperated by comma ",".)' => '8,2'],
            'unsignedDecimal' => ['Decimal Points(Seperated by comma ",".)' => '8,2'],
            'double' => ['Decimal Points(Seperated by comma ",". Default: 8,2)' => '8,2'],
            'float' => ['Decimal Points(Seperated by comma ",".)' => '8,2'],
            'enum' => ['List Of Values(Seperate each with comma ",")' => ''],
            'set' => ['List Of Values(Seperate each with comma ",")' => '']
        ]
    ];
    public function __construct($Command){
        parent::__construct($Command);
        $this->tableName=$this->command->argument('name')??null;
        $this->PrimaryKey=null;
        $this->FieldList = [];
        $this->Modifers=[
            'autoIncrement'=>'Integer type auto-increment',
            'nullable' => 'Allows (by default) NULL values to be inserted into the column',
            'unsigned' => 'Set INTEGER columns as UNSIGNED (MySQL)',
            'useCurrent' => 'Set TIMESTAMP columns to use CURRENT_TIMESTAMP as default value',
            'primary'=>'Set as Primary Key',
            'editable' => 'Make field available for mass assignables'
        ];
        $this->dataTypes=[
            'increments',
            'bigIncrements',
            'mediumIncrements',
            'smallIncrements',
            'tinyIncrements',
            'integer',
            'mediumInteger',
            'bigInteger',
            'smallInteger',
            'tinyInteger',
            'unsignedBigInteger',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
            'binary',
            'boolean',
            'char'=>'option (Size). example => name:char(100)',
            'date',
            'dateTime',
            'dateTimeTz',
            'decimal' => 'option (total digits,decimal digits). example => number:decimal(8,2)',
            'double' => 'option (total digits,decimal digits). example => number:double(8,2)',
            'enum' => 'option (value list seperated by comma ","). example => enums:enum(a,b,c,1,2,3)',
            'float' => 'option (total digits,decimal digits). example => number:float(8,2)',
            'geometry',
            'geometryCollection',
            'ipAddress',
            'json',
            'jsonb',
            'lineString',
            'longText',
            'macAddress',
            'mediumText',
            'morphs',
            'uuidMorphs',
            'multiLineString',
            'multiPoint',
            'multiPolygon',
            'nullableMorphs',
            'nullableUuidMorphs',
            'point',
            'polygon',
            'rememberToken',
            'set' => 'option (value list seperated by comma ","). example => sets:set(a,b,c,1,2,3)',
            'string' => 'option (Size). example => name:string(100)',
            'text',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'timestamps',
            'timestampsTz',
            'unsignedDecimal' => 'option (total digits,decimal digits). example => number:unsignedDecimal(8,2)',
            'uuid',
            'year'
        ];
        $this->options=[
            'engine' => $this->command->option('engine') ?? 'InnoDB',
            'charset' => $this->command->option('charset') ?? 'utf8',
            'collation' => $this->command->option('collation') ?? 'utf8_unicode_ci',
            'timestamps' => ($this->command->option('timestamps') === true),
            'migrate'=>false
        ];

    }

    public function Build(){

        if($this->command->option('types')??false){ //List all available types
            foreach($this->dataTypes as $type=>$info){
                if(is_numeric($type)){
                    $this->command->comment($info);
                }else{
                    $this->command->comment($type.':'.$info);
                }
            }
            return false;
        }

        if ($this->command->option('modifiers') ?? false) { //List all available modifiers
            $this->command->info('You can seperate each modifiers with comma ","');
            foreach ($this->Modifers as $type => $info) {
                $this->command->comment($type.': '.$info);
            }
            return false;
        }

        if (empty($this->tableName) && ($this->command->option('wizard') === false)) {
            $this->command->error('Model name is required or using flag -W to use wizard.');
        }else{
            $this->command->info('Building migration for ' . $this->tableName . ' ...');
            $ModelDefinition = [
                'modelName' => ucfirst($this->tableName),
                'engine' => $this->options['engine'],
                'charset' => $this->options['charset'],
                'collation' => $this->options['collation'],
                'withTimeStamps' => ($this->options['timestamps'] === true)
            ];
            $factory= new BluePrintsModelFactory($ModelDefinition);

            if($this->command->option('wizard') !== false){
                $this->Wizard(true,$factory);
            }else{
                if (!empty($this->command->option('datafields'))) {
                    foreach ((explode(';', $this->command->option('datafields')) ?? []) as $field) {
                        preg_match('/(.*):(\w*)(?:\((.*)\))?(?:\[(.*)\])?(?:\<(.*)\>)?/i', $field, $fieldDef);
                        $Definition = [ //Process name and type
                            "name" => $fieldDef[1],
                            "dataType" => $fieldDef[2]
                        ];

                        if (!empty($fieldDef[3])) { //Process options
                            if (stripos(trim($fieldDef[3], ','), ',') === false) {
                                $Definition['size'] = $fieldDef[3];
                            } elseif (in_array($fieldDef[2], ['set', 'enum']) === true) {
                                $Definition['modifier'] = explode(',', trim($fieldDef[3], ','));
                            } else {
                                $Definition['modifier'] = $fieldDef[3];
                            }
                        }

                        if (!empty($fieldDef[4])) { //Process Modifiers
                            foreach (explode(',', trim($fieldDef[4], ',')) ?? [] as $modifier) {
                                if ((stripos($modifier, '=') === false)) {
                                    $Definition[$modifier] = true;
                                    if ($modifier == 'primary') {
                                        $this->PrimaryKey = $fieldDef[1];
                                    }
                                } else {
                                    $modifier = explode('=', $modifier);
                                    $Definition[$modifier[0]] = $modifier[1];
                                }
                            }
                        }

                        if (!empty($fieldDef[5])) {
                            $Definition['default'] = $fieldDef[5];
                        }
                        $Definition['nullable'] = $Definition['nullable'] ?? false;
                        $factory->addField((object) $Definition);
                    }
                }
            }    

            try {

                $factory->buildMigrations();
                $this->command->info('Migration file is created and stored at /database/migrations/');
                if(($this->options['migrate']??false)===true){
                    $this->command->info('Now migrating to database ...');
                    Artisan::call('migrate');
                    $this->command->info("\nMigration completed.");
                }
            } catch (Exception $e) {
                $this->command->error($e->getMessage());
            }
            
        }
    }

    public function Wizard($banner=false, BluePrintsModelFactory &$factory){
        if($banner===true){
            $this->command->comment("=====Welcome to BluePrints Migration building utility.======");
            $this->command->comment("This wizard will walk you through the steps to create a migration file.\nLet's get started...");
        }
        while(empty($this->tableName)){
            $this->tableName= $this->command->ask('What is the Table Name:');
            if(empty($this->tableName)){
                $this->command->error('Table Name is required.');
            }else{
                $factory->SetModelDefinition('modelName', $this->tableName);
            }
        }
        $factory->SetModelDefinition('engine', $this->command->ask('Table Engine ("Enter" to skip and use InnoDB):') ?? 'InnoDB');
        $factory->SetModelDefinition('charset', $this->command->ask('Table Charset ("Enter" to skip and use utf8):') ?? 'utf8');
        $factory->SetModelDefinition('collation', $this->command->ask('Table Charset ("Enter" to skip and use utf8_unicode_ci):') ?? 'utf8_unicode_ci');
        $factory->SetModelDefinition('withTimeStamps', $this->command->confirm('Include timestamps?') ?? false);

        // $this->options['engine'] = $this->command->ask('Table Engine ("Enter" to skip and use InnoDB):')??'InnoDB';
        // $this->options['charset'] = $this->command->ask('Table Charset ("Enter" to skip and use utf8):')?? 'utf8';
        // $this->options['collation'] = $this->command->ask('Table Charset ("Enter" to skip and use utf8_unicode_ci):')?? 'utf8_unicode_ci';
        // $this->options['timestamps'] = $this->command->confirm('Include timestamps?');
        $this->options['migrate']= $this->command->confirm('Perform migration to database at the end?');
        $this->command->info("Adding table fields now...");

        while (true) {//Table Definition Section--------------------
            $Definition = [];
            $Definition['name'] = $this->command->ask('Field name (Enter empty to skip):');
            if(empty($Definition['name'])) break 1;
            if (empty($this->PrimaryKey)) {
                if ($this->command->confirm('Primary Key?') ?? false) {
                    $this->PrimaryKey = $Definition['name'];
                    $Definition['dataType']= 'bigIncrements';
                    $Definition['primary']=true;
                    $Definition['nullable']=false;
                    $factory->addField((object) $Definition);
                    continue;
                }
            }
            $Definition['dataType'] = $this->command->ask('Data Type (default:string):')??'string';

            if(false!==($this->command->confirm('Nullable?') ?? false)){
                $Definition['nullable'] = true;
            }            

            if(in_array($Definition['dataType'],self::OPTIONLIST['unsingn'])){
                if (false !== ($this->command->confirm('Unsigned?') ?? false)) {
                    $Definition['unsigned'] = true;
                }  
            }
            if (in_array($Definition['dataType'], self::OPTIONLIST['autoIncrement'])) {
                if (false !== ($this->command->confirm('AutoIncrement?') ?? false)) {
                    $Definition['autoIncrement'] = true;
                }  
            }

            if (array_key_exists($Definition['dataType'], self::OPTIONLIST['hasOption'])) {
                foreach(self::OPTIONLIST['hasOption'][$Definition['dataType']] as $option=>$default){
                    if (in_array($Definition['dataType'], ['char', 'string'])) {
                        $Definition['size'] = $this->command->ask(ucfirst($option) . '(Default:' . $default . '):') ?? $default;
                    }else{
                        $Definition['modifier'] = $this->command->ask(ucfirst($option) . '(Default: ' . $default . ' ):') ?? $default;
                    }
                    if (in_array($Definition['dataType'], ['set', 'enum'])) {
                        $Definition['modifier'] =explode(',', $Definition['modifier']);
                    }
                }
            }
            if (!in_array($Definition['dataType'], self::OPTIONLIST['NonDefault'])) {
                $default= $this->command->ask('Default value? (boolean use 1,0):');
                if(!empty($default)){
                    $Definition['default'] =  $default;
                }
            }
            $factory->addField((object) $Definition);
        }
        if ($banner === true) {
            $this->command->comment("=====Thank you for using BluePrint Wizard.======");
        }
    }
}
