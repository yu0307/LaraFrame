<?php

namespace feiron\felaraframe\lib\contracts;
use Illuminate\Http\Request;
interface feInitBlock
{

    public function name(): string;

    public function execute(Request $request);

}
