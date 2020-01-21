<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;

class BluePrintsBaseFactory {
    protected $Definition;
    protected $AvailableModels;
    protected $RootStorage;
    protected const viewPath = "resources/views/fe_generated/";
    protected const routePath = "routes/BluePrints/BluePrintsRoute.php";
    protected const controllerPath = "app/Http/Controllers/BluePrints/";
    protected const ViewClassPrefix = 'fe_view_';
    protected const ViewPackage = 'fe_generated';
    protected const ControllerClassPostfix = '_FeBp_Controller';
    protected const ModelClassPrefix = 'fe_bp_';

    public function __construct($definition = null, $ModelList=null)
    {
        $this->loadDefinition($definition);
        $this->setModelList($ModelList);
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
    }

    public function loadDefinition($definition = null){
        $this->Definition = ((array) $definition ?? []);
    }

    public function setModelList($ModelList=null){
        $this->AvailableModels = $ModelList??[];
    }
}