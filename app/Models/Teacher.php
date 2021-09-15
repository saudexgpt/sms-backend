<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Auth;

class Teacher extends Model
{


    /**
     *teacherSubjects() method
     *@param $teacher_id gotten from $staff_id of Staff table
     *@return $details
     */
    public function teacherSubjects($id, $school_id = null, $all = false)
    {
        if ($school_id == null) {
            $user_id = Auth::user()->id;
            $school_id = Staff::where('user_id', $user_id)->first()->school_id;
        }
        $details = SubjectTeacher::where(['teacher_id' => $id, 'school_id' => $school_id])->get();

        if ($all) {
            $details = SubjectTeacher::where('school_id', $school_id)->get();
        }

        foreach ($details as $detail) :
            $class_id = $detail->classTeacher->class_id;

            $class = CClass::find($class_id);
            $level = Level::find($class->level);

            $detail->class = $class;
            $detail->level = $level;
        endforeach;
        /*$class = $details->
            ->select('subject_teachers.id as id','subjects.id as subject_id', 'subjects.name as subject_name', 'subjects.code as subject_code', 'classes.name as class_name', 'sections.name as section_name', 'levels.level')
            ->get();*/
        return $details;
    }

    /**
     *teacherClasses() method
     *@param $teacher_id gotten from $staff_id of Staff table
     *@return $details
     */
    public function teacherClasses($id)
    {
        $details = CClass::join('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->join('levels', 'classes.level', '=', 'levels.id')
            ->where('class_teachers.teacher_id', $id)
            ->select('class_teachers.id as id', 'classes.id as class_id', 'classes.name as class_name', 'levels.level')
            ->get();
        return $details;
    }

    public function getClassId(array $options = [], $action = 'fetch')
    {
        if ($action == 'insert') {
            DB::table('class_teachers')->insert($options);
        }
        $teacher_class = CClass::join('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->where('class_teachers.school_id', $options['school_id'])
            ->where('class_teachers.class_id', $options['class_id'])
            ->where('class_teachers.section_id', $options['section_id'])
            ->where('class_teachers.level_id', $options['level_id'])
            ->select('class_teachers.id as class_teacher_id')
            ->get();

        return $teacher_class;
    }

    /**
     *teacherClassStudents() method
     *@param class_teachers.id as class_id in students table
     *@return $students
     */
    public function teacherClassStudents($class_teacher_id, $sess_id, $term_id, $school_id)
    {
        $students_in_class = StudentsInClass::with('student.user')->where([
            'class_teacher_id' => $class_teacher_id,
            'sess_id' => $sess_id,
            //'term_id'=>$term_id,
            'school_id' => $school_id
        ])->get();

        $students = [];

        foreach ($students_in_class as $student_in_class) :

            $student = $student_in_class->student;
            $student->skill = $student->skills()->where(['school_id' => $school_id, 'sess_id' => $sess_id, 'term_id' => $term_id])->first();

            $student->behavior = $student->behaviors()->where(['school_id' => $school_id, 'sess_id' => $sess_id, 'term_id' => $term_id])->first();
            $students[] = $student;
        // if ($student->studentship_status == 'active') {
        //     $students[] = $student;
        // }


        endforeach;


        return $students;
    }


    /**
     *subjectStudents() method
     *@param subject_teacher_id as $id
     *@return $students in array format
     */
    public function teacherSubjectStudents($class_teacher_id, $sess_id, $term_id, $school_id)
    {
        return $this->teacherClassStudents($class_teacher_id, $sess_id, $term_id, $school_id);
    }



    /**
     *allMarkedAttendance() method
     *@param array $options with 'option', 'fromDate', 'toDate' and 'id' as keys
     *@return $marked students
     */
    public function allMarkedAttendance(array $options = [])
    {

        if ($options['option'] == 'class') {

            $marked =  ClassAttendance::where('class_teacher_id', $options['id'])
                ->where('student_ids', '!=', NULL)
                ->whereDate('created_at', '=', todayDate())
                ->select('id', 'student_ids', 'created_at as date')
                ->get();
            return $marked;
        }
        $marked = SubjectAttendance::where('subject_teacher_id', $options['id'])
            ->where('student_ids', '!=', NULL)
            ->whereDate('created_at', '=', todayDate())
            ->select('id', 'student_ids', 'created_at as date')
            ->get();
        return $marked;
    }
}
