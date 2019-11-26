<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;

class BluePrintsModelFactory {
    private $ModelDefinition;
    private $FieldList;
    private $myRelations;
    private $RelatedModels;
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

    private const ModelClassPrefix= 'fe_bp_';
    private const migrationPath = "database/migrations/";
    private const modelPath = "app/model/";

    public function __construct($definition=null){
        $this->FieldList=[];
        $this->myRelations=[];
        $this->RelatedModels=[];
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

    private function getRelationModifier($relation,$reverse=false){
        switch($relation->type){
            case "OneToOne":
                return ($reverse? "belongsTo": 'hasOne') . ('("App\model\\' . self::ModelClassPrefix.$relation->target . '","' . $relation->targetReference . '","' . $relation->sourceReference . '" )');
                break;
            case "OneToMany":
                return ($reverse ? "belongsTo" : "hasMany") . ('("App\model\\' . self::ModelClassPrefix . $relation->target . '","' . $relation->targetReference . '","' . $relation->sourceReference . '" )');
                break;
            case "ManyToOne":
                return ($reverse ? "hasMany" : "belongsTo") . ('("App\model\\' . self::ModelClassPrefix . $relation->target . '","' . $relation->targetReference . '","' . $relation->sourceReference . '" )');
                break;
            case "ManyToMany":
                $tableName = [];
                array_push($tableName, $this->ModelDefinition['modelName'], $relation->target);
                sort($tableName);
                $tableName = 'MtoM_' . join('_', $tableName);
                return "belongsToMany('App\model\\" . self::ModelClassPrefix . $relation->target."', '$tableName', '" . $relation->sourceReference . "','" . $relation->target.'_'.$relation->targetReference . "')";
                break;
        }
        return false;
    }

    public function addField($definition){
        if (null===($this->PrimaryKey) && ($definition->dataType == 'bigIncrements' || (isset($definition->primary) && true === $definition->primary))) {
            $this->SetPrimary($definition->name);
        }
        $this->FieldList[$definition->name]= array_merge(self::Defaults, (array) $definition);
    }

    public function addRelation($relation){
        array_push($this->myRelations,$relation);
        array_push($this->RelatedModels, $relation->target);
    }

    public function getPrimary(){
        return $this->PrimaryKey;
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

    public function BuildModel(){
        $className = self::ModelClassPrefix . $this->ModelDefinition['modelName'];
        $target = self::modelPath . $className . '.php';
        $this->PrimaryKey=($this->PrimaryKey ?? 'idx');
        $guarded = [$this->PrimaryKey];
        $hidden = [];
        $relations = "";
        foreach ($this->FieldList as $field) {
            if (($field['visible'] ?? true) == false) {
                if (in_array($field['name'], $hidden) === false)
                    array_push($hidden, $field['name']);
            }
            if (($field['editable'] ?? true) == false) {
                if (in_array($field['name'], $guarded) === false)
                    array_push($guarded, $field['name']);
            }
        }

        foreach ($this->myRelations as $relation) {
            $modifier = $this->getRelationModifier($relation);
            if (false !== $modifier) {
                $relations .= '
                        public function ' . $relation->target . 's()
                        {
                            return $this->' . $modifier . ';
                        }
                    ';
            }
        }
        $contents = '<?php
        namespace App\model;

        use Illuminate\Database\Eloquent\Model;

        class ' . $className . ' extends Model
        {
            protected $table = "' . $this->ModelDefinition['modelName'] . '";
            protected $primaryKey = "' . $this->PrimaryKey . '";
            ' . (($this->ModelDefinition['withTimeStamps'] ?? false) ? "" : 'public $timestamps = false;') . '
            ' . (!empty($guarded) ? ('protected $guarded = [' . join(',', array_map(function ($g) { return ("'" . $g . "'"); }, $guarded)) . '];') : "") . '
            ' . (!empty($hidden) ? ('protected $hidden = [' . join(',', array_map(function ($h) { return ("'" . $h . "'"); }, $hidden)) . '];') : "") . '
            '. $relations.'
        }
        ';
        $this->RootStorage->put($target, $contents);
    }

    public function getFieldNames(){
        return array_map(function($f){return (object)['name'=> $f,''];}, array_keys($this->FieldList));
    }

    public function isRelatedTo($modelName){
        return in_array($modelName,$this->RelatedModels);
    }
}

?>