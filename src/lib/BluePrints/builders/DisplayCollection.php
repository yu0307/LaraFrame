<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsControllerBuilderBase;

class DisplayCollection extends BluePrintsControllerBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }

    public function BuildMethod(): string
    {

        return $this->PrepareModels() . '
        ' . $this->PrepareInputs() . '
                        $withData=($query->get()??new Collection([]))->toArray();
        ';
    }

}