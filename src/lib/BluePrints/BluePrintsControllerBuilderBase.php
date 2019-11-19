<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\contracts\BluePrintControllerBuilderContract;

abstract class BluePrintsControllerBuilderBase implements BluePrintControllerBuilderContract {

    protected $MethodDefinition;

    // $MethodDefinition=[
    //     'name'
    //     'targetView'
    //     'input'=>[
    //         'name'
    //         'optional'
    //         'onModel'
    //     ]
    // ]

    public function __construct($MethodDefinition = null, $ModelList){
        $this->MethodDefinition=$MethodDefinition;
    }

    public abstract function BuildControllerMethod():array;

}