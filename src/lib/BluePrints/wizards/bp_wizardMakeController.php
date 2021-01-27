<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardbase;

class bp_wizardMakeController extends bp_wizardbase
{
    protected $command;
    protected $storage;
    private $path;
    private $controllerName;
    private $libList;

    public function __construct($Command)
    {
        parent::__construct($Command);
        $this->path= ((empty($this->command->option('path')))?'/App/Http/Controllers': $this->command->option('path'));
        $this->controllerName=$this->command->argument('name')??null;
        $this->libList=[];
    }

    public function Build(){
        if(empty($this->controllerName) && $this->command->option('wizard')===false){
            $this->command->error('Controller name is required or using flag -W to use wizard.');
        }else{
            $path='/'.str_replace('\\', "/", trim($this->path, '/')) . '/';
            if($this->command->option('wizard')){
                $this->Wizard(true);
            }
            $this->command->info('Creating Controller...');
            $this->storage->put( ($path. $this->controllerName . '.php'), $this->BuildController());
            $this->command->info('Controller file is created and stored at '. $path . $this->controllerName . '.php');
        }
    }

    private function Wizard($banner=false){
        if($banner===true){
            $this->command->comment("=====Welcome to BluePrints Controller building utility.======");
            $this->command->comment("This wizard will walk you through the steps to create a controller.\nLet's get started...");
        }
        while(empty($this->controllerName)){
            $this->controllerName= $this->command->ask('Controller Name(without ".php"):');
        }
        $lib=0;
        while(true){
            $lib=$this->command->choice('Add framework Library? (Press "Enter" anytime to skip)', ['DataTable', 'CrudActions','No Thank You!'],2);
            if($lib== 'No Thank You!'){
                break 1;
            }
            switch ($lib) {
                case 'DataTable':
                    if(!in_array('DataTable',$this->libList)){
                        array_push($this->libList, 'DataTable');
                    }
                    break;
                case 'CrudActions':
                    if(!in_array('CrudActions',$this->libList)){
                        array_push($this->libList, 'CrudActions');
                    }
                    break;
            }
        }
        if ($banner === true) {
            $this->command->comment("=====Thank you for using BluePrint Wizard.======");
        }
    }

    private function BuildController(){
        return "<?php
    namespace ". join(
        '\\',
        array_map(
            function ($word) {
                return ucwords($word);
            },
            explode('/', trim($this->path, '/'))
        )
    ). ";
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    ". (in_array('CrudActions', $this->libList)? 'use feiron\felaraframe\lib\traits\crudActions;':''). "
    " . (in_array('dataTable', $this->libList) ? 'use feiron\felaraframe\lib\traits\dataTables;' : '') . "

    class ". $this->controllerName. " extends Controller{
        " . (in_array('CrudActions', $this->libList) ? 'use crudActions;' : '') . "
        " . (in_array('dataTable', $this->libList) ? 'use dataTables;' : '') . "

    }
?>
        ";
    }
}
