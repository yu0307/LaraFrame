<?php

namespace FeIron\LaraFrame\lib\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use FeIron\Fe_Roles\models\fe_User;
use FeIron\Fe_Roles\models\fe_roles;
class UserCreated
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        fe_User::find(1)->Roles()->save(fe_roles::where('name','Call Rep')->first());
    }
}
