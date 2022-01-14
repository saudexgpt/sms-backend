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
        if ($this->student) {
            $school  = $this->student->school;
        }
        if ($this->guardian) {
            $school  = $this->guardian->school;
        }
        if ($this->staff) {
            $school  = $this->staff->school;
        }
        return [
            'id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'first_name' => $this->first_name,
            'last_name' =>  $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'student' => $this->student,
            'guardian' => $this->guardian,
            'staff' => $this->staff,
            'school' => $school,
            'password_status' => $this->password_status,
            'notifications' => [],
            // 'activity_logs' => $this->notifications()->orderBy('created_at', 'DESC')->get(),
            'roles' => $rights,
            // 'role' => 'admin',
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->allPermissions()->toArray()
            ),
            'avatar' => '/' . $this->photo, //'https://i.pravatar.cc',
            'can_edit' => $can_edit,
        ];
    }
}
