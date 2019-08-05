<?php

namespace FeIron\LaraFrame;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class LF_EventServiceProvider extends ServiceProvider
{
    protected $listen = [
            'FeIron\Fe_Login\lib\events\UserCreated' => [
                'FeIron\LaraFrame\lib\Listeners\UserCreated',
            ],
    ];
}
