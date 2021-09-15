<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    protected $fillable = [
        'school_id',
        'class_teacher_id',
        'subject_teacher_id',
        'teacher_id',        
        'day',
        'start',
        'end',
        'all_day'
    ];

    public function subjectTeacher() {
        return $this->belongsTo(SubjectTeacher::class);
    }

    
    public function classTeacher() {
        return $this->belongsTo(ClassTeacher::class);
    }
    public function school() {
        return $this->belongsTo(School::class);
    }

    public function timeTable($id)
    {
        $options = [];
        $subject_teachers = SubjectTeacher::where('teacher_id', $id)->get();
        foreach ($subject_teachers as $subject_teacher) :
            $subject_teacher_id = $subject_teacher->id;
            $data = Routine::where('subject_teacher_id', $subject_teacher_id)->get();
            //$events = [];
            foreach ($data as $record) :
                $subject = Subject::find($record->subjectTeacher->subject_id);
                $class = CClass::find($record->classTeacher->class_id);
                $options[] =  array(
                    'id' => $record->id,
                    'subject_id' => $record->subject_teacher_id,
                    'title' => $subject->name.' ('.$class->name.')',
                    'start' => $record->start,
                    'background_color' => $record->subjectTeacher->subject->color_code,
                    'end' => $record->end,
                    'dow' => $record->day
                );
            endforeach;
           //$options[] = $events;
        endforeach;
        
        return $routines =  response()->json([
            'events' => $options
        ]);
    }
}
