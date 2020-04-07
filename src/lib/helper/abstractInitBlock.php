<?php
namespace feiron\felaraframe\lib\helper;
use feiron\felaraframe\lib\contracts\feInitBlock;
use Illuminate\Http\Request;
abstract class abstractInitBlock implements feInitBlock
{
    public function name(): string{
        return 'InitBlock';
    }

    public abstract function execute(Request $request);
}
