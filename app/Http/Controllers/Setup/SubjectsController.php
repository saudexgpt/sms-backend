<?php

namespace App\Http\Controllers\Setup;


use App\Http\Requests\SubjectRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CClass;
use App\Models\ClassTeacher;
use App\Models\Curriculum;
use App\Models\CurriculumLevelGroup;
use App\Models\Level;
use App\Models\Material;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\StudentsOfferingSubject;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;

class SubjectsController extends Controller
{
    // public function necessaryParams()
    // {
    //     $school = $this->getSchool();
    //     $school_id = $school->id;
    //     $sess_id = $this->getSession()->id;
    //     $term_id = $this->getTerm()->id;

    //     $subjectTeacher = new SubjectTeacher();

    //     $subject_teachers = SubjectTeacher::with('classTeacher.c_class', 'staff', 'subject')->where('school_id', $school_id)->get();

    //     $subjects = $subjectTeacher->getSubjectTeachers($subject_teachers);

    //     //this is necessary for assigning subject teacher via bootstrap modal
    //     $classTeacher = new ClassTeacher();
    //     $class_teachers = ClassTeacher::where('school_id', $school_id)->get();

    //     $classes = $classTeacher->getClassTeachers($class_teachers, $sess_id, $term_id, $school_id);

    //     $level_groups = CurriculumLevelGroup::where('curriculum', $school->curriculum)->get();

    //     $level_group_array = [];
    //     foreach ($level_groups as $level_group) {
    //         $level_group_array[$level_group->id] = $level_group->name;
    //     }
    //     $levels = $this->getLevels(); //Level::get();
    //     $level_array = [];
    //     foreach ($levels as $level) {
    //         $level_array[$level->id] = formatLevel($level->level);
    //     }

    //     //this is necessary since we would want to assign class to PC teachers from here
    //     $teachers = [];

    //     $staff_roles = StaffRole::where(['school_id' => $school_id, 'role' => 'teacher'])->get();
    //     foreach ($staff_roles as $staff_role) {
    //         $teachers[] = $staff_role->staff;
    //     }
    //     return array($subjects, $classes, $level_array, $level_group_array, $teachers);
    // }

    // public function manageSubject()
    // {
    //     $subjects = Subject::where('school_id', $this->getSchool()->id)->get();
    //     $levels = $this->getLevels(); //Level::get();
    //     $level_array = [];
    //     foreach ($levels as $level) {
    //         $level_array[$level->id] = formatLevel($level->level);
    //     }

    //     return $this->render('core::subjects.manage', compact('subjects', 'level_array'));
    // }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $school = $this->getSchool();
        // $curriculum_level_group_id = $request->curriculum_level_group_id;
        $level_groups = CurriculumLevelGroup::with(['subjects' => function ($q) use ($school) {
            $q->orderBy('id', 'DESC')->where('school_id', $school->id);
        }])->where('curriculum', $school->curriculum)->get();
        return $this->render(compact('level_groups'));
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function getLevelSubjects(Request $request)
    // {
    //     $school_id = $this->getSchool()->id;
    //     $curriculum_level_group_id = $request->curriculum_level_group_id;

    //     $all_subjects =  Subject::with('levelGroup')->where(['school_id' => $school_id, 'curriculum_level_group_id' => $curriculum_level_group_id])->get();

    //     return $this->render('core::subjects.level_subjects', compact('all_subjects'));
    // }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function viewAllSubjects()
    // {
    //     list($subjects, $classes, $level_array, $level_group_array, $teachers) = $this->necessaryParams();

    //     //return $subjects[0]->class->name;
    //     return $this->render('core::subjects.all_subjects', compact('subjects', 'classes', 'level_array', 'teachers'));
    // }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function create()
    // {
    //     list($subjects, $classes, $level_array, $level_group_array, $teachers) = $this->necessaryParams();


    //     return $this->render('core::subjects.create', compact('subjects', 'classes', 'level_array', 'teachers'));
    // }

    public function fetchTeacherSubject(Request $request)
    {
        $school_id = $this->getSchool()->id;
        $level_id = $request->level_id;
        $class_teacher_id = $request->class_teacher_id;
        $level = Level::find($level_id);

        $subjects = Subject::where(['school_id' => $school_id, 'curriculum_level_group_id' => $level->curriculum_level_group_id])->get(); //$level->levelGroup->subjects;
        foreach ($subjects as $subject) {
            $subject_teacher = SubjectTeacher::where(['class_teacher_id' => $class_teacher_id, 'subject_id' => $subject->id, 'school_id' => $school_id])->first();

            if (!$subject_teacher) {
                $subject_teacher = new SubjectTeacher();
                $subject_teacher->school_id = $school_id;
                $subject_teacher->subject_id = $subject->id;
                $subject_teacher->teacher_id = null;
                $subject_teacher->class_teacher_id = $class_teacher_id;
                $subject_teacher->save();
            }
        }
        $subject_teachers = SubjectTeacher::with(['subject' => function ($q) {
            $q->orderBy('name');
        }])->where(['class_teacher_id' => $class_teacher_id, 'school_id' => $school_id])->get();
        $staff = Staff::with('user')->where(['school_id' => $school_id])->get();

        return $this->render(compact('subject_teachers', 'staff'));
    }
    /**
     * @param SubjectRequest $request
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Subject $subject)
    {

        $name = $request->name;
        $subject->code = $request->code;
        $level_groups = $request->level_group;

        foreach ($level_groups as $level_group) {
            $subject = Subject::where(['school_id' => $this->getSchool()->id, 'curriculum_level_group_id' => $level_group, 'name' => $name])->first();



            if (!$subject) {
                $subject = new Subject();
                //$subject->subject_group = $request->subject_group;

            }
            $subject->name = $request->name;
            $subject->code = $request->code;
            $subject->curriculum_level_group_id = $level_group;
            $subject->school_id = $this->getSchool()->id;
            $subject->is_mock = $request->is_mock;
            $subject->color_code = randomColorCode(); //this is from helpers in form of '#FFFFFF'
            $subject->save();
        }
        // Flash::success('Subject information added successfully');
        return $this->index();
    }

    /**
     * @param SubjectRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $subject->update($request->all());
        return $subject;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAssignSubject(Subject $subject, Teacher $teacher, CClass $class_object)
    {


        $subject_teachers = $teacher->teacherSubjects(null, $this->getSchool()->id, true);
        foreach ($subject_teachers as $subject_teacher) {
            $teacher_id = $subject_teacher->teacher_id;
            $teacher = Staff::find($teacher_id);
            $subject_teacher->teacher = $teacher->user;
        }
        //get subjects
        $subjects = ['' => 'Select Subject'];
        $data =  Subject::where('school_id', $this->getSchool()->id)->get();

        foreach ($data as $subject) {
            $subjects[$subject->id] = $subject->name . ' (' . $subject->code . ')';
        }
        //get classess
        $curriculum_array = explode('~', $this->getSchool()->curriculum);
        $class_data = $class_object->getCurriculumClasses($this->getSchool(), $curriculum_array);
        //echo $class_teachers[0]->teacher->first_name;exit;
        $classes = ['' => 'Select Class'];

        foreach ($class_data as $class) {
            $classes[$class->id] = $class->name;
        }

        //get teachers
        $teachers = [];
        $data = Staff::where('school_id', $this->getSchool()->id)->get();
        foreach ($data as $teacher) {
            $teachers[$teacher->id] = $teacher->user->first_name . ' ' . $teacher->user->last_name;
        }
        return $this->render('core::subjects.assign', compact('subjects', 'teachers', 'subject_teachers', 'classes'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignSubject(Request $request, SubjectTeacher $subject_teacher)
    {
        // if (isset($request->teacher_id)) {
        // remove the assignment if it was already assigned to this teacher
        // if ($request->teacher_id === $subject_teacher->teacher_id) {
        //     $subject_teacher->teacher_id = null;
        // } else {

        //     $subject_teacher->teacher_id = $request->teacher_id;
        // }
        $subject_teacher->teacher_id = $request->teacher_id;
        $subject_teacher->save();
        return 'teacher:  ' . $request->teacher_id;
        // }
    }

    public function subjects()
    {
        $subjects = Subject::join('classes', 'subjects.level_id', '=', 'classes.level')
            ->join('students', 'classes.id', '=', 'students.class_id')
            ->join('subject_teacher', 'subjects.id', '=', 'subject_teacher.subject_id')
            ->join('teachers', 'subject_teacher.teacher_id', '=', 'teachers.id')
            ->where('students.id', auth()->user()->account_holder_id)
            ->select('subjects.id', 'subjects.name', 'teachers.first_name', 'teachers.last_name')->get();
        return $this->render('core::subjects.subjects', compact('subjects'));
    }

    public function materials($subjectId)
    {
        $subject = SubjectTeacher::find($subjectId);

        $materials = Material::where(['school_id' => $this->getSchool()->id, 'subject_teacher_id' => $subjectId])->get();

        $curricula = Curriculum::where(['school_id' => $this->getSchool()->id, 'subject_teacher_id' => $subjectId])->get();
        return $this->render('core::subjects.materials', compact('materials', 'curricula', 'subject'));
    }

    public function curriculum($subjectId)
    {
        $curricula = Curriculum::where('subject_id', $subjectId)->get();
        return $this->render('core::subjects.curricula', compact('curricula'));
    }

    public function addSubjectStudents(Request $request)
    {
        $sess_id = $this->getSession()->id;
        $term_id = $this->getTerm()->id;
        $school_id = $this->getSchool()->id;

        $class = $request->class;
        $subject = $request->subject;
        $subject_teacher_id = $request->subject_teacher_id;

        $student_offering_subjects = StudentsOfferingSubject::where([
            'subject_teacher_id' => $subject_teacher_id,
            'sess_id' => $sess_id,
            'term_id' => $term_id,
            'school_id' => $school_id
        ])->first();

        //in array format so we implode to string
        $student_ids = implode('~', $request->student_ids);
        $existing_subject_students = "";
        if ($student_offering_subjects) {
            $existing_subject_students = $student_offering_subjects->student_ids;
        } else {
            $student_offering_subjects = new StudentsOfferingSubject();
            $student_offering_subjects->sess_id = $sess_id;
            $student_offering_subjects->term_id = $term_id;
            $student_offering_subjects->school_id = $school_id;
            $student_offering_subjects->subject_teacher_id = $subject_teacher_id;
        }


        $new_student_ids = addSingleElementToString($existing_subject_students, $student_ids);

        $student_offering_subjects->student_ids = $new_student_ids;

        $student_offering_subjects->save();

        //return 'success';

        return redirect()->route('teacher_subject_students', ['subject' => $subject, 'class' => $class, 'id' => $subject_teacher_id]);
    }
}
