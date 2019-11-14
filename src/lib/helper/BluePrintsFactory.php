<?php

namespace feiron\felaraframe\lib\helper;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
class BluePrintsFactory {

    private $blueprint;
    private $projectPath;
    private $BlueprintStorage;
    private $RootStorage;
    private $command;
    private $relations;
    private $reverseRelations;
    private $MtoMRelations;
    private const Defaults = [
        "model"=>[
            'dataType' => 'string',
            'size' => '175',
            'default' => null,
            'nullable' => true,
            'autoIncrement' => false,
            'withTimeStamps' => true,
            'unsigned' => false,
            'visible' => true,
            'editable' => true,
            'engine'=>'InnoDB',
            'charset_table'=>'utf8',
            'charset_field'=>'utf8',
            'collation'=>'utf8_unicode_ci',
            'modifier' =>'8,2'
        ]
    ];
    private const FieldsWithSize=[
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
    private const migrationPath="database/migrations/";

    public function __construct($target,$storage,$command){
        $this->BlueprintStorage=$storage;
        $this->command= $command;
        $this->relations=[];
        $this->reverseRelations = [];
        $this->MtoMRelations=[];
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
        $this->projectPath = str_replace($this->BlueprintStorage->getAdapter()->getPathPrefix(), '', dirname($this->BlueprintStorage->path($target)));
        try{
            $this->blueprint = json_decode($this->BlueprintStorage->get($target));
        }catch(Exception $e){
            throw new Exception("Error Processing blueprint file. Please make sure it's in a correct format.", 1);
        }
    }

    public function buildModels(){
        if(false=== $this->RootStorage->exists('app/model')){
            $this->RootStorage->makeDirectory('app/model');
        }

        $modelFiles= preg_grep('/^.*\.(mbp)$/i',$this->BlueprintStorage->files($this->projectPath.'/models'));

        if(empty($modelFiles)){
            $this->command->info("There are no model files in the sub direcotry [models]");
        }else{
            foreach($modelFiles as $model){
                $m =json_decode($this->BlueprintStorage->get($model));
                foreach ($m as $model) {
                    if (isset($model->modelName)) {
                        $this->createMigration($model);
                        $this->createModel($model);
                    }
                }  
            }
            if(!empty($this->relationList)){
                $this->createRelationMigration();
            }

            $this->command->info('-->Now Migrating database to the server...');
            try {
                // Artisan::call('migrate');
            } catch (Exception $e) {
                throw $e;
            }
            
        }
    }

    private function getRelationModifier($relation,$reverse=false){
        switch($relation->type){
            case "OneToOne":
                return ($reverse? "belongsTo": 'hasOne') . ('("App\model\\' . $relation->target . '","' . $relation->targetReference . '","' . $relation->sourcekey . '" )');
                break;
            case "OneToMany":
                return ($reverse ? "belongsTo" : "hasMany") . ('("App\model\\' . $relation->target . '","' . $relation->targetReference . '","' . $relation->sourcekey . '" )');
                break;
            case "ManyToOne":
                return ($reverse ? "hasMany" : "belongsTo") . ('("App\model\\' . $relation->target . '","' . $relation->targetReference . '","' . $relation->sourcekey . '" )');
                break;
            case "ManyToMany":
                return "belongsToMany";
                break;
        }
        return false;
    }

    private function createModel($model){

        $className = 'fe_bp_' . $model->modelName;
        $target = 'app/model/' . $className . '.php';
        $guarded=[$model->primaryKey];
        $hidden=[];
        $relations="";
        foreach($model->modelFields as $field){
            if(($field->visible??true)==false){
                if (in_array($field->name, $hidden) === false) 
                    array_push($hidden, $field->name);
            }
            if (($field->editable ?? true) == false) {
                if(in_array($field->name,$guarded)===false) 
                    array_push($guarded, $field->name);
            }
            if(isset($field->relation) && array_key_exists('target', $field->relation) && array_key_exists('type', $field->relation)){
                $field->relation->source= $model->modelName;
                $field->relation->sourcekey = $field->name;
                $modifier= $this->getRelationModifier($field->relation);
                if(false!== $modifier){
                    $relations.='
                        public function '.$field->relation->target.'s()
                        {
                            return $this->'. $modifier.'
                        }
                    ';
                }
                // if (array_key_exists($model->modelName, $this->reverseRelations)) {
                //     $modifier = $this->getRelationModifier($reverseRelations[$model->modelName],true);
                //     if (false !== $modifier) { 
                //         $relations .= '
                //                 public function ' . $reverseRelations[$model->modelName]['target'] . 's()
                //                 {
                //                     return $this->' . $modifier . '
                //                 }
                //             ';
                //     }
                // }
            }
        }
$contents= '<?php
namespace App\model;

use Illuminate\Database\Eloquent\Model;

class '. $className.' extends Model
{
    protected $table = "'. $model->modelName. '";
    protected $primaryKey = "' . $model->primaryKey .'";
    '.(($model->withTimeStamps??false)?"": 'public $timestamps = false;').'
    '.(!empty($guarded)?('protected $guarded = ['. join(',',array_map(function($g){return ("'".$g."'"); },$guarded)).'];'):"").'
    '.(!empty($hidden)?('protected $hidden = ['. join(',',array_map(function($h){return ("'".$h."'"); }, $hidden)).'];'):"").'
}
';
        $this->RootStorage->put($target, $contents);
        $this->command->line("Model File created for " . $model->modelName);
    }

    private function getFieldModifier($field){

        if(in_array($field->dataType, self::FieldsWithSize)){
            return ("," . ($field->size ?? self::Defaults['model']['size']));
        } elseif(in_array($field->dataType, self::FieldsWithModifier)){
            return ("," . ($field->modifier ?? self::Defaults['model']['modifier']));
        } elseif (in_array($field->dataType, self::FieldsWithCollection)) {
            $field->modifier=array_map(function($f){return ("'". $f."'");}, ($field->modifier??['']));
            return (",[" . (join(',',$field->modifier)).']');
        }
        return "";
    }

    private function createRelationMigration(){
        $className = 'create_Relations_table';
        $target = self::migrationPath . 'fe_blueprint_migration_file_' . $className . '.php';
        if ($this->RootStorage->exists($target) === false) 
        {
            try {
                    $relationList = '';
                    $dropList = '';
                    foreach ($this->relationList as $model => $field) {
                    
                        $relationList .= '
                            if(false!==Schema::hasTable("' . $model . '")){
                                Schema::table("' . $model . '", function (Blueprint $table) {
                                    $table->foreign("' . $field->name . '") 
                                            ->references("' . $field->relation->targetReference . '")
                                            ->on("' . $field->relation->target . '")' . (isset($field->relation->onDelete) ? ('->onDelete("' . $field->relation->onDelete . '")') : '') . ';
                                        
                                });
                            }
                        ';
                        $dropList .= '
                            if(false!==Schema::hasTable("' . $model . '")){
                                Schema::table("' . $model . '", function (Blueprint $table) {
                                    $table->dropForeign(["' . $field->name . '"]);
                                });
                            }
                        ';
                        
                        if($field->relation->type == 'ManyToMany'){
                            $tableName=[];
                            array_push($tableName, $model);
                            array_push($tableName, $field->relation->target);
                            sort($tableName);
                            $foriegnField= clone $field;
                            $foriegnField->name= $field->relation->targetReference;
                            $tableName= 'MtoM_'.join('_', $tableName);
                            $relationList.='
                                if(false===Schema::hasTable("' . $tableName . '")){
                                    Schema::create("' . $tableName . '", function (Blueprint $table) {
                                        $table->engine = "' . ($field->DBEngin ?? self::Defaults['model']['engine']) . '";
                                        $table->charset = "' . ($field->DBCharset ?? self::Defaults['model']['charset_table']) . '";
                                        $table->collation = "' . ($field->DBCollation ?? self::Defaults['model']['collation']) . '";
                                        $table->bigIncrements("id");
                                        ' . $this->createDBField($field, $model.'_',true) . '
                                        ' . $this->createDBField($foriegnField, $field->relation->target.'_', true) . '
                                        
                                        $table->foreign("' . $model . '_' . $field->name . '") 
                                                ->references("' . $field->name . '")
                                                ->on("' . $model . '")->onDelete("cascade");

                                        $table->foreign("' . $field->relation->target . '_' . $foriegnField->name . '") 
                                                ->references("' . $foriegnField->name . '")
                                                ->on("' . $field->relation->target . '")->onDelete("cascade");    
                                    });
                                }
                            ';
                            $dropList .= '
                                Schema::dropIfExists("' . $tableName . '");
                            ';
                            $this->MtoMRelations[$model]= $tableName;
                            $this->MtoMRelations[$field->relation->target]= $tableName;
                        }
                    }
                    $contents = '
                <?php
        
                use Illuminate\Database\Migrations\Migration;
                use Illuminate\Database\Schema\Blueprint;
                use Illuminate\Support\Facades\Schema;
        
                class ' . str_replace('_', '', $className) . ' extends Migration
                {
                    public function up()
                    {
                        ' . $relationList . '
                    }
        
                    public function down()
                    {
                        ' . $dropList . '
                    }
                }
                ?>';
                    $this->RootStorage->put($target, $contents);
            } catch (Exception $e) {
                throw new Exception("Error Creating Migration Relations " . $e->getMessage(), 1);
            }
        }
        $this->command->line("Migration created for table relations. ");
    }

    private function createDBField($field,$namePrefix='',$skipIndex=false){
        return '
            $table->' . ($field->dataType ?? self::Defaults['model']['dataType']) . '("' . $namePrefix.$field->name . '"' . $this->getFieldModifier($field) . ')'
            . ((isset($field->nullable) && false === $field->nullable) ? "->nullable(false)" : "->nullable(true)")
            . ($skipIndex === false && isset($field->autoIncrement) && true === $field->autoIncrement ? "->autoIncrement()" : "")
            . (isset($field->unsigned) && true === $field->unsigned ? "->unsigned()" : "")
            . (false === empty($field->default) ? "->default($field->default)" : "")
            . (false === empty($field->charset) ? "->charset($field->charset)" : "")
            . (false === empty($field->collation) ? "->collation($field->collation)" : "")
            . ($skipIndex === false && isset($field->unique) && true === $field->unique ? "->unique()" : "")
            . ($skipIndex === false && isset($field->index) && true === $field->index ? "->index()" : "")
            . ($skipIndex === false && isset($field->spatialIndex) && true === $field->spatialIndex ? "->spatialIndex()" : "")
            . ($skipIndex === false && isset($field->primary) &&  true === $field->primary && $field->dataType != 'bigIncrements' ? "->primary()" : "")
            . ';';
    }

    private function createMigration(&$model){
        $className = 'create_' . $model->modelName . '_table';
        $target = self::migrationPath . 'fe_blueprint_migration_file_' . $className . '.php';
        try {
            if($this->RootStorage->exists($target)===false)
            {
                $fieldList="";
                $hasPrimary=false;
                if(!empty($model->index)){
                    $fieldList.= '$table->bigIncrements("'. $model->index.'");';
                    $model->primaryKey= $model->index;
                    $hasPrimary=true;
                }
                foreach($model->modelFields as $field){

                    $fieldList.=$this->createDBField($field);

                    if ($hasPrimary == false && ($field->dataType == 'bigIncrements' || (isset($field->primary) && true === $field->primary))) {
                        $model->primaryKey = $field->name;
                        $hasPrimary = true;
                    }

                    if(isset($field->relation) && array_key_exists('target', $field->relation) && array_key_exists('targetReference', $field->relation)){
                        $this->relationList[$model->modelName]= $field;
                        $this->relationList[$model->modelName]->sourceKey= $model->primaryKey;
                        $this->relationList[$model->modelName]->DBEngin = ($model->engine ?? self::Defaults['model']['engine']);
                        $this->relationList[$model->modelName]->DBCharset = ($model->charset ?? self::Defaults['model']['charset_table']);
                        $this->relationList[$model->modelName]->DBCollation = ($model->collation ?? self::Defaults['model']['collation']);
                        if(array_key_exists('type', $field->relation)){
                            $this->reverseRelations[$field->relation->target]=['target'=> $model->modelName,'type'=> $field->relation->type];
                        }
                    }
                }
                if($hasPrimary==false){
                    $fieldList = '
                    $table->bigIncrements("idx");
                    '. $fieldList;
                    $model->primaryKey = 'idx';
                    $hasPrimary = true;
                }
                if (($model->withTimeStamps??false)===true) {
                    $fieldList .= '
                    $table->timestamps();';
                }
                if(strlen($fieldList)>0){
                    $contents='
            <?php
    
            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;
    
            class '.str_replace('_','',$className).' extends Migration
            {
                public function up()
                {
                    if(false===Schema::hasTable("'.$model->modelName.'")){
                        Schema::create("'.$model->modelName.'", function (Blueprint $table) {
                            $table->engine = "'.($model->engine??self::Defaults['model']['engine']).'";
                            $table->charset = "'.($model->charset??self::Defaults['model']['charset_table']).'";
                            $table->collation = "'.($model->collation??self::Defaults['model']['collation']).'";
                            '.$fieldList.'
                        });
                    }
                }
    
                public function down()
                {
                    Schema::dropIfExists("'.$model->modelName.'");
                }
            }
            ?>';
                    $this->RootStorage->put($target,$contents);
                }else{
                    throw new Exception("Model contains no fields definition [$model->modelName]");
                }
            }
        } catch (Exception $e) {
            throw new Exception("Error Creating Migrations". $e->getMessage(), 1);
        }
        $this->command->line("Migration created for " . $model->modelName);
    }

    public function buildPageTemplate(){
        $path='resources/views/';
        $contents= "
            @extends('felaraframe::page')

            " . ((!empty($this->blueprint->siteName) ? ("
            @php
            config(['app.name'=>\"" . $this->blueprint->siteName . "\"]);
            @endphp
            ") : "")) .
            "

            " . ((!empty($this->blueprint->favIcon) ? ("
            @section('favicon')
            " . ($this->blueprint->favIcon) . "
            @endsection
            ") : "")) .
            "

            @section('user_name')
            {{Auth::user()->name}}
            @endsection

            ".((!empty($this->blueprint->siteTitle)?("
            @section('title')
            " . $this->blueprint->siteTitle . "
            @endsection
            "):"")).
            "

            " . ((!empty($this->blueprint->siteAuthor) ? ("
            @section('site_author')
            " . $this->blueprint->siteAuthor . "
            @endsection
            ") : "")) .
            "
            
            " . ((!empty($this->blueprint->siteFooter) ? ("
            @section('footer')
            " . ($this->blueprint->siteFooter->footerText??"") . "
            @endsection
            ") : "")) .
            "
            
        ";

        try {
            $this->RootStorage->put($path."page.blade.php",$contents);
        } catch (Exception $e) {
            throw new Exception("Error creating page template.", 1);
            return false;
        }
        return true;
    }
}
?>