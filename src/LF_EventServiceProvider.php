<?php

namespace \felaraframe;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class LF_EventServiceProvider extends ServiceProvider
{
    protected $listen = [
            'feiron\fe_login\lib\events\UserCreated' => [
                '\felaraframe\lib\Listeners\UserCreated',
            ],
    ];
}
