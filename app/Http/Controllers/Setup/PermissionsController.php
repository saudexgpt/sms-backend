<?php

namespace App\Http\Controllers\Setup;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $school = $this->getSchool();
        $permissions =  Permission::where('hidden', '0')->orderBy('name')->get();
        return $this->render(compact('permissions'));
    }



    public function assignUserPermissions(Request $request)
    {
        $user = User::find($request->user_id);
        $user->syncPermissions($request->permissions);
        $permissions = $user->allPermissions();
        return response()->json(compact('permissions'), 200);
    }
    public function assignRolePermissions(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->syncPermissions($request->permissions);
        $permissions = $role->permissions;
        return response()->json(compact('permissions'), 200);
    }
    // public function removeAssignedPermission(Request $request)
    // {
    //     $user = User::find($request->user_id);
    //     $user->detachPermissions($request->permissions);
    // }
}