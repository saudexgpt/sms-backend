<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sess()
    {
        return $this->belongsTo(SSession::class, 'sess_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(ClassTeacher::class);
    }

    public function subjectTeacher()
    {
        return $this->belongsTo(SubjectTeacher::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'recorded_by', 'id');
    }

    //
    public function studentResult($sess_id, $student_id, $reg_no, $school_id, $term_id, $subject_teacher_id, $class_teacher_id, $teacher_id)
    {
        $result = Result::where(
            [
                'sess_id' => $sess_id,
                'student_id' => $student_id,
                'school_id' => $school_id,
                'class_teacher_id' => $class_teacher_id,
                'term_id' => $term_id,
                'subject_teacher_id' => $subject_teacher_id
            ]
        )->first();


        if (!$result) {
            $result = new Result();
            $result->school_id = $school_id;
            $result->student_id = $student_id;
            $result->reg_no = $reg_no;
            $result->sess_id = $sess_id;
            $result->class_teacher_id = $class_teacher_id;
            $result->recorded_by = $teacher_id;
            $result->term_id = $term_id;
            $result->subject_teacher_id = $subject_teacher_id;
            //other fields are nullable
            $result->save();
        }




        return $result;
    }

    public function processResultInfo($student_grade, $grades)
    {

        $ca1 =  $student_grade->ca1;
        $ca2 =  $student_grade->ca2;
        $ca3 =  $student_grade->ca3;

        $test = $this->addScores($ca1, $ca2, $ca3);

        $exam =  $student_grade->exam;


        $total = $this->addScores($test, $exam);

        list($result_grade, $color, $grade_point) = $this->resultGrade($total, $grades);


        return array($test, $total, $result_grade, $color, $grade_point);
    }


    public function addScores($score1, $score2, $score3 = null)
    {
        if ($score1 == null && $score2 == null && $score3 == null) {
            $total = null;
            return $total;
        }
        if ($score1 == null) {
            $score1 = 0;
        }
        if ($score2 == null) {
            $score2 = 0;
        }
        if ($score3 == null) {
            $score3 = 0;
        }
        $total = $score1 + $score2 + $score3;
        return $total;
    }

    public function resultRemark($student_grade, $grades)
    {
        $remark = "Undefined";
        if (!empty($grades)) {
            foreach ($grades as $grade_detail) {

                if ($student_grade == $grade_detail->grade) {

                    $remark = $grade_detail->interpretation;
                }
            }
        }
        return $remark;
    }

    public function resultGrade($total, $grades)
    {
        $grade = 'F';
        $color = '#F00000';
        $grade_point = 0;
        if (!empty($grades)) {
            foreach ($grades as $grade_detail) {
                //grade range (example 80-100)
                $higher_point = $grade_detail->upper_limit;
                $lower_point = $grade_detail->lower_limit;
                //cast the grade_range to array
                /*$grades_range_array = explode('-', $grade_range);
				$lower_point =  $grades_range_array[0];
				$higher_point = $grades_range_array[1];*/
                if ($higher_point < 100) {
                    //To round a number down e.g 85.99 = 85.0
                    $higher_point = floor($higher_point) + 0.99;
                }

                if ($total == null) {
                    $grade = null;
                    $color = null;
                    $grade_point = null;
                } elseif ($total <= $higher_point and $total >= $lower_point) {
                    $grade = $grade_detail->grade;
                    $color = $grade_detail->color_code;
                    $grade_point = sprintf("%01.2f", $grade_detail->grade_point);

                    break;
                }
            }
        }


        return array($grade, $color, $grade_point);
    }





    public function resultStatus($grade, $test_score, $exam_score)
    {
        if ($grade != 'F' /*AND $grade != 'E'*/) {
            $status = "Pass";
        } else {
            $status = "Fail (Repeat)";
        }
        return $status;
    }

    public function resultOverflow($test_score, $exam_score, $ca_score_limit)
    {
        if (
            $test_score > $ca_score_limit || $exam_score > (100 - $ca_score_limit)
        ) {
            return true;
        }
        return false;
    }

    function emptyScore($test_score, $exam_score)
    {
        if ($test_score == "" || $exam_score == "") {
            return true;
        }
        return false;
    }

    private function checkTestAbsence($test_score)
    {
        if ($test_score == '' || $test_score == '-') {
            $test_score = 'No-Score';
        } else {
            $test_score = $test_score;
        }
        return $test_score;
    }

    private function  checkExamAbsence($exam_score)
    {
        if ($exam_score == '' || $exam_score == '-') {
            $exam_score = 'No-Score';
        } else {
            $exam_score = $exam_score;
        }
        return $exam_score;
    }




    //This ensures that once a result is submitted or approved no more editing is made
    public function getAllResultActions($result_actions)
    {
        $decoded_action = json_decode($result_actions);
        $half_status = $decoded_action->half;
        $full_status = $decoded_action->full;

        $edit_ca1 = $edit_ca2 = $edit_ca3 = $edit_exam = true;
        if ($half_status == 'submitted' || $half_status == 'approved' || $half_status == 'published') {
            $edit_ca1 = false;
            $edit_ca2 = false;
        }
        if ($full_status == 'submitted' || $full_status == 'approved' || $full_status == 'published') {
            $edit_ca3 = false;
            $edit_exam = false;
        }
        return array($edit_ca1, $edit_ca2, $edit_ca3, $edit_exam);
    }

    //This ensures that once a result is submitted or approved no more editing is made
    public function resultStatusAction($result_actions)
    {
        $view_half = $view_full =  'false';
        $status_half = $status_full = 'Not Submitted';
        if ($result_actions != null) {
            $decoded_action = json_decode($result_actions);
            $half = $decoded_action->half;
            $full = $decoded_action->full;



            if ($half == 'submitted' || $half == 'approved' || $half == 'published') {
                $view_half = 'true';
                $status_half = $half;
            }
            if ($full == 'submitted' || $full == 'approved' || $full == 'published') {
                $view_full = 'true';
                $status_full = $full;
            }
            if ($half == 'rejected') {
                $status_half = $half;
            }
            if ($full == 'rejected') {
                $status_full = $full;
            }
        }
        return array($view_half, $view_full, $status_half, $status_full);
    }

    public function subjectStudentPerformance($result_details)
    {
        $subject_class_average = 0;
        $male_average = 0;
        $female_average = 0;
        $subject_highest_score = 0;
        $subject_lowest_score = 100;

        $male_total_score = 0;
        $female_total_score = 0;
        $total_score = 0;
        $male_count = 0;
        $female_count = 0;
        $count = 0;
        $subject_totals = [];
        foreach ($result_details as $result_detail) {
            $count++;
            if ($result_detail->total > $subject_highest_score) {
                $subject_highest_score = $result_detail->total;
            }

            if ($result_detail->total < $subject_lowest_score) {
                $subject_lowest_score = $result_detail->total;
            }

            if (strtolower($result_detail->student->user->gender) == 'male') {
                $male_count++;

                $male_total_score += $result_detail->total;
            }

            if (strtolower($result_detail->student->user->gender) == 'female') {
                $female_count++;
                $female_total_score += $result_detail->total;
            }

            $total_score += $result_detail->total;
            $subject_totals[] = $result_detail->total;
        }
        if ($male_count > 0) {
            $male_average = $male_total_score / $male_count;
        }

        if ($female_count > 0) {
            $female_average = $female_total_score / $female_count;
        }
        if ($count > 0) {
            $subject_class_average = sprintf("%01.1f", $total_score / $count);
        }


        return array($subject_class_average, $subject_highest_score, $subject_lowest_score, $male_average, $female_average, $subject_totals);
    }

    public function processStudentResults($student_results, $options)
    {
        $total_subject_class_average = 0;
        $total_student_score = 0;
        $result_count = 0;
        foreach ($student_results as $student_result) :

            $subject_teacher_id = $student_result->subject_teacher_id;
            //$student_result->user = Student::find($student_result->student_id)->user;
            //$subject_teacher = SubjectTeacher::find($subject_teacher_id);

            //$student_result->teacher = User::find($student_result->teacher->user_id);

            //$student_result->subject = $subject_teacher->subject;

            $action_term = 'actions_term_' . $options['term'];
            $grades = $options['grades'];
            $result_action = ResultAction::where([
                'sess_id' => $options['sess_id'],
                'school_id' => $options['school_id'],
                'subject_teacher_id' => $subject_teacher_id
            ])->first();

            if ($result_action) {
                $student_result->result_action_array = $this->resultStatusAction($result_action->$action_term);
            }
            //$total_for_avg = $total_for_avg+$student_result->total;
            list($test, $total, $result_grade, $color, $grade_point) = $this->processResultInfo($student_result, $grades);


            //fetch the performance of students for each subject in this class
            $subject_result_details = Result::where([
                'subject_teacher_id' => $subject_teacher_id,
                'school_id' => $options['school_id'],
                'sess_id' => $options['sess_id'],
                'term_id' => $options['term']
            ])->get();

            list($subject_class_average, $subject_highest_score, $subject_lowest_score, $male_average, $female_average, $subject_totals) = $this->subjectStudentPerformance($subject_result_details);

            $student_result->test = $test;
            $student_result->result_grade = $result_grade;
            $student_result->color = $color;
            $student_result->grade_point = $grade_point;
            $student_result->remark = $this->resultRemark($result_grade, $grades);
            $student_result->subject_class_average = $subject_class_average;
            $student_result->subject_highest_score = $subject_highest_score;
            $student_result->subject_lowest_score = $subject_lowest_score;
            $student_result->male_average = $male_average;
            $student_result->female_average = $female_average;
            $student_result->subject_totals = $subject_totals;
            if ($total != null) {
                $result_count++;
                $total_subject_class_average += $subject_class_average;
                $total_student_score += $total;
            }
            $student_result->position = rankResult($total, $subject_totals, 'position');
        endforeach;
        return array($student_results, $total_subject_class_average, $total_student_score, $result_count);
    }

    public function analyseStudentsResult($student, $options)
    {
        $grades = $options['grades']; // $this->getGrades();//Grade::where('school_id', $school_id)->get();//Grade::all();

        $subjects = [];
        $student_id = $student->id;
        $class_teacher_id = $options['class_teacher_id'];
        $school_id = $options['school_id'];
        $sess_id = $options['sess_id'];
        $term_id = $options['term'];
        $sub_term = $options['sub_term'];


        $result_details = Result::with('subjectTeacher.subject')->where(
            [
                'class_teacher_id' => $class_teacher_id,
                'school_id' => $school_id,
                'sess_id' => $sess_id,
                'term_id' => $term_id,
                'student_id' => $student_id
            ]
        )->orderBy('subject_teacher_id')->get();
        $result_details_array = [];
        if ($result_details->isNotEmpty()) {

            $total_score = 0;

            $count = 0;

            foreach ($result_details as $student_result) :

                $subject_name = $student_result->subjectTeacher->subject->code;
                $total = $student_result->total;


                if ($sub_term == 'half') {
                    //we want to calculate the total ca for half term and convert to its equivalent 100%
                    $total_ca = $student_result->ca1 + $student_result->ca2;
                    $total = scoreInPercentage($total_ca, 30);
                }


                list($grade, $color, $grade_point) = $this->resultGrade($total, $grades);

                if ($sub_term == 'half') {
                    //return to the normal value
                    $total_ca = $student_result->ca1 + $student_result->ca2;
                    $total = $total_ca;
                }
                if ($total != null) {
                    $count++;
                    $total_score = $total_score + $total;
                }
                $subjects[$student_result->subject_teacher_id] = $subject_name;

                $student_result->total = $total;
                $student_result->color = $color;

                $result_details_array[] = ['name' => $subject_name, 'grade' => $total];

            endforeach;

            $student->result_details = $result_details;
            //average of all subjects offered by this particular student
            if ($count == 0) {
                $average = 0;
            } else {
                $average = $total_score / $count;
            }
            if ($sub_term == 'half') {
                //we want to calculate the average for half term and convert to its equivalent 100%
                $average_half_term = $average;

                $average = scoreInPercentage($average_half_term, 30);
            }


            list($grade, $color, $grade_point) = $this->resultGrade($average, $grades);

            if ($sub_term == 'half') {
                //return to the normal value
                $average = $average_half_term;
            }

            $average = sprintf("%01.1f", $average);
            $student->average = ($average) ? $average : 0.00;
            $student->gender = $student->user->gender;
            $student->average_color = $color;
            $student->average_grade = $grade;
            $student->grade_point = $grade_point;
        }
        $student->subjects = $subjects;
        $student->result_details_array = $result_details_array;



        return $student;
    }

    public function analyzeTeacherPerformance($teacher_id, $sess_id, $grades)
    {

        $subject_teacher_ids = Result::distinct()->where(['recorded_by' => $teacher_id, 'sess_id' => $sess_id])
            ->orderBy('subject_teacher_id')->pluck('subject_teacher_id');

        $subject_averages = [];
        $count_subjects = 0;
        $total_score = 0;
        if ($subject_teacher_ids != '[]') {
            foreach ($subject_teacher_ids as $subject_teacher_id) {
                $subject_teacher = SubjectTeacher::with('subject')->findOrFail($subject_teacher_id);
                $average = Result::where(['recorded_by' => $teacher_id, 'sess_id' => $sess_id, 'subject_teacher_id' => $subject_teacher_id])->where('exam', '!=', null)->avg('total');

                list($grade, $color, $grade_point) = $this->resultGrade($average, $grades);
                $subject_teacher->subject_average =  (float) sprintf("%01.1f", $average);
                $subject_teacher->color =  $color;
                $subject_teacher->grade_point =  $grade_point;
                $subject_averages[] = $subject_teacher;

                if ($average > 0) {
                    $count_subjects++;
                    $total_score += $average;
                }
            }
        }
        if ($count_subjects < 1) {
            $performance_average = 0;
        } else {
            $performance_average = (float) sprintf("%01.1f", $total_score / $count_subjects);
        }



        return array($subject_averages, $performance_average);
    }
}
