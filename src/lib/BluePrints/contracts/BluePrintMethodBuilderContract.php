<?php

namespace feiron\felaraframe\lib\BluePrints\contracts;

interface BluePrintMethodBuilderContract
{

    public function BuildMethod():string;

    public function BuildCRUD(): string;

}