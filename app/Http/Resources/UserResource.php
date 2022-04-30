<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $currentUser = Auth::user();
        $can_edit = false;
        if ($this->id === $currentUser->id || $currentUser->hasRole('super')) {
            $can_edit = true;
        }
        $user_role = [$this->role];
        $roles = array_map(
            function ($role) {
                return $role['name'];
            },
            $this->roles->toArray()
        );
        // $permissions = array_map(
        //     function ($permission) {
        //         return $permission['name'];
        //     },
        //     $this->allPermissions()->toArray()
        // );
        $rights = array_merge($roles, $user_role);
        $school = '';

        $my_wards_ids = [];
        $suspended_for_nonpayment = 0;
        if ($this->student) {
            $school  = $this->student->school()->with(['package.packageModules.module', 'currentTerm', 'currentSession'])->first();
            if ($this->student->suspended_for_nonpayment == 1) {
                $suspended_for_nonpayment = 1;
            }
        }
        if ($this->guardian) {
            $wards = $this->guardian->guardianStudents;
            $school  = $this->guardian->school()->with(['package.packageModules.module', 'currentTerm', 'currentSession'])->first();
            foreach ($wards as $ward) {
                $my_wards_ids[] = $ward->student_id;
            }
        }
        if ($this->staff) {
            $school  = $this->staff->school()->with(['package.packageModules.module', 'currentTerm', 'currentSession'])->first();
        }
        $modules = [];
        if ($school != '') {
            $module_packages = $school->package->packageModules;
            foreach ($module_packages as $module_package) {

                $modules[] = $module_package->module->slug;
            }
            if ($school->suspended_for_nonpayment == 1) {
                $suspended_for_nonpayment = 1;
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'first_name' => $this->first_name,
            'last_name' =>  $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone1 . ' | ' . $this->phone2,
            'username' => $this->username,
            'gender' => $this->gender,
            'address' =>  $this->address,
            'student' => $this->student,
            'guardian' => $this->guardian,
            'my_wards_ids' => $my_wards_ids,
            'staff' => $this->staff,
            'school' => $school,
            'suspended_for_nonpayment' => $suspended_for_nonpayment,
            'password_status' => $this->password_status,
            'notifications' => [],
            // 'activity_logs' => $this->notifications()->orderBy('created_at', 'DESC')->get(),
            'roles' => $rights,
            'modules' => $modules,
            // 'role' => 'admin',
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->allPermissions()->toArray()
            ),
            'avatar' => '/' . $this->photo, //'https://i.pravatar.cc',
            'photo' => '/' . $this->photo,
            'can_edit' => $can_edit,
            'whatsapp_no' => '2347044449412'
        ];
    }
}
