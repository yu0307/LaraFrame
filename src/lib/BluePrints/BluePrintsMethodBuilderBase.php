<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\contracts\BluePrintMethodBuilderContract;

abstract class BluePrintsMethodBuilderBase implements BluePrintMethodBuilderContract {

    protected $MethodDefinition;
    protected $ModelList;
    private const DEFAULT=[
        "name" => "",
        "view" => "",
        "type" => "GET",
        "style" => "singular",
        "params" => [],
        "useModel" => [],
    ];

    public function __construct($MethodDefinition = null, $ModelList){
        $this->MethodDefinition=array_merge(self::DEFAULT,$MethodDefinition);
        $this->ModelList= $ModelList;
    }

    public abstract function BuildMethod():string;

}