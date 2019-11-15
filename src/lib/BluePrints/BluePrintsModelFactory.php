<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;

class BluePrintsModelFactory {
    private $ModelDefinition;
    private $FieldList;
    private $myRelations;
    private $PrimaryKey;
    private $RootStorage;
    private const FieldsWithSize = [
        'char',
        'string'
    ];
    private const FieldsWithModifier = [
        'decimal',
        'double',
        'float',
        'unsignedDecimal'
    ];
    private const FieldsWithCollection = [
        'enum',
        'set'
    ];
    private const Defaults =[
        'dataType' => 'string',
        'size' => '175',
        'default' => null,
        'nullable' => true,
        'autoIncrement' => false,
        'visible' => true,
        'editable' => true
    ];

    private const migrationPath = "database/migrations/";
    private const modelPath = "app/model/";

    public function __construct($definition=null){
        $this->FieldList=[];
        $this->myRelations=[];
        $this->PrimaryKey=null;
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
        $ModelDefinition=[
            'modelName'=>'',
            'engine'=> 'InnoDB',
            'charset'=> 'utf8',
            'collation'=> 'utf8_unicode_ci',
            'withTimeStamps'=>true,
            'withCRUD'=>false
        ];
        $this->ModelDefinition= array_merge($ModelDefinition, ($definition??[]));
        if (array_key_exists('index', $this->ModelDefinition)) {
            $this->SetPrimary($this->ModelDefinition['index']);
        }
    }

    public function getModelName(){
        return $this->ModelDefinition['modelName'];
    }
    
    public function getRelations(){
        return $this->myRelations;
    }

    public function addField($definition){
        if (null===($this->PrimaryKey) && ($definition->dataType == 'bigIncrements' || (isset($definition->primary) && true === $definition->primary))) {
            $this->SetPrimary($definition->name);
        }
        $this->FieldList[$definition->name]= array_merge(self::Defaults, (array) $definition);
    }

    public function addRelation($relation){
        array_push($this->myRelations,$relation);
    }

    private function SetPrimary($keyName){
        $this->PrimaryKey=$keyName;
    }
    private function getFieldModifier($field){

        if(in_array($field['dataType'], self::FieldsWithSize)){
            return ("," . ($field['size'] ?? self::Defaults['size']));
        } elseif(in_array($field['dataType'], self::FieldsWithModifier)){
            return ("," . ($field['modifier'] ?? '8,2'));
        } elseif (in_array($field['dataType'], self::FieldsWithCollection)) {
            $field['modifier']=array_map(function($f){return ("'". $f."'");}, ($field['modifier']??['']));
            return (",[" . (join(',',$field['modifier'])).']');
        }
        return "";
    }

    private function createDBField($field,$namePrefix='',$skipIndex=false, $forceType = false){
        if(false!==$forceType){
            $field['dataType']= $forceType;
        }
        return '
            $table->' . ($field['dataType'] ?? self::Defaults['dataType']) . '("'.$namePrefix.$field['name'].'"' . $this->getFieldModifier($field) . ')'
            . ((isset($field['nullable']) && false === $field['nullable']) ? "->nullable(false)" : "->nullable(true)")
            . ($skipIndex === false && isset($field['autoIncrement']) && true === $field['autoIncrement'] ? "->autoIncrement()" : "")
            . (isset($field['unsigned']) && true === $field['unsigned'] ? "->unsigned()" : "")
            . (false === empty($field['default']) ? ("->default(".$field['default'].")") : "")
            . (false === empty($field['charset']) ? ("->charset(".$field['charset'].")") : "")
            . (false === empty($field['collation']) ?("->collation(".$field['collation'].")") : "")
            . ($skipIndex === false && isset($field['unique']) && true === $field['unique'] ? "->unique()" : "")
            . ($skipIndex === false && isset($field['index']) && true === $field['index'] ? "->index()" : "")
            . ($skipIndex === false && isset($field['spatialIndex']) && true === $field['spatialIndex'] ? "->spatialIndex()" : "")
            . ($skipIndex === false && isset($field['primary']) &&  true === $field['primary'] && $field['dataType'] != 'bigIncrements' ? "->primary()" : "")
            . ';';
    }

    public function renderDBField($fieldName,$prefix='', $skipIndex = false,$forceType=false){
        if(array_key_exists($fieldName, $this->FieldList)){
            return $this->createDBField($this->FieldList[$fieldName], $prefix, $skipIndex);
        }
        return '';
    }

    public function buildMigrations(){
        $className = 'create_' . $this->ModelDefinition['modelName'] . '_table';
        $target = self::migrationPath . 'fe_blueprint_migration_file_' . $className . '.php';

        try {
            if ($this->RootStorage->exists($target) === false) {
                $fieldList = "";

                if (array_key_exists('index',$this->ModelDefinition)) {
                    $fieldList .= '
                    $table->bigIncrements("' . $this->ModelDefinition['index'] . '");
                    ';
                }
                foreach ($this->FieldList as $fieldName=>$field) {

                    $fieldList .= $this->createDBField($field);

                }

                if ($this->PrimaryKey == null) {
                    $fieldList = '
                    $table->bigIncrements("idx");
                    ' . $fieldList;
                    $this->PrimaryKey = 'idx';
                }
                if (($this->ModelDefinition['withTimeStamps'] ?? false) === true) {
                    $fieldList .= '
                    $table->timestamps();';
                }
                if (strlen($fieldList) > 0) {
                    $contents = '
                        <?php
                
                        use Illuminate\Database\Migrations\Migration;
                        use Illuminate\Database\Schema\Blueprint;
                        use Illuminate\Support\Facades\Schema;
                
                        class ' . str_replace('_', '', $className) . ' extends Migration
                        {
                            public function up()
                            {
                                if(false===Schema::hasTable("' . $this->ModelDefinition['modelName'] . '")){
                                    Schema::create("' . $this->ModelDefinition['modelName'] . '", function (Blueprint $table) {
                                        $table->engine = "' . ($this->ModelDefinition['engine'] ?? 'InnoDB') . '";
                                        $table->charset = "' . ($this->ModelDefinition['charset'] ?? 'utf8') . '";
                                        $table->collation = "' . ($this->ModelDefinition['collation'] ?? 'utf8_unicode_ci') . '";
                                        ' . $fieldList . '
                                    });
                                }
                            }
                
                            public function down()
                            {
                                Schema::dropIfExists("' . $this->ModelDefinition['modelName'] . '");
                            }
                        }
                        ?>';
                    $this->RootStorage->put($target, $contents);
                    
                } else {
                    throw new Exception("Model contains no fields definition [".$this->ModelDefinition['modelName']."]");
                }
            }
        } catch (Exception $e) {
            throw new Exception("Error Creating Migrations" . $e->getMessage(), 1);
        }
    }

    public function getFieldDefinition($fieldName){
        return $this->FieldList[$fieldName]??[];
    }

    public function getModelDefition($definitionName){
        return $this->ModelDefinition[$definitionName]??'';
    }

}

?>