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
            'editable' => true
        ]
    ];

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
            throw new Exception("There are no model files in the sub direcotry [models]");
        }else{
            foreach($modelFiles as $model){
                $m =json_decode($this->BlueprintStorage->get($model));
                $this->ProcessModels($m);
            }
        }
    }

    private function ProcessModels($model){
        $path = 'app/model/';
        $migrationPath = 'database/migrations/';
        
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