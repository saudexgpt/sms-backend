<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\CClass;
use App\Models\ClassTeacher;
use App\Models\Country;
use App\Models\Guardian;
use App\Models\Level;
use App\Models\LocalGovernmentArea;
use App\Models\RegistrationPin;
use App\Models\SSession;
use App\Models\State;
use App\Models\Student;
use App\Models\StudentsInClass;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Notification;


class StudentsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $levels = $this->getLevels(); //Level::all();

        return $this->render(compact('levels'));
    }
    public function allStudentsTable(Request $request)
    {
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        $levels = $this->getLevels();
        $level_id = $levels[0]->id;
        if (isset($request->level_id) && $request->level_id != '') {
            $level_id = $request->level_id;
        }
        // $students_in_class = StudentsInClass::with(['student.studentGuardian.guardian.user', 'student.user', 'classTeachers.c_class'])->where(['sess_id' => $sess_id, 'level_id'=> $level_id, 'school_id' => $school_id])->get();
        $level = Level::with(['classTeachers.c_class', 'studentsInClass' => function ($query) use ($school_id, $sess_id) {
            $query->where(['sess_id' => $sess_id, 'students_in_classes.school_id' => $school_id]);
        }, 'studentsInClass.student.studentGuardian.guardian.user', 'studentsInClass.student.user.country.states.lgas', 'studentsInClass.student.user.state.lgas', 'studentsInClass.student.user.lga', 'studentsInClass.classTeacher.c_class'])->where('school_id',  $school_id)->find($level_id);

        $students_in_class = $level->studentsInClass;


        return  $this->render(compact('students_in_class', 'levels', 'level'));
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchAlumni()
    {
        $school_id = $this->getSchool()->id;
        $alumni = Alumni::with('student.user')->where('school_id', $school_id)->orderBy('graduate_session', 'DESC')->paginate(10);
        return $this->render('core::students.alumni', compact('alumni'));
    }

    public function promoteStudentForm()
    {

        $term_id = $this->getTerm()->id;
        $levels = $this->getLevels(); //Level::all();
        $level_array = ['' => 'Select Level'];
        foreach ($levels as $level) {
            $level_array[$level->id] = formatLevel($level->level);
        }
        $level_array['alumni'] = 'Alumni';
        return $this->render('core::students.promote_student', compact('level_array', 'term_id'));
    }

    public function studentRegPinForm()
    {
        $reg_pins = RegistrationPin::where(['school_id' => $this->getSchool()->id, 'pin_type' => 'student', 'status' => 'unused'])->get();
        return $this->render('core::students.reg_pin_form', compact('reg_pins'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {


        //if(session()->has('pin') && session()->exists('school_id') && session()->has('type')){
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        /////////////Information for adding a new student/////////////////////////////
        // $admission_session = $this->getSession();
        $reg_no = $this->generateUsername($school_id, 'student');
        //$this->updateUniqNumDb($school->id, 'student');

        $parent_username = $this->generateUsername($school_id, 'parent');
        //$this->updateUniqNumDb($school->id, 'parent');
        $levels = $this->getLevels(); //Level::all();

        $countries = Country::with('states.lgas')->orderBy('country_name')->get();
        $selected_country = Country::with('states.lgas')->where('country_name', 'Nigeria')->first();
        $admission_sessions = SSession::where('id', '<=', $sess_id)->orderBy('id', 'DESC')->get();
        //////////////////////////////////////////////////////////////////////////


        return  $this->render(compact('levels', 'countries', 'selected_country', 'reg_no', 'admission_sessions', 'parent_username'));
        /*}
        return redirect()->route('student_reg_pin');*/
    }

    private function isDuplicateStudent($first_name, $last_name)
    {
        $existing_students = User::where(['last_name' => $last_name, 'role' => 'student'])->get();

        foreach ($existing_students as $existing_student) {
            $existing_student_first_name = $existing_student->first_name;
            $existing_student_first_name_array = explode(' ', $existing_student_first_name);

            $student_first_name_array = explode(' ', $first_name);
            $count_entries = count($student_first_name_array);
            $count_existing_names = 0;
            foreach ($student_first_name_array as $student_first_name) {
                if (in_array($student_first_name, $existing_student_first_name_array)) {
                    $count_existing_names++;
                }
            }

            if ($count_entries === $count_existing_names) {
                return true;
            }
            return false;
        }
    }
    /**
     * @param StudentRequest $request
     * @param Student $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //save and retrieve class information
        // return $request;
        $student_obj = new Student();
        $school = $this->getSchool();
        $request->school_id = $this->getSchool()->id;


        if ($this->isDuplicateStudent($request->first_name, $request->last_name)) {
            return response()->json(['message' => "$request->last_name $request->first_name exists already"], 409);
        }

        $request->folder_key = $school->folder_key;


        $username = $this->generateUsername($school->id, 'parent');
        $request->username = $username;
        $user_obj = new User();
        list($request->parent_user_id, $entry_status) = $user_obj->saveUserAsParent($request);

        if ($entry_status == 'new_entry') {
            $this->updateUniqNumDb($school->id, 'parent');
        }

        // check for duplicate students

        $username = $this->generateUsername($school->id, 'student');
        $request->username = $username;
        //save user information as student
        $user_obj = new User();
        $request->student_user_id = $user_obj->saveUserAsStudent($request);
        $this->updateUniqNumDb($school->id, 'student');

        //save students table informaiton
        $request->registration_no = $username;
        $request->student_id = $student_obj->saveStudentInfo($request);


        $request->class_id = $request->class_teacher_id;



        //add student to class
        $student_in_class_obj = new StudentsInClass();
        $student_in_class_obj->addStudentToClass($request->student_id, $request->class_id, $request->admission_sess_id, $this->getTerm()->id, $school->id);

        //save guardian informaiton
        $guardian_obj = new Guardian();

        $guardian_obj->saveGuardianInfo($request);

        $action = "Registered " . $request->first_name . " " . $request->last_name . " as new student";
        $this->auditTrailEvent($request, $action);
        //$new_user = User::find($request->student_user_id);
        //$all_staff = User::where('role', 'staff')->get();
        //$user->notify(new NewRegistration($user));
        //Notification::send($all_staff, new NewRegistration($new_user));
        return 'Successful';
    }

    public function uploadBulkStudents(Request $request)
    {
        // return $request;
        // $school_id = $this->getSchool()->id;
        $request->admission_sess_id = $this->getSession()->id;
        $bulk_data = json_decode(json_encode($request->bulk_data));
        // $level_id = $request->level_id;
        // $class_teacher_id = $request->class_teacher_id;

        foreach ($bulk_data as $csvRow) {
            try {

                $request->last_name = trim($csvRow->SURNAME);
                $request->first_name = trim($csvRow->OTHER_NAMES);
                $request->gender = trim(strtolower($csvRow->GENDER));
                $request->dob = trim($csvRow->DOB);

                $request->admission_year    =   trim($csvRow->ADMISSION_YEAR);

                $request->fname             =   trim($csvRow->PARENT_FIRST_NAME);
                $request->lname             =   trim($csvRow->PARENT_LAST_NAME);
                $request->parent_phone      =   trim($csvRow->PARENT_PHONE_1);
                $request->parent_phone2     =   (isset($csvRow->PARENT_PHONE_2)) ? trim($csvRow->PARENT_PHONE_2) : NULL;
                $request->email             =   trim($csvRow->PARENT_EMAIL);
                $request->occupation        =   trim($csvRow->PARENT_OCCUPATION);
                $request->address           =   trim($csvRow->RESIDENTIAL_ADDRESS);
                $request->religion          =   trim($csvRow->RELIGION);

                //store the entry for this student
                $this->store($request);
            } catch (\Throwable $th) {
                // return response()->json($th);
            }
        }
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Student $stud_obj, $id)
    {
        $school_id = $this->getSchool()->id;
        $student = Student::with(['user', 'studentGuardian.guardian.user'])->find($id);
        $levels = $this->getLevels(); //Level::all();
        $level_array = ['' => 'Select Level'];
        foreach ($levels as $level) {
            $level_array[$level->id] = formatLevel($level->level);
        }

        $state_array = ['' => 'Select State'];
        $states = State::orderBy('name')->get();
        foreach ($states as $state) {
            $state_array[$state->id] = $state->name;
        }

        $admission_sessions = SSession::orderBy('id', 'DESC')->get();
        $session_array = [];
        foreach ($admission_sessions as $admission_session) {
            $session_array[$admission_session->id] = $admission_session->name;
        }
        $parent_username = $this->generateUsername($school_id, 'parent');


        return $this->render('core::students.edit', compact('student', 'levels', 'level_array', 'state_array', 'session_array', 'parent_username'));
    }

    /**
     * @param StudentRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, StudentsInClass $student_in_class)
    {
        // return $request;

        $student_in_class = StudentsInClass::with(['student.studentGuardian.guardian.user', 'student.user'])->find($student_in_class->id);
        $student = $student_in_class->student;
        $student_user = $student->user;
        $student_guardian = $student->studentGuardian;
        $guardian = $student_guardian->guardian;
        $guardian_user = $guardian->user;
        $sess_id = $this->getSession()->id;
        try {
            // update student details
            $student->saveStudentInfo($request, 'update');
            $student_user->saveUserAsStudent($request, 'update');

            // update parent info

            $student_guardian->relationship = $request->relation;
            $student_guardian->save();

            $guardian->occupation = $request->occupation;
            $guardian->save();
            $request->parent_user_id = $guardian_user->id;
            $guardian_user->saveUserAsParent($request, 'update');

            //add student to class
            $student_in_class->addStudentToClass($student->id, $request->class_teacher_id, $sess_id, $this->getTerm()->id, $this->getSchool()->id);


            //save guardian informaiton
            $guardian = new Guardian();

            $guardian->saveGuardianInfo($request);

            $student_in_class = StudentsInClass::with(['student.studentGuardian.guardian.user', 'student.user'])->find($student_in_class->id);
            return response()->json(compact('student_in_class'), 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['message' => $ex], 500);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Student $student)
    {
        $school_id = $student->school_id;
        $sess_id = $student->school->current_session;
        $term_id = $student->school->current_term;
        // $school_id = $student->school_id;
        // $sess_id = $this->getSession()->id;
        // $term_id = $this->getTerm()->id;
        $student = Student::with(['school.lga', 'studentGuardian.guardian.user', 'user.state', 'user.lga', 'myClasses' => function ($query) use ($school_id, $sess_id) {
            $query->where(['sess_id' => $sess_id, 'students_in_classes.school_id' => $school_id])->orderBy('id', 'DESC');
        }, 'myClasses.classTeacher.c_class',  'currentStudentLevel', 'myClasses.classTeacher.staff.user', 'myClasses.classTeacher.subjectTeachers.staff.user', 'behaviors', 'skills', 'results.subjectTeacher.subject'])->find($student->id);
        return $this->render(compact('student'));
    }

    // public function show(Student $student)
    // {
    //     try {
    //         $school = $this->getSchool();
    //         $user = $this->getUser();
    //         $sess_id = $this->getSession()->id;
    //         $term_id = $this->getTerm()->id;
    //         $can_edit = false;
    //         $id = $student->id;
    //         $student_in_class_obj = new StudentsInClass();


    //         // $student_in_class =  StudentsInClass::where([
    //         //                         'student_id' => $id,
    //         //                         'sess_id' => $sess_id,
    //         //                         'term_id' => $term_id,
    //         //                         //'school_id' => $school_id
    //         //                     ])->first();
    //         $student_in_class =  StudentsInClass::where('student_id', $id)->orderBy('id', 'DESC')->first();

    //         if (!$student_in_class) {

    //             return response()->json(['error' => 'Student not found'], 404);
    //         }
    //         if ($user->student) {
    //             if ($id == $user->student->id) {
    //                 $can_edit = true;
    //             }
    //         }
    //         //Get the student details for this student_id
    //         $student = $student->getStudentDetails($school, $id, $student_in_class->class_teacher_id);


    //         return  $this->render(compact('student', 'can_edit'));
    //     } catch (ModelNotFoundException $ex) {
    //         return response()->json(['Error ' => $ex->getMessage()], 404);
    //     }
    // }
    //this method is performed by the teacher
    public function assignments()
    {
        $classes = ['' => 'Select Class'] + CClass::where('school_id', $this->getSchool()->id)->pluck('name', 'id')->all();
        return  $this->render('core::students.assignments', compact('classes'));
    }

    //this is performed by the teacher
    public function getStudents($class_id)
    {
        $students = Student::where('class_id', $class_id)->get();
        $html = "";
        foreach ($students as $student) {
            $html .= "<option value='{$student->id}'>" . $student->first_name . ' ' . $student->last_name . "</option>";
        }
        return $html;
    }

    /*public function studentAssignments() {
        try {
            $assignments = Student::with('assignment')->findOrFail(request()->get('student_id'));
            return  $this->render('core::students.student_assignments', compact('assignments'));
        } catch (ModelNotFoundException $ex) {
            Flash::error('Error: ' . $ex->getMessage());
            return redirect()->url('students/assignments');
        }
    }*/

    public function studentTeachers(Student $stud, $id = NULL)
    {
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        $student_in_class_obj = new StudentsInClass();
        $request = request()->all();
        if (isset($request['student_id']) && $request['student_id'] != NULL) {
            $stud_id  = $request['student_id'];
            $parent_view = TRUE;
        }
        if ($this->getUser()->hasRole('student')) {
            $stud_id = $this->getStudent()->id;
            $parent_view = FALSE;
        }


        $student_in_class = $student_in_class_obj->fetchStudentInClass($stud_id,  $sess_id, $term_id, $school_id);

        if (!$student_in_class) {
            $message = "You are yet to be assigned a class ";

            return $this->render('errors.404', compact('message'));
        }
        $class_teacher_id = $student_in_class->class_teacher_id;

        //Get the student details for this student_id
        list($student, $parent, $class, $subjects) = $stud->getStudentDetails($stud_id, $class_teacher_id);



        //dd(DB::getQueryLog());
        return $this->render(
            'core::students.teachers',
            compact('student', 'parent', 'class', 'subjects', 'parent_view')
        );
    }

    //method to render student subjects
    public function studentSubjects(Student $stud)
    {
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        $student_in_class_obj = new StudentsInClass();
        $request = request()->all();
        if (isset($request['stud_id']) && $request['stud_id'] != NULL) {
            $stud_id  = $request['stud_id'];
            $parent_view = TRUE;
        }
        if ($this->getUser()->hasRole('student')) {
            $stud_id = $this->getStudent()->id;
            $parent_view = FALSE;
        }

        $student_in_class = $student_in_class_obj->fetchStudentInClass($stud_id,  $sess_id, $term_id, $school_id);

        if (!$student_in_class) {
            $message = "You are yet to be assigned a class ";

            return $this->render('errors.404', compact('message'));
        }
        $class_teacher_id = $student_in_class->class_teacher_id;
        list($student, $parent, $class, $subjects) = $stud->getStudentDetails($stud_id, $class_teacher_id);
        /*foreach ($subjects as $subject):

            $student_ids = $subject->student_ids;
            $student_id_array = explode('~', $student_ids);
            $subject->student_ids = $student_id_array;
        endforeach;*/

        return $this->render('core::students.subjects', compact('subjects', 'stud_id', 'student', 'parent_view', 'parent', 'class'));
    }


    public function promoteStudents(Request $request)
    {
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $former_session_id = $sess_id - 1;
        $term_id = $this->getTerm()->id;
        $student_in_class_obj = new StudentsInClass();

        $promote_student_ids = $request->promote_student_id; //in array form
        $promote_next_level_id = $request->promote_next_level_id;

        foreach ($promote_student_ids as $student_id) {
            if ($student_id != "All") {
                if ($promote_next_level_id == 'alumni') {
                    $alumni = Alumni::where(['school_id' => $school_id, 'student_id' => $student_id])->first();
                    if (!$alumni) {
                        $alumni = new Alumni();
                    }
                    $alumni->school_id = $school_id;
                    $alumni->student_id = $student_id;
                    $alumni->graduate_session = $former_session_id;
                    $alumni->save();
                    $student = Student::find($student_id);

                    $student->studentship_status = 'graduated';
                    $student->save();
                } else {
                    //we do this because of the select-all tage from the form

                    //let's get the student's former class
                    $student_in_class =   $student_in_class_obj->fetchStudentInClass($student_id, $former_session_id, $term_id, $school_id);

                    if ($student_in_class) {


                        $student = Student::find($student_id);

                        $student->current_level = $promote_next_level_id;

                        if ($student->save()) {
                            //perform this if the student had a class.
                            $class_teacher_id = $student_in_class->class_teacher_id;

                            $old_class = CClass::find($student_in_class->classTeacher->class_id);

                            $old_class_section = $old_class->section; //eg..A,B,C....we need this to assign the student a new class

                            //fetch the new class details
                            $new_class = CClass::where(['school_id' => $school_id, 'level' => $promote_next_level_id, 'section' => $old_class_section])->first();

                            if (!$new_class) {
                                // incase the sections are not named the same way
                                $new_class = CClass::where(['school_id' => $school_id, 'level' => $promote_next_level_id])->first();
                            }
                            $new_class_teacher = ClassTeacher::where(['school_id' => $school_id, 'level_id' => $promote_next_level_id, 'class_id' => $new_class->id])->first();
                            # code...
                            //assign the student a class

                            $student_in_class_obj->addStudentToClass($student_id, $new_class_teacher->id, $sess_id, $term_id, $school_id);
                        }
                    }
                }
            }
        }
        // Flash::success('Promotion Process Successful');

        return redirect()->route('students.index');
    }



    public function checkRegNum(Request $request)
    {
        $reg_no = $request->reg_no;

        $student = Student::where('registration_no', $reg_no)->get();

        if ($student->count() > 0) {

            return 'true';
        }
        return 'false';
    }

    public function studentDashboard()
    {


        $stud = $this->getStudent();
        $id = $this->getStudent()->id;
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        $student_in_class_obj = new StudentsInClass();


        $student_in_class = $student_in_class_obj->fetchStudentInClass($id, $sess_id, $term_id, $school_id);

        if ($student_in_class) {

            //Get the student details for this student_id
            list($student, $parent, $class, $subjects) = $stud->getStudentDetails($id, $student_in_class->class_teacher_id);
            //return $class->level;

            //return $parent->relationship;
            //echo $class->section->name;exit;
            //removing the array to make it a single string
            //$student = $student[0];
            $student->state = State::find($student->user->state_id);
            $student->lga = LocalGovernmentArea::find($student->user->lga_id);
            $student->feePaymentMonitor = $student->feePaymentMonitor()->orderBy('id', 'DESC')->get();

            return  $this->render('core::dashboard.student', compact('student', 'parent', 'class', 'subjects'));
        }
        // Flash::error('STUDENT CLASS DETAILS NOT FOUND');
        $message = "You are yet to be assigned a class. See the school Administrator. ";

        return $this->render('errors.404', compact('message'));
    }
    // public function toggleStudentNonPaymentSuspension(Request $request)
    // {
    //     $student_id = $request->student_id;
    //     $status = $request->status;
    //     $student = Student::find($student_id);
    //     $student->suspended_for_nonpayment = $status;
    //     $student->save();
    //     return 'success';
    // }

    public function toggleStudentshipStatus(Request $request)
    {
        $student_id = $request->student_id;
        $status = $request->status;
        $student = Student::find($student_id);
        $student->studentship_status = $status;
        $student->save();
        return 'success';
    }
}