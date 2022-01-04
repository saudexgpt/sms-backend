<?php

namespace App\Http\Controllers\Result;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ResultDisplaySetting;

class ResultDisplaySettingsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $curriculum_level_group_id = $request->curriculum_level_group_id;
        $result_setting = $this->getResultSettings($curriculum_level_group_id); // ResultDisplaySetting::where('school_id', $school_id)->get();
        return response()->json(compact('result_setting'), 200);
    }

    public function update(Request $request, ResultDisplaySetting $result_setting)
    {
        $school_id = $this->getSchool()->id;
        $curriculum_level_group_id = $request->curriculum_level_group_id;
        // $result_setting = ResultDisplaySetting::where(['school_id' => $school_id, 'curriculum_level_group_id' => $curriculum_level_group_id])->first();
        $display_exam_score_only_for_full_term = $request->display_exam_score_only_for_full_term;

        $exam = $request->exam;
        // if ($display_exam_score_only_for_full_term === 'yes') {
        //     $exam = 100;
        // }
        $result_setting->no_of_ca = $request->no_of_ca;
        $result_setting->ca1 = $request->ca1;
        $result_setting->ca2 = $request->ca2;
        $result_setting->ca3 = $request->ca3;
        $result_setting->ca4 = $request->ca4;
        $result_setting->ca5 = $request->ca5;
        $result_setting->exam = $exam;
        $result_setting->no_of_ca_for_midterm = $request->no_of_ca_for_midterm;
        $result_setting->display_exam_score_only_for_full_term = $display_exam_score_only_for_full_term;
        $result_setting->display_student_position = $request->display_student_position;
        $result_setting->display_highest_score = $request->display_highest_score;
        $result_setting->display_lowest_score = $request->display_lowest_score;
        $result_setting->display_average_score = $request->display_average_score;
        $result_setting->display_grade = $request->display_grade;
        $result_setting->display_student_behovior_and_skill_rating = $request->display_student_behovior_and_skill_rating;


        $result_setting->save();

        return response()->json(compact('result_setting'), 200);
    }
}
