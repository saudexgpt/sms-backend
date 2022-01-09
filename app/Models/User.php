<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'username',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function lga()
    {
        return $this->belongsTo(LocalGovernmentArea::class, 'lga_id', 'id');
    }
    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    public function getUser($value, $phone = null)
    {
        //
        if ($phone) {
            return  User::where('phone1', $value)->orWhere('phone2', $value)->first();
        }
        return  User::where('email', $value)->first();
    }

    public function uploadFile($request, $file_name, $folder_key)
    {
        $subdomain = ""; //School::where('folder_key', $folder_key)->first()->sub_domain;
        $storage_subfolder = ''; //'storage/';//$subdomain.'/storage/';

        $folder = "schools/" . $folder_key;

        $upload_directory = $storage_subfolder . $folder;

        $photo = $request->file('photo')->storeAs($upload_directory, $file_name, 'public');

        return $photo_name = $folder . '/' . $file_name;
    }
    public function saveUserAsAdmin($request)
    {
        $email = $request->email;
        $username = $request->username;
        $user = $this->getUser($email);
        if (!$user) {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $email;
            //$this->address = $request->address;
            $user->password = $username; //'password';//$email;//$username;
            $user->phone1 = $request->phone1;
            $user->phone2 = $request->phone2;
            $user->role = 'staff';
            $user->gender = $request->gender;
            $user->username = $username;
            $user->religion = $request->religion;
            $photo_name = photoPath($this->school, ['type' => 'default', 'file' => strtolower($request->gender) . '.png']);
            $mime = "image/png";
            if ($request->file('photo') != null && $request->file('photo')->isValid()) {
                $mime = $request->file('photo')->getClientMimeType();
                $name = str_replace('/', '-', $username) . "." . $request->file('photo')->guessClientExtension();
                $folder_key = $request->folder_key . "/photo/user";
                $photo_name = $this->uploadFile($request, $name, $folder_key);

                /*$folder = "schools/".$folder_key."/photo/staff";
                $photo = $request->file('photo')->storeAs($folder, $name, 'public');   */
            }

            $user->photo = $photo_name;
            $user->mime = $mime;
            $user->password_status = defaultPasswordStatus();
            $user->confirm_hash = hashing($email . time());
            $user->is_confirmed = '0';
            $user->save();


            return array($user, 'new_entry');
        }
        return array($user, 'exists');
    }
    public function saveUserAsStaff($request)
    {
        $email = $request->email;
        $username = $request->username;
        $user = $this->getUser($email);
        if (!$user) {
            $this->first_name = $request->first_name;
            $this->last_name = $request->last_name;
            $this->email = $email;
            $this->address = $request->address;
            $this->password = $username;
            $this->phone1 = $request->phone1;
            $this->phone2 = $request->phone2;
            $this->role = 'staff';
            $this->gender = $request->gender;
            $this->username = $username;
            $this->religion = $request->religion;
            $photo_name = photoPath($this->school, ['type' => 'default', 'file' => strtolower($request->gender) . '.png']);
            $mime = $request->mime;
            if ($request->file('photo') != null && $request->file('photo')->isValid()) {
                $mime = $request->file('photo')->getClientMimeType();
                $name = str_replace('/', '-', $username) . "." . $request->file('photo')->guessClientExtension();
                $folder_key = $request->folder_key . "/photo/staff";
                $photo_name = $this->uploadFile($request, $name, $folder_key);
            }

            $this->photo = $photo_name;
            $this->mime = $mime;
            $this->password_status = defaultPasswordStatus(); //'custom';
            $this->save();

            return $this;
        }
        return $user;
    }
    public function saveUserAsParent($request, $action = 'save')
    {
        $phone = $request->parent_phone;
        $phone2 = $request->parent_phone2;
        $username = $request->username;
        $email = $request->email;

        $user = User::where('username', $username)->first();
        if ($email != 'NIL' && $email != '') {

            $user = User::where('email', $email)->first();
            if ($phone != 'NIL' && $phone != '') {
                $user = User::where('email', $email)->orWhereIn('phone1', [$phone, $phone2])->first();
            } elseif ($phone2 != 'NIL' && $phone2 != '') {
                $user = User::where('email', $email)->orWhereIn('phone2', [$phone, $phone2])->first();
            }
        } elseif ($phone != 'NIL' && $phone != '') {
            $user = User::where('phone1', $phone)->first();
            /*if($phone2 != 'NIL'){
                $user = User::whereIn('phone1', [$phone, $phone2])->first();
            }*/
        } elseif ($phone2 != 'NIL' && $phone2 != '') {

            $user = User::whereIn('phone2', $phone2)->first();
        }
        if ($action == 'update') {

            $user_id  = $request->parent_user_id;
            $user = User::find($user_id);
            $user->gender = ($request->sponsor_gender) ? $request->sponsor_gender : 'male';
            $user->photo = photoPath($request->school, ['type' => 'default', 'file' => strtolower($request->gender) . '.png']);
            //$user->mime = $request->mime;
            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->address = $request->address;
            $user->religion = $request->religion;

            $user->email = $email;
            $user->phone1 = $phone;
            $user->phone2 = $phone2;


            $user->lga_id = $request->lga_id;
            $user->state_id = $request->state_id;
            $user->country_id = $request->country_id;
            $user->save();

            return $user->id;

            # code...
        }
        //$user = User::where('email', $email)->orWhereIn('phone1',[$phone, $phone2])->orWhereIn('phone2', [$phone, $phone2])->first();
        //$user = User::where('email', $email)->first();

        //$this->getUser($email/*, 'phone'*/);
        if (!$user) {
            $user = new User();


            $user->username = $username;
            $user->password = $username;   //default password is their username
            $user->password_status = defaultPasswordStatus();
            $user->role = 'parent';
            $user->gender = ($request->sponsor_gender) ? strtolower($request->sponsor_gender) : 'male';
            $user->photo = photoPath($request->school, ['type' => 'default', 'file' => strtolower($request->gender) . '.png']);
            //$user->mime = $request->mime;
            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->address = $request->address;
            $user->religion = $request->religion;

            if ($email != 'NIL') {
                $user->email = $email;
            }
            if ($phone != 'NIL') {
                $user->phone1 = $phone;
            }
            if ($phone2 != 'NIL') {
                $user->phone2 = $phone2;
            }

            $user->lga_id = $request->lga_id;
            $user->state_id = $request->state_id;
            $user->save();

            return array($user->id, 'new_entry');
        }

        $user->lga_id = $request->lga_id;
        $user->state_id = $request->state_id;
        $user->save();

        return array($user->id, 'exists');
    }
    public function saveUserAsStudent($request, $action = "save")
    {
        $uniq_id = $request->parent_user_id;
        $username = $request->username;
        $this->first_name = $request->first_name;
        $this->last_name = $request->last_name;
        //generate the student email address

        $this->address = $request->address;
        $this->lga_id = $request->lga_id;
        $this->state_id = $request->state_id;
        $this->country_id = $request->country_id;
        $this->disablility = $request->disablility;

        if ($request->file('photo') != null && $request->file('photo')->isValid()) {
            $mime = $request->file('photo')->getClientMimeType();
            $name = str_replace('/', '-', $username) . "." . $request->file('photo')->guessClientExtension();
            $folder_key = $request->folder_key . "/photo/student";

            $photo_name = $this->uploadFile($request, $name, $folder_key);

            /*
            $folder = "schools/".$folder_key."/photo/student";
            $photo = $request->file('photo')->storeAs($folder, $name, 'public');*/
            $this->photo = $photo_name;
            $this->mime = $mime;
        } else {
            $this->photo = photoPath($request->school, ['type' => 'default', 'file' => strtolower($request->gender) . '.png']);
        }






        if ($action == 'update') {
            //this is the students.id from students table
            $id = $request->student_id;
            $student = Student::findOrFail($id);

            //get the users.id from the students table
            $this->id = $student->user_id;
            $this->dob = date('Y-m-d', strtotime($request->dob));

            //retrieve  user fields to perform update action
            $this->save();

            return $this->id;
        } else {
            $this->dob = date('Y-m-d', strtotime($request->dob));
            $this->username = $username;
            $gender = strtolower($request->gender);
            if ($gender == 'm' || $request->gender == 'male') {
                $this->gender = 'male';
            } else {
                $this->gender = 'female';
            }

            $this->password = $username; //$request->password;
            $this->role = 'student';
            $this->password_status = defaultPasswordStatus();
            $this->religion = $request->religion;
            $this->save();

            return $this->id;
        }
    }

    public function isSuperAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->isSuperAdmin()) {
                return true;
            }
        }

        return false;
    }
}
