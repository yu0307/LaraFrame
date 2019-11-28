<?php

namespace feiron\felaraframe\lib\BluePrints\contracts;

interface BluePrintViewBuilderContract
{

    public function BuildView():string;

    public function BuildCRUD(): string;

}