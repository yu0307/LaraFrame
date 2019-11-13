<?php

namespace feiron\felaraframe\lib\helper;

use Exception;
use Illuminate\Support\Facades\Storage;
class BluePrintsFactory {

    private $blueprint;
    private $projectPath;
    private $BlueprintStorage;
    private $RootStorage;
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
            'collation'=>'utf8_unicode_ci'
        ]
    ];
    private const FieldsWithSize=[
        'char',
        'string'
    ];
    private const migrationPath="database/migrations/";

    public function __construct($target,$storage)
    {
        $this->BlueprintStorage=$storage;
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
        $this->projectPath = str_replace($this->BlueprintStorage->getAdapter()->getPathPrefix(), '', dirname($this->BlueprintStorage->path($target)));
        try{
            $this->blueprint = json_decode($this->BlueprintStorage->get($target));
        }catch(Exception $e){
            throw new Exception("Error Processing blueprint file. Please make sure it's in a correct format.", 1);
        }
    }

    public function buildModels(){
        $modelFiles= preg_grep('/^.*\.(mbp)$/i',$this->BlueprintStorage->files($this->projectPath.'/models'));
        if(empty($modelFiles)){
            echo ("There are no model files in the sub direcotry [models]\n");
        }else{
            foreach($modelFiles as $model){
                $m =json_decode($this->BlueprintStorage->get($model));
                $this->ProcessModels($m);
            }
        }
    }

    private function ProcessModels($models){
        $path = 'app/model/';
        
        foreach($models as $model){
            if(isset($model->modelName)){
                $this->createMigration($model);
            }
        }
    }

    private function createMigration($model){
        $className='create_'.$model->modelName.'_table';
        $target=self::migrationPath.'fe_blueprint_migration_file_'.$className.'.php';
        if($this->RootStorage->exists($target)===false){
            $fieldList="";
            foreach($model->modelFields as $field){
                $fieldList.='
                $table->'.($field->dataType??self::Defaults['model']['dataType']).'("'.$field->name.'"'.(false===in_array($field->dataType,self::FieldsWithSize)?"":(",".($field->size??self::Defaults['model']['size']))).')'
                .(isset($field->nullable) && true===$field->nullable?"->nullable(true)":"->nullable(false)")
                .(isset($field->autoIncrement) && true===$field->autoIncrement?"->autoIncrement()":"")
                .(isset($field->unsigned) && true===$field->unsigned?"->unsigned()":"")
                .(false===empty($field->default)?"->default($field->default)":"")
                .(false===empty($field->charset)?"->charset($field->charset)":"")
                .(false===empty($field->collation)?"->collation($field->collation)":"")
                .(isset($field->unique) && true===$field->unique?"->unique()":"")
                .(isset($field->index) && true===$field->index?"->index()":"")
                .(isset($field->spatialIndex) && true===$field->spatialIndex?"->spatialIndex()":"")
                .(isset($field->primary) && true===$field->primary && $field->dataType!='bigIncrements'?"->primary()":"")
                .';';
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