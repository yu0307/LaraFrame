<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\contracts\BluePrintViewBuilderContract;

abstract class BluePrintsViewBuilderBase implements BluePrintViewBuilderContract {

    protected $ViewDefinition;
    protected $ModelList;

        
    private const DEFAULT=[
        "name" => "",
        "style" => "singular",
        "usage" => "display",
        "title" => "",
        "subtext" => "",
        "html" => "",
        "FieldList" => [],
        "headerSearch" => false
    ];

    public function __construct($ViewDefinition = null, $ModelList){
        $this->ViewDefinition=array_merge(self::DEFAULT,$ViewDefinition);
        $this->ModelList= $ModelList;
    }

    public abstract function BuildView():string;
    public abstract function BuildCRUD(): string;

}