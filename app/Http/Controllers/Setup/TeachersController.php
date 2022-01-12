<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;

use App\Models\Assignment;
use App\Models\CClass;
use App\Models\Qualification;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\SubjectTeacher;
use App\Models\ClassTeacher;
use App\Models\StudentsInClass;
use App\Models\Behavior;
use App\Models\Skill;
use App\Models\Term;
use App\Models\Result;
use App\Http\Requests\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $teachers = Teacher::where(function ($query) {
            if (in_array($this->role, $this->roles)) {
                $query->where('school_id', $this->getSchool()->id);
            }
        })->get();
        return $this->render('core::teachers.index', compact('teachers'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $sRecords = Subject::where('school_id', $this->getSchool()->id)->get();
        $subjects = [];
        foreach ($sRecords as $record) {
            $subjects[$record->id] = $record->name . " (" . $record->code . ")";
        }
        $classes = CClass::where('school_id', $this->getSchool()->id)->pluck('name', 'id');


        return $this->render('core::staff.create', compact('school'));
    }

    /**
     * @param Request $request
     * @param Teacher $teacher
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Teacher $teacher)
    {
        $inputs = $request->all();
        $mime = $request->file('avatar')->getClientMimeType();
        $name = "teacher_" . time() . "." . $request->file('avatar')->guessClientExtension();
        $avatar = $request->file('avatar')->storeAs('teachers', $name, "public");
        $inputs['avatar'] = $avatar;
        $inputs['mime'] = $mime;
        $inputs['school_id'] = $this->getSchool()->id;
        $teacher = $teacher->create($inputs);

        // Attach/Detach subjects
        $teacher->subjects()->sync($inputs['subjects']);

        // Attach/Detach classes
        $teacher->classes()->sync($inputs['classes']);

        // Add teacher qualifications
        foreach ($inputs['degree'] as $key => $degree) {
            $teacher->qualifications()->create([
                'degree_title' => $degree,
                'institution' => isset($inputs['institution'][$key]) ? $inputs['institution'][$key] : '',
                'result' => isset($inputs['result'][$key]) ? $inputs['result'][$key] : '',
                'passing_year' => isset($inputs['passing_year'][$key]) ? $inputs['passing_year'][$key] : '',
            ]);
        }

        // Create parent account
        $teacher->user()->create([
            'email' => $inputs['email'],
            'password' => $inputs['password'],
            'account_holder_type' => 'teacher'
        ]);

        Flash::success('Teacher information added successfully');
        return redirect()->route('teachers.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $sRecords = Subject::where('school_id', $this->getSchool()->id)->get();
            $subjects = [];
            foreach ($sRecords as $record) {
                $subjects[$record->id] = $record->name . " (" . $record->code . ")";
            }
            $classes = CClass::where('school_id', $this->getSchool()->id)->pluck('name', 'id');
            // Pivot
            $sClasses = [];
            foreach ($teacher->classes as $class) {
                $sClasses[] = $class->pivot->class_id;
            }

            $sSubjects = [];
            foreach ($teacher->subjects as $subject) {
                $sSubjects[] = $subject->pivot->subject_id;
            }


            return $this->render('core::teachers.edit', compact('teacher', 'subjects', 'classes', 'sClasses', 'sSubjects', 'countries'));
        } catch (ModelNotFoundException $ex) {
            Flash::error('Error: ' . $ex->getMessage());
            return redirect()->route('teachers.index');
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $inputs = $request->all();

            if ($request->hasFile('avatar')) {
                // Unlink the old image
                Storage::disk('public')->delete($teacher->avatar);

                $mime = $request->file('avatar')->getClientMimeType();
                $name = "teacher_" . time() . "." . $request->file('avatar')->guessClientExtension();
                $logo = $request->file('avatar')->storeAs('teachers', $name, "public");
                $inputs['avatar'] = $logo;
                $inputs['mime'] = $mime;
            }
            $teacher->update($inputs);

            // Attach/Detach subjects
            $teacher->subjects()->sync($inputs['subjects']);

            // Attach/Detach classes
            $teacher->classes()->sync($inputs['classes']);

            // Add teacher qualifications
            // Remove old qualification data
            Qualification::where('teacher_id', $teacher->id)->delete();
            foreach ($inputs['degree'] as $key => $degree) {
                $teacher->qualifications()->create([
                    'degree_title' => $degree,
                    'institution' => isset($inputs['institution'][$key]) ? $inputs['institution'][$key] : '',
                    'result' => isset($inputs['result'][$key]) ? $inputs['result'][$key] : '',
                    'passing_year' => isset($inputs['passing_year'][$key]) ? $inputs['passing_year'][$key] : '',
                ]);
            }

            if ($inputs['password'] != "") {
                $user = $teacher->user;
                $user->email = $inputs['email'];
                $user->password = $inputs['password'];
                $user->save();
            }
            Flash::success('Teacher information updated successfully');
            return redirect()->route('teachers.index');
        } catch (ModelNotFoundException $ex) {
            Flash::error('Error: ' . $ex->getMessage());
            return redirect()->route('teachers.index');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            return $this->render('core::teachers.show', compact('teacher'));
        } catch (ModelNotFoundException $ex) {
            Flash::error('Error: ' . $ex->getMessage());
            return redirect()->route('teachers.index');
        }
    }

    public function assignments(Assignment $assign)
    {
        $assignments = Assignment::join('subject_teachers', 'assignments.subject_teacher_id', '=', 'subject_teachers.id')
            ->where('assignments.school_id', $this->getSchool()->id)
            ->orderBy('assignments.id', 'DESC')
            ->select('assignments.id', 'subject_teachers.id as subject_teacher_id', 'deadline', 'download_link', 'is_marked', 'assignments.created_at')
            ->get();
        $assignments = $assign->teacherAssignmentsDetails($assignments);
        return $this->render('core::teachers.assignments', compact('assignments'));
    }

    public function classes()
    {
        $request = request()->all();
        $dashboard_view = 0;
        if (isset($request['dashboard']) && $request['dashboard'] == '1') {
            $dashboard_view = 1;
        }
        $teacher = new Teacher();

        $id = $this->getStaff()->id;

        $details = $teacher->teacherClasses($id);

        return $this->render('core::teachers.classes', compact('details', 'dashboard_view'));
    }

    public function subjects()
    {
        $teacher = new Teacher();

        $id = $this->getStaff()->id;

        $school_id = $this->getSchool()->id;
        $details = $teacher->teacherSubjects($id, $school_id);
        foreach ($details as $detail) {
            //
            $student_ids = StudentsInClass::where('class_teacher_id', $detail->class_teacher_id)->pluck('student_ids')->first();
            $ids = explode("~", $student_ids);

            $count_student = count($ids); // - 1;
            $detail->student_count = $count_student;
        }
        $subjects = $this->getStudents($id);
        return $this->render('core::teachers.subjects', compact('details', 'subjects'));
    }

    private function getAnalysis($subject_id)
    {
        $terms = Term::select('name', 'id')->get();
        $this->good = 0;
        $results = array();
        foreach ($terms as $term) {
            $result = array();
            $result['name'] = $term->name;
            $result['average'] = Result::where('term_id', $term->id)
                ->where('subject_teacher_id', $subject_id)
                ->avg('total');

            $result['average'] = $result['average'] == null ? 0 : $result['average'];

            Result::where('term_id', $term->id)
                ->where('subject_teacher_id', $subject_id)
                ->each(
                    function ($item, $key) use ($result) {
                        if ($item['total'] > $result['average']) {
                            $this->good += 1;
                        }
                        return true;
                    }
                );
            array_push($results, $result);
        }
        return $results;
    }

    private $good = 0;

    function analyse($totals)
    {
        $result = array();
        $fails = 0;
        $least = $this->getLeastGrade();
        if ($totals->count() == 0)
            return ['average' => 0, 'fails' => 0];
        $sum = 0;
        foreach ($totals as $total) {
            if ($total <= $least)
                $fails += 1;

            $sum += $total;
        }
        $result['average'] = $sum / $totals->count();
        $result['fails'] = $fails;
        return $result;
    }

    function getLeastGrade()
    {
        //        ->take( 1 )->get()->pluck("grade_range");
        //        $range = Grade::all()->sortBy( 'grade_point' )
        $range = Grade::all()->sortBy('grade_point')->take(1)->pluck('grade_range');
        $ranges = explode('-', $range);
        return $ranges[1];
        //        return 0;
    }

    function average($totals)
    {
        if ($totals->count() == 0)
            return 0;
        $sum = 0;
        foreach ($totals as $total)
            $sum += $total;
        return $sum / $totals->count();
    }

    private function getStudents($id)
    {
        $subjects = array();
        $teacher = new Teacher();
        $school_id = $this->getSchool()->id;
        $details = $teacher->teacherSubjects($id, $school_id);
        foreach ($details as $detail) {
            if ($detail->subject) {
                $student_ids = StudentsInClass::where('class_teacher_id', $detail->class_teacher_id)
                    ->pluck('student_ids')->first();
                $ids = explode("~", $student_ids);
                $subject = array();
                $subject['name'] = $detail->subject->name;
                $subject['class'] = $detail->classTeacher->c_class->name;
                $subject['id'] = $detail->id;
                $subject['result'] = $this->getAnalysis($detail->id);
                $subject['good'] = $this->good;
                $subject['student_count'] = count($ids) - 1;
                array_push($subjects, $subject);
            }
            //          $class_teacher_id = SubjectTeacher::where( 'teacher_id', '=', $id )->where( 'subject_id', '=', $subject->id )->pluck( 'class_teacher_id' )->first();

        }
        //        dd( $subjects );
        return $subjects;
    }

    public function classStudents($id)
    {
        $school_id = $this->getSchool()->id;
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;

        $request = request()->all();

        $sesssion_name = $this->getSession()->name;

        $teacher = new Teacher();

        $class = ClassTeacher::find($id)->c_class;

        list($student_ids_arr, $students) = $teacher->teacherClassStudents($id, $sess_id, $term_id, $school_id);

        if (isset($request['view_only']) && $request['view_only'] == '1') {
            return $this->render('core::teachers.class_students_views', compact('students', 'class', 'id', 'sesssion_name'));
        }
        return $this->render('core::teachers.class_students', compact('students', 'class', 'id', 'sesssion_name', 'sess_id', 'term_id'));
    }

    public function subjectStudents($subject_teacher_id)
    {
        $teacher = new Teacher();

        $subject_teacher = SubjectTeacher::find($subject_teacher_id);
        $class_teacher_id = $subject_teacher->class_teacher_id;

        $class = CClass::find($subject_teacher->classTeacher->class_id);

        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        $school_id = $this->getSchool()->id;
        list($student_ids_arr, $students) = $teacher->teacherSubjectStudents($class_teacher_id, $sess_id, $term_id, $school_id);
        /*return $students;
        $student_array = [];
        foreach ($subject_class_students as $student) :

            //fetch students that have not been registered already
            if (!in_array($student->id, $student_ids_arr)) {
               $student_array[$student->id] = $student->user->first_name.' '.$student->user->last_name;
            }

        endforeach;*/

        return $this->render('core::teachers.subject_students', compact('students', 'class', 'subject_teacher'));
    }

    public function teachers()
    {
        //DB::enableQueryLog();
        $class = Student::find(auth()->user()->account_holder_id);
        $teachers = Teacher::join('class_teacher', 'teachers.id', '=', 'class_teacher.teacher_id')
            ->join('subject_teacher', 'teachers.id', '=', 'subject_teacher.teacher_id')
            ->join('subjects', 'subject_teacher.subject_id', '=', 'subjects.id')
            ->where('class_teacher.class_id', $class->class_id)
            ->where('subjects.level', $class->level)
            ->select('teachers.id', 'teachers.email', 'teachers.first_name', 'teachers.last_name', 'subjects.name as subject_name')
            ->get();
        //dd($teachers);
        //dd(DB::getQueryLog());
        return $this->render('core::teachers.class_teachers', compact('teachers'));
    }
}
