<?php

namespace App\Http\Controllers\Result;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Grade;
use App\CurriculumLevelGroup;
use Laracasts\Flash\Flash;

class GradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $grades = $this->getGrades(); //Grade::where('school_id', $this->getSchool()->id)->get();

        return $this->render('result::grades.index', compact('grades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $school = $this->getSchool();
        $curriculum = $school->curriculum;
        $curriculum_levels = CurriculumLevelGroup::where('curriculum', $curriculum)->get();
        return $this->render('result::grades.create', compact('curriculum_levels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $count = count($request->grade);
        $curriculum_level_group_id = $request->curriculum_level_group_id;
        $school_id = $this->getSchool()->id;
        for ($i = 0; $i <= $count; $i++) :

            if (isset($request->grade[$i])) {
                $grade = new Grade();
                $grade->grade = $request->grade[$i];
                $grade->school_id = $school_id;
                $grade->interpretation = $request->interpretation[$i];
                //$grade->grade_range = $request->grade_range[$i];
                $grade->upper_limit = $request->upper_limit[$i];
                $grade->lower_limit = $request->lower_limit[$i];
                $grade->grade_point = $request->grade_point[$i];
                $grade->curriculum_level_group_id = $curriculum_level_group_id;
                $grade->color_code = randomColorCode(); //this is from helpers in form of '#FFFFFF'
                $grade->save();
            }
        endfor;

        return redirect()->route('grades.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function show(Grade $grade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $grade = Grade::find($id);
        $school = $this->getSchool();
        $curriculum = $school->curriculum;
        $curriculum_levels = CurriculumLevelGroup::where('curriculum', $curriculum)->get();
        return $this->render('result::grades.edit', compact('grade', 'curriculum_levels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Grade  $grade
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //
        $curriculum_level_group_id = $request->curriculum_level_group_id;
        $grade = Grade::find($id);

        $grade->curriculum_level_group_id = $curriculum_level_group_id;
        $grade->grade = $request->grade;
        $grade->interpretation = $request->interpretation;
        //$grade->grade_range = $request->grade_range;
        $grade->upper_limit = $request->upper_limit;
        $grade->lower_limit = $request->lower_limit;
        $grade->grade_point = $request->grade_point;
        $grade->save();


        return redirect()->route('grades.index');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Grade  $grade
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grade $grade)
    {
        //
        try {

            $grade->delete();
            Flash::success("Grade deleted successfully");
            return redirect()->back();
        } catch (ModelNotFoundException $ex) {
            Flash::error('Error: ' . $ex->getMessage());
            return redirect()->route('grades.index');
        }
    }
}
