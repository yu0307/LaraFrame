<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use feiron\felaraframe\lib\BluePrints\BluePrintsModelFactory;
class BluePrintsFactory {

    private $blueprint; //stores the contents of the target blueprint.
    private $projectPath;
    private $BlueprintStorage;
    private $RootStorage;
    private $command;
    private $ModelList;
    private $relations;

    
    private const migrationPath="database/migrations/";

    public function __construct($target,$storage,$command){
        $this->BlueprintStorage=$storage;
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
        $this->command= $command;
        $this->projectPath = str_replace($this->BlueprintStorage->getAdapter()->getPathPrefix(), '', dirname($this->BlueprintStorage->path($target)));
        $this->ModelList=[];
        $this->relations=[];


        $this->reverseRelations = [];
        $this->MtoMRelations=[];
        try{
            $this->blueprint = json_decode($this->BlueprintStorage->get($target));
        }catch(Exception $e){
            throw new Exception("Error Processing blueprint file. Please make sure it's in a correct format.", 1);
        }
    }

    private function getInverseRelation($relationSource,$target,$targetReference){
        $relation = clone $relationSource;
        $relation->sourceReference = $relation->targetReference;
        $relation->target = $target;
        $relation->targetReference = $targetReference;
        if(isset($relation->onDelete)) 
            unset($relation->{'onDelete'});
        if(strcasecmp($relation->type, 'OneToMany')===0){
            $relation->type= 'ManyToOne';
        }elseif(strcasecmp($relation->type, 'ManyToOne') === 0){
            $relation->type = 'OneToMany';
        }
        return $relation;
    }

    public function processModels($model){
        $fieldDefinitions= $model->modelFields??[];
        unset($model->modelFields);
        $views = $model->view??[];
        unset($model->view);
        $MyModel= new BluePrintsModelFactory((array) $model);
        $this->ModelList[$model->modelName]=$MyModel;
        foreach($fieldDefinitions as $field){
            if(isset($field->relation)){
                $field->index=true;
                $field->relation->sourceReference = $field->name;
                $MyModel->addRelation($field->relation);//add relation defined in the blueprint

                //if target inverse model is already imported
                if(array_key_exists($field->relation->target, $this->ModelList)){
                    $this->ModelList[$field->relation->target]->addRelation($this->getInverseRelation($field->relation, $MyModel->getModelName(), $field->name));
                }else{//only add to relation list if it's not processed.
                    //add inverse relation for models imported later.
                    $this->relations[$field->relation->target] = $this->getInverseRelation($field->relation, $MyModel->getModelName(), $field->name);
                }
                //if current model has inverse relation
                if(array_key_exists($MyModel->getModelName(),$this->relations)){
                    $MyModel->addRelation($this->relations[$MyModel->getModelName()]);
                }
                unset($field->relation);
            }
            $MyModel->addField($field);
        }
    }

    public function ImportModels(){
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
                        $this->processModels($model);
                    }
                }  
            }
            $this->command->line("Model blueprints imported. Now generating migrations...");
            // dd($this->ModelList);
            $this->BuildMigrations();
            // dd($this->ModelList);
            dd();
            // $this->command->info('-->Now Migrating database to the server...');
            // try {
            //     // Artisan::call('migrate');
            // } catch (Exception $e) {
            //     throw $e;
            // }
            
        }
    }

    private function BuildMigrations(){
        $relations=[];
        foreach ($this->ModelList as $modelName => $model) {
            $model->buildMigrations();
            $relation=$model->getRelations();
            if(!empty($relation)){
                $relations[$modelName]=$relation;
            }
            $this->command->line("Migration created for " . $modelName);
        }
        $this->createRelationMigration($relations);
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

    private function createRelationMigration($models){
        $className = 'create_Relations_table';
        $target = self::migrationPath . 'fe_blueprint_migration_file_' . $className . '.php';
        if ($this->RootStorage->exists($target) === false) {
            try {
                $relationList = '';
                $dropList = '';
                $M2MList='';
                $M2MTables=[];
                foreach($models as $model=>$relations){
                    $relationString='';
                    $dropString='';
                    foreach($relations as $relation){
                        $relationString.= '
                        $table->foreign("' . $relation->sourceReference . '") 
                            ->references("' . $relation->targetReference . '")
                            ->on("' . $relation->target . '")'.
                            (isset($relation->onDelete) ? ('->onDelete("' . $relation->onDelete . '")') : '') . ';
                        ';
                        $dropString .= '
                            $table->dropForeign(["' . $relation->sourceReference . '"]);
                        ';
                        if($relation->type == 'ManyToMany'){
                            $tableName = [];
                            array_push($tableName, $model, $relation->target);
                            sort($tableName);
                            $tableName = 'MtoM_' . join('_', $tableName);
                            if(!in_array($tableName, $M2MTables)){
                                $M2MList .= '
                                if(false===Schema::hasTable("' . $tableName . '")){
                                        Schema::create("' . $tableName . '", function (Blueprint $table) {
                                            $table->engine = "' . ($this->ModelList[$model]->getModelDefition('engine') ?? 'InnoDB') . '";
                                            $table->charset = "' . ($this->ModelList[$model]->getModelDefition('charset') ?? 'utf8') . '";
                                            $table->collation = "' . ($this->ModelList[$model]->getModelDefition('collation') ?? 'utf8_unicode_ci') . '";
                                            $table->bigIncrements("id");
                                            ' . $this->ModelList[$model]->renderDBField($relation->sourceReference, $model . '_', true, ($this->ModelList[$relation->target]->getFieldDefinition($relation->targetReference)['dataType'])) . '
                                            ' . $this->ModelList[$relation->target]->renderDBField($relation->targetReference, $relation->target . '_', true) . '
                                            
                                            $table->foreign("' . $model . '_' . $relation->sourceReference . '") 
                                                    ->references("' . $relation->sourceReference . '")
                                                    ->on("' . $model . '")->onDelete("cascade");

                                            $table->foreign("' . $relation->target . '_' . $relation->targetReference . '") 
                                                    ->references("' . $relation->targetReference . '")
                                                    ->on("' . $relation->target . '")->onDelete("cascade");    
                                            $table->unique(["' . $model . '_' . $relation->sourceReference.'", "' . $relation->target . '_' . $relation->targetReference.'"]);
                                        });
                                    }
                                ';
                                $dropList .= '
                                    Schema::dropIfExists("' . $tableName . '");
                                ';
                                array_push($M2MTables, $tableName);
                            }
                        }
                    }
                    $relationList .= '
                            if(false!==Schema::hasTable("' . $model . '")){
                                Schema::table("' . $model . '", function (Blueprint $table) {
                                        '. $relationString .'
                                });
                            }
                        ';
                    $dropList .= '
                            if(false!==Schema::hasTable("' . $model . '")){
                                Schema::table("' . $model . '", function (Blueprint $table) {
                                    '.$dropString.'
                                });
                            }
                        ';
                }
                if(strlen($relationList)>0 || strlen($M2MList) > 0){
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
                            ' . $M2MList . '
                        }
            
                        public function down()
                        {
                            ' . $dropList . '
                        }
                    }
                    ?>';
                    $this->RootStorage->put($target, $contents);
                }
            } catch (Exception $e) {
                throw new Exception("Error Creating Migration Relations " . $e->getMessage(), 1);
            }
        }
        $this->command->line("Migration created for table relations. ");
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