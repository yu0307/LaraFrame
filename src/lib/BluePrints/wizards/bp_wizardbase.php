<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;
use Illuminate\Support\Facades\Storage;

abstract class bp_wizardbase
{
    protected $command;
    protected $storage;
    protected const OPTIONLIST = [
        'unsingn' => ['bigInteger', 'decimal', 'double', 'float', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'],
        'autoIncrement' => ['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'],
        'NonDefault' => ['uuid', 'tinyIncrements', 'smallIncrements', 'rememberToken', 'mediumIncrements', 'increments', 'bigIncrements'],
        'hasOption' => [
            'char' => ['size' => 100],
            'string' => ['size' => 100],
            'decimal' => ['Decimal Points(Seperated by comma ",".)' => '8,2'],
            'unsignedDecimal' => ['Decimal Points(Seperated by comma ",".)' => '8,2'],
            'double' => ['Decimal Points(Seperated by comma ",". Default: 8,2)' => '8,2'],
            'float' => ['Decimal Points(Seperated by comma ",".)' => '8,2'],
            'enum' => ['List Of Values(Seperate each with comma ",")' => ''],
            'set' => ['List Of Values(Seperate each with comma ",")' => '']
        ]
    ];
    protected $Modifers = [
        'autoIncrement' => 'Integer type auto-increment',
        'nullable' => 'Allows (by default) NULL values to be inserted into the column',
        'unsigned' => 'Set INTEGER columns as UNSIGNED (MySQL)',
        'useCurrent' => 'Set TIMESTAMP columns to use CURRENT_TIMESTAMP as default value',
        'primary' => 'Set as Primary Key',
        'editable' => 'Make field available for mass assignables'
    ];
    protected $options = [
        'engine' => 'InnoDB',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'timestamps' => true,
        'migrate' => false
    ];
    protected const PAGETEMPLATE = [
        "name" => "",
        "title" => '', //Displayed in title tag
        "subtext" => '', //Displayed after HTML contents as text and wrapped in H5 tag
        'style' => 'Singular', //'singular', 'table', 'accordion', 'collection', 'crud', 'crudsingleton', 'crudsingletonlist'
        "model" => [
            // "name"=> "",
            // "fields"=> "all" | ["name"=>"Field Name", "caption"=>"label displayed"]
            // (Optional)"with"=> [
            // [
            //     "name"=> "",
            //     "fields"=> "all"
            // ]
            // ]
            // (Optional)"join": [
            //     {
            //         "type": "left|full|...etc",
            //         "name": "Model Name",
            //         "fields": "all"| ["name"=>"Field Name", "caption"=>"label displayed"],
            //         "on": [
            //             "name(Local),name(Foreign"
            //         ],
            //         "modifier"(Optional): [
            //             {
            //                 "name": "fieldName",
            //                 "symbol": "Operators(<,>,<>,=,etc...)",
            //                 "value": "on value"
            //             }
            //         ]
            //     }
            // ]
        ],
        'routes' => [
            // [
            //  "name"=> "Route_Name",
            //  "slug"=> used as URL(optional),
            //  "type"=> POST | GET(default),
            //  "input"(optional)=> [
            //                 [
            //                     "name"=> "field_name",
            //                     "onModel"=> "Model Name" (Optional, As Variable if not on model)
            //                     "optional"=> false | true
            //                 ],
            //                 ...
            //             ]
            // ]
        ],
        // For Style="table" Only---------------------

        // "tableFilter"=>[
        // {
        //     "name"=> "on Model Name",
        //     "fields"=> [
        //         "all"| ["field name",...]
        //     ]
        // }
        // ],
        // "headerSearch"=>true | false,

        // -------------------------------------------

        'html' => '', //Html Contents before the page components
        "visible" => []
    ];

    public function __construct($Command)
    {
        $this->command=$Command;
        $this->storage = Storage::createLocalDriver(['root' => base_path()]);
    }

    public abstract function Build();
}
