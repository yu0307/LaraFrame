<?php

namespace feiron\felaraframe\lib\contracts;
use Illuminate\Http\Request;
interface feFilterBlock
{

    public function name(): string;

    public function filter(Request $request,$widgetManager);

}
