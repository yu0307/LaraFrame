<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsMethodBuilderBase;

class DisplaySingularInfo extends BluePrintsMethodBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition??[]), $ModelList);
    }

    public function BuildMethod(): string{

        return $this->PrepareModels().'
        '. $this->PrepareInputs(). '
                        $withData=array_merge($withData,(($query->first()??new Collection([]))->toArray()));
        ';
    }
}