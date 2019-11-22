<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsMethodBuilderBase;

class DisplayCollection extends BluePrintsMethodBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }

    public function BuildMethod(): string
    {

        return $this->PrepareModels() . '
        ' . $this->PrepareInputs() . '
                        $withData=["collection"=>($query->get()??new Collection([]))->toArray()];
        ';
    }

    public function BuildCRUD(): string{
        return '';
    }

}