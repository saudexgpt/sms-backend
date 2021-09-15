<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

use App\Models\GroupOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use App\Models\Teacher;
use App\Models\StaffLevel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Hash;

class StaffController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $school = $this->getSchool();
        $staff = Staff::with('user')->where('school_id', $school->id)->get();

        return $this->render(compact('staff'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(User $user)
    {
        $school_id = $this->getSchool()->id;
        $username = $this->generateUsername($school_id, 'staff');
        $countries = Country::with('states.lgas')->orderBy('country_name')->get();
        $selected_country = Country::with('states.lgas')->where('country_name', 'Nigeria')->first();

        $staff_roles = Role::where('role_type', 'staff')->whereIn('school_id', [0, $school_id])->get();
        return $this->render(compact('username', 'countries', 'selected_country', 'staff_roles'));
    }

    /**
     * @param UserRequest $request
     * @param Staff $staff
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Staff $staff,  User $user)
    {
        // return $request;
        // Create staff account
        $request->school_id = $this->getSchool()->id;
        try {
            $saved_user = $user->saveUserAsStaff($request);
            $request->user_id = $saved_user->id;
            // asssign the roles to the user
            $saved_user->syncRoles($request->roles);
            $staff_id = $staff->registerStaff($request);
            // $staff_role_obj = new StaffRole();
            // $staff_role_obj->addStaffRole($staff_id, $request->school_id, $request->roles);
            $this->updateUniqNumDb($this->getschool()->id, 'staff');

            $action = "Registered " . $request->first_name . " " . $request->last_name . " as new staff";
            $this->auditTrailEvent($request, $action);
            //$new_user = User::find($request->student_user_id);
            //$all_staff = User::where('role', 'staff')->get();
            //$user->notify(new NewRegistration($user));
            //Notification::send($all_staff, new NewRegistration($new_user));
            return 'Successful';

            // Flash::success('Staff information added successfully');
        } catch (ModelNotFoundException $ex) {
            // Flash::error('Error: ' . $ex->getMessage());
        }
        return redirect()->route('staff.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $staff = Staff::findOrFail($id);
            $schoolsList = ['' => 'Select School'] + School::pluck('name', 'id')->all();
            return $this->render('core::staff.edit', compact('staff', 'schoolsList'));
        } catch (ModelNotFoundException $ex) {
            // Flash::error('Error: ' . $ex->getMessage());
            return redirect()->route('staff.index');
        }
    }

    /**
     * @param StaffRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(User $user, Request $request, $id = null)
    {

        try {
            if ($user->hasRole('admin')) {

                $inputs = request()->all();
                $staff = Staff::findOrFail($request->id);


                $staff->update($inputs);

                // Flash::success('Staff information updated successfully');

                if (request()->ajax()) {
                    return 'true';
                }

                return redirect()->route('staff.index');
            }
            /*$staff = Staff::findOrFail($id);
            $inputs = $request->all();

            if ($request->hasFile('avatar')) {
                // Unlink the old image
                Storage::disk('public')->delete($staff->avatar);

                $mime = $request->file('avatar')->getClientMimeType();
                $name = "staff_".time().".".$request->file('avatar')->guessClientExtension();
                $avatar = $request->file('avatar')->storeAs('staffs', $name, "public");
                $inputs['$avatar'] = $avatar;
                $inputs['mime'] = $mime;
            }
            $staff->update($inputs);
            Flash::success('Staff information updated successfully');
            return redirect()->route('staff.index');*/
        } catch (ModelNotFoundException $ex) {
            // Flash::error('Error: ' . $ex->getMessage());
            return redirect()->route('staff.index');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $staff = Staff::with(['user.state', 'user.lga', 'classTeachers.c_class', 'subjectTeachers' => function ($q) {
            $q->orderBy('class_teacher_id');
        }, 'subjectTeachers.classTeacher.c_class', 'subjectTeachers.subject'])->findOrFail($id);
        return $this->render(compact('staff'));
    }

    public function destroy(Request $request)
    {
        $id = $request->staff_id;
        if ($this->getStaff()->id != $id) {
            $staff = Staff::findOrFail($id);
            if ($staff->delete()) {
                return 'true';
            }
            return 'false';
        }

        return 'false';
    }
    public function assignStaffRole(Request $request)
    {
        $roles = $request->roles;
        $staff_id = $request->staff_id;
        $staff = Staff::find($staff_id);

        $school = $this->getSchool();
        $school_id = $school->id;
        //$staff_id = Staff::find('pf_no', $pf_no)->first()->id;

        foreach ($roles as $role) {
            if ($role === 'proprietor') {
                if ($school->group_of_school_id !== null) {
                    // give proprietor access to the group of schools
                    $group_of_school = GroupOfSchool::find($school->group_of_school_id);
                    $group_of_school->proprietor_user_id = $staff->user_id;
                    $group_of_school->save();
                }
            }
            $staff_role_obj = new StaffRole();
            $staff_role_obj->addStaffRole($staff_id, $school_id, $role);
        }
        //$request->session()->flash('tab_display', 'assign_role');
        // Flash::success('Roles Assigned Successfully');

        return redirect()->route('staff.index');
    }

    public function unassignStaffRole(Request $request)
    {
        $school = $this->getSchool();
        $role = $request->role;
        $staff_id = $request->staff_id;
        if ($role === 'proprietor') {
            if ($school->group_of_school_id !== null) {
                // give proprietor access to the group of schools
                $group_of_school = GroupOfSchool::find($school->group_of_school_id);
                $group_of_school->proprietor_user_id = null;
                $group_of_school->save();
            }
        }

        $staff_role = StaffRole::where(['staff_id' => $staff_id, 'role' => $role])->first();

        if ($staff_role) {
            $staff_role->delete();
        }
        // Flash::error('Role Unssigned Successfully');
        if (request()->ajax()) {
            return 'true';
        }
        //$request->session()->flash('tab_display', 'assign_role');


        return redirect()->route('staff.index');
    }
}
