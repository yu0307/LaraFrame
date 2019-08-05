<?php
namespace FeIron\LaraFrame\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use FeIron\Fe_Roles\models\fe_roles;
class LF_Role_Controller extends Controller
{
    public function getUsersByRole(Request $request, $roleName){
        $role=fe_roles::where('name',$roleName)->firstOr(function(){return false;});
        return ($role===false?[]:$role->User->pluck('id','name')->toArray());
    }
}
