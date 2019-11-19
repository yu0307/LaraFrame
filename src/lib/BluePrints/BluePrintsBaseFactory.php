<?php

namespace feiron\felaraframe\lib\BluePrints;

use Exception;
use Illuminate\Support\Facades\Storage;

class BluePrintsBaseFactory {
    protected $Definition;
    protected $AvailableModels;
    protected $RootStorage;
    protected $routeList;
    protected const viewPath = "resources/views/fe_generated/";
    protected const routePath = "routes/BluePrints/BluePrintsRoute.php";
    protected const controllerPath = "app/Http/Controllers/BluePrints/";
    protected const ViewClassPrefix = 'fe_view_';
    protected const Defaults = [
        'routes' => [
            //  {
            //  "name": "Route_Name",
            //  "slug": used as URL(optional),
            //  "type": POST | GET(default),
            //  "input"(optional): [
            //                 {
            //                     "name": "field_name",
            //                     "onModel": "modal_name"(optional),
            //                     "optional": false | true
            //                 },
            //                 ...
            //             ]
            //  }
            //   ...
        ],
        'style' => 'normal', //normal, table, accordian 
        "usage"=>"display",
        //usage: display | CRUD
        //models: [
        //             (modelName):[
        //                      {
        //                            name:field_name,
        //                            label:formlabel(optional),
        //                            caption:description(optional),
        //                            attr: tag attr(optional),
        //                            class: tag class(optional),
        //                            container_attr: tag attr appied on the container(optional)
        //                            container_class: tag class appied on the container(optional)
        //                      },
        //                            ...
        //                 ],
        //             ...
        //         ]
        'title' => '',
        'subtext' => '',
        'html' => ''
    ];

    public function __construct($definition = null, $ModelList)
    {
        $this->Definition = array_merge(self::Defaults, ((array) $definition ?? []));
        $this->AvailableModels = $ModelList;
        $this->RootStorage = Storage::createLocalDriver(['root' => base_path()]);
        $this->routeList = [];
    }
}