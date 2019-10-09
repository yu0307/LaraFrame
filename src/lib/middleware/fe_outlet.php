<?php

namespace feiron\felaraframe\lib\middleware;

use Closure;
use feiron\felaraframe\lib\outlet\frameOutlet;

class fe_outlet
{
    protected $FrameOutlet;

    public function __construct(frameOutlet $FrameOutlet)
    {
        $this->FrameOutlet = $FrameOutlet;
    }
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}