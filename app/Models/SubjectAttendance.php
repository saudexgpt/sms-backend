<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectAttendance extends Model
{
    //
    protected $fillable = [
        'subject_teacher_id',
        'student_ids'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectTeacher() {
        return $this->belongsTo(SubjectTeacher::class);
    }

    /**
    * Method to fetch students attendance report for chart view for a particular date and class
    *@param $sess_id: Academic Session for which the admission report is to be fetched
    *@param $options: array of extra conditions for the query
    *@return $levels: containing number of males and females in a particular level
    */
    function fetchAttendanceReport($school_id, $date, $teacher_id=null)
    {   
        $subject_teachers = SubjectTeacher::where('school_id',$school_id)->get();
        if($teacher_id){
            $subject_teachers = SubjectTeacher::where(['school_id'=>$school_id, 'teacher_id'=>$teacher_id])->get();
        }
        
        if($subject_teachers != '[]'){
            foreach ($subject_teachers as $subject_teacher) {
                $attendance = SubjectAttendance::where( 'subject_teacher_id',$subject_teacher->id)
                ->whereDate('subject_attendances.created_at', '=', $date )->first();

                $subject_teacher->attendance_count = 0;
                if($attendance) {
                    
                    $student_ids = $attendance->student_ids;

                    $count = count(explode('~', $student_ids));

                    $subject_teacher->attendance_count = $count;
                }
                
                $subject_teacher->date = $date;
               
                
            }
            
            return $subject_teachers;
        }
        return null;

    }
}
