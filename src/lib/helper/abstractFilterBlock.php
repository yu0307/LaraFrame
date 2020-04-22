<?php
namespace feiron\felaraframe\lib\helper;
use feiron\felaraframe\lib\contracts\feFilterBlock;
use Illuminate\Http\Request;
abstract class abstractFilterBlock implements feFilterBlock
{
    public function name(): string{
        return 'filterBlock';
    }

    public abstract function filter(Request $request,$object);
}
