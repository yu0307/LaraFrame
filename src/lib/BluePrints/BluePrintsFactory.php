<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use feiron\felaraframe\lib\BluePrints\BluePrintsModelFactory;
use feiron\felaraframe\lib\BluePrints\BluePrintsViewFactory;
use feiron\felaraframe\lib\BluePrints\BluePrintsControllerFactory;
class BluePrintsFactory {

    private $blueprint; //stores the contents of the target blueprint.
    private $projectPath;
    private $BlueprintStorage;
    private $RootStorage;
    private $command;
    private $ModelList;
    private $relations;
    private $ViewList;
    
    private const migrationPath="database/migrations/";

    public function __construct($target,$storage,$command){
        $this->BlueprintStorage=$storage;
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
        $this->command= $command;
        $this->projectPath = str_replace($this->BlueprintStorage->getAdapter()->getPathPrefix(), '', dirname($this->BlueprintStorage->path($target)));
        $this->ModelList=[];
        $this->relations=[];
        $this->ViewList=[];

        try{
            $this->blueprint = json_decode($this->BlueprintStorage->get($target));
        }catch(Exception $e){
            throw new Exception("Error Processing blueprint file. Please make sure it's in a correct format.", 1);
        }
    }

    //=================================Model Related Operations=============================

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
            $this->command->line("Model blueprints imported. Now generating files...");
            $this->BuildModel();
            try {
                $this->command->info('-->Now Migrating database to the server...');
                Artisan::call('migrate');
            } catch (Exception $e) {
                throw $e;
            }
            
        }
    }

    public function processModels($model){
        $fieldDefinitions= $model->modelFields??[];
        unset($model->modelFields);
        $views = $model->view??[];
        //<------------------------------------Handle Model Views Definition, Deferred implementation, Needs attention later. 
        unset($model->view);
        $MyModel= new BluePrintsModelFactory((array) $model);
        $this->ModelList[$model->modelName]=$MyModel;
        foreach($fieldDefinitions as $field){
            if(isset($field->relation) && isset($field->relation->target) && isset($field->relation->type)){
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

    private function BuildModel(){
        $relations=[];
        foreach ($this->ModelList as $modelName => $model) {
            $model->buildMigrations();
            $this->command->line("Migration created for " . $modelName);
            $relation=$model->getRelations();
            if(!empty($relation)){
                $relations[$modelName]=$relation;
            }
            $model->BuildModel();
            $this->command->line("Model File created for " . $modelName);
            
        }
        $this->createRelationMigration($relations);
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
                                $newField= $this->ModelList[$relation->target]->renderDBField($relation->targetReference, $relation->target . '_', true);
                                $M2MList .= '
                                if(false===Schema::hasTable("' . $tableName . '")){
                                        Schema::create("' . $tableName . '", function (Blueprint $table) {
                                            $table->engine = "' . ($this->ModelList[$model]->getModelDefition('engine') ?? 'InnoDB') . '";
                                            $table->charset = "' . ($this->ModelList[$model]->getModelDefition('charset') ?? 'utf8') . '";
                                            $table->collation = "' . ($this->ModelList[$model]->getModelDefition('collation') ?? 'utf8_unicode_ci') . '";
                                            $table->bigIncrements("id");
                                            ' . str_replace(($relation->target . '_' . $relation->targetReference), ($model . '_' . $relation->sourceReference), $newField) . '
                                            ' . $newField . '
                                            
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

    //=================================End Related Operation Section=========================



    //=================================View Related Operations=============================
    public function BuildViews(){
        $this->command->line("-->Building View Files and Controllers");
        foreach($this->blueprint->Views as $viewDefinition){
            $view=new BluePrintsViewFactory($viewDefinition,$this->ModelList);
            if(!array_key_exists(($viewDefinition->name??''),$this->ViewList)){
                $this->ViewList[$viewDefinition->name]=$view;
            }
            if(false!==$view->buildView()){
                $this->command->line("view File created for " . $viewDefinition->name);
                $controller = new BluePrintsControllerFactory($viewDefinition, $this->ModelList);
                if(false!== $controller->buildController()){
                    $this->command->line("controller File created for " . $viewDefinition->name);
                }
            }
        }
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
    //=================================End View Operation Section=========================
}
?>