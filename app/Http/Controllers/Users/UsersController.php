<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\School;
use App\Models\Staff;
use App\Models\State;
use App\Models\Student;
use Auth;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class UsersController extends Controller
{
    public function userNotifications()
    {
        $user = $this->getUser();
        $notifications = $user->notifications()->orderBy('created_at', 'DESC')->take(20)->get();
        $unread_notifications = $user->unreadNotifications()->count();
        // if ($notifications->isEmpty()) {
        //     $notifications = $user->notifications()->orderBy('created_at', 'DESC')->take(20)->get();
        //     // $notifications =
        // }
        return response()->json(compact('notifications', 'unread_notifications'), 200);
    }
    public function markNotificationAsRead()
    {
        $user = $this->getUser();
        $user->unreadNotifications->markAsRead();
        return $this->userNotifications();
    }
    public function changePassword()
    {
        $user = $this->getUser();
        $user->password_status = 'default';
        $user->save();
        return redirect()->route('dashboard');
    }
    public function adminResetUserPassword(Request $request)
    {
        $user = User::find($request->user_id);
        $user->password = 'password';
        $user->password_status = 'default';
        $user->save();
    }
    public function resetPassword(Request $request, User $user)
    {
        $confirm_password = $request->confirm_password;
        $new_password = $request->new_password;

        if ($new_password === $confirm_password) {
            $user->password = $new_password;
            $user->password_status = 'custom';

            if ($user->save()) {
                return response()->json(['message' => 'success'], 200);
            }
        }
        return response()->json([
            'message' => 'Password does not match'
        ], 401);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addAdministrators(User $user, Request $request)
    {
        //
        if (Auth::user()->role->role === 'super') {


            $roles = Role::whereIn('role', ['school admin', 'super'])->get();

            $schools = School::where('is_active', '1')->orderBy('name')->get();

            if ($request->isMethod('post')) {

                //$user = new User();

                return $user->registerNewUser($request);
            }
            return view('cpanel.add_admin', compact('roles', 'schools'));
        }
    }




    public function show(User $user)
    {
    }

    public function editProfile()
    {
        $state_array = ['' => 'Select State'];
        $states = State::orderBy('name')->get();
        foreach ($states as $state) {
            $state_array[$state->id] = $state->name;
        }
        $user = $this->getUser();
        return $this->render('core::users.edit', compact('user', 'state_array'));
    }

    public function editPhoto(Request $request)
    {

        if (isset($request->user_id) && $request->user_id != '') {
            $user_id = $request->user_id;
            $edit_user = User::find($user_id);
        } else {
            $edit_user = $this->getUser();
        }

        return $this->render('core::users.edit_photo', compact('edit_user'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto(Request $request)
    {
        //
        $request->file('photo');
        $school = $this->getSchool();
        $user_id = $request->user_id;
        $user = User::find($user_id);
        if ($request->file('photo') != null && $request->file('photo')->isValid()) {
            $name = str_replace('@', '_', $user->username);
            $name = str_replace('.', '_', $name) . "." . $request->file('photo')->guessClientExtension();
            $folder_key = $school->folder_key;
            $photo_name = $user->uploadFile($request, $name, $folder_key);
            $user->photo = $photo_name;
            if ($user->save()) {
                //return "success";
                if ($request->ajax()) {

                    return "true";
                }
                Flash::success('Photo Updated Successfully');
                return redirect()->route('dashboard');
            }
        }

        Flash::error('An error occured. Please try again.');
        return redirect()->route('dashboard');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        //
        //return $request->getMethod();
        $school = $this->getSchool();
        $user = $this->getUser();
        $update_data_array =  request()->all();

        if ($user->update($update_data_array)) {
            //return "success";
            Flash::success('Profile Updated Successfully');
            return redirect()->route('dashboard');
        }
        Flash::error('An error occured. Please try again.');
        return redirect()->route('dashboard');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function existingUser(Request $request, User $users)
    {
        //
        $phone = $request->phone;

        $user = $users->getUser($phone, 'phone');

        if ($user) {
            $first_name = $user->first_name;
            $last_name = $user->last_name;
            $address = $user->address;
            $email = $user->email;
            $phone1 = $user->phone1;
            $phone2 = $user->phone2;
            $available = 'true';
        } else {
            $first_name = '';
            $last_name = '';
            $address = '';
            $email = '';
            $phone1 = '';
            $phone2 = '';
            $available = 'false';
        }
        $user_details = array(
            'fname' => $first_name,
            'lname' => $last_name,
            'address' => $address,
            'email' => $email,
            'phone1' => $phone1,
            'phone2' => $phone2,
            'available' => $available


        );


        //encode the customer_details
        return json_encode($user_details);
    }
}
