<?php

namespace feiron\felaraframe\lib\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \feiron\fe_roles\models\fe_User;
use \feiron\fe_roles\models\fe_roles;
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
        // fe_User::find($event->User->id)->Roles()->save(fe_roles::where('name','subscriber')->first());
    }
}
