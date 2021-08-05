<?php

namespace App\Http\Controllers;

use App\Models\CurriculumLevel;
use App\Models\CurriculumSetup;
use Illuminate\Http\Request;

class CurriculumSetupController extends Controller
{

    public function fetchCurriculumSetup()
    {
        $curricula = CurriculumSetup::get();
        return response()->json(compact('curricula'), 200);
    }
    public function allCurriculumLevels()
    {
        $curriculum_levels = CurriculumLevel::with('curriculumSetup')->orderBy('id', 'DESC')->get();
        return response()->json(compact('curriculum_levels'), 200);
    }
    public function storeCurriculumLevel(Request $request)
    {
        $curriculum_setup_id = $request->curriculum_id;
        $level_name = $request->level_name;

        $curriculum_level = CurriculumLevel::where(['curriculum_setup_id' => $curriculum_setup_id, 'level_name' => $level_name])->first();

        if ($curriculum_level) {
            // something exist return an error
            return response()->json('exist');
        }
        $curriculum_level = new CurriculumLevel();
        $curriculum_level->curriculum_setup_id = $curriculum_setup_id;
        $curriculum_level->level_name = $level_name;
        $curriculum_level->abbrev = $request->abbrev;
        $curriculum_level->level_group = $request->level_group;
        $curriculum_level->save();

        return $this->showCurriculumLevel($curriculum_level);
    }
    public function updateCurriculumLevel(Request $request, CurriculumLevel $curriculum_level)
    {
        $curriculum_level->curriculum_setup_id = $request->curriculum_id;
        $curriculum_level->level_name = $request->level_name;
        $curriculum_level->abbrev = $request->abbrev;
        $curriculum_level->level_group = $request->level_group;
        $curriculum_level->save();

        return $this->showCurriculumLevel($curriculum_level);
    }


    public function showCurriculumLevel(CurriculumLevel $curriculum_level)
    {
        $curriculum_level = $curriculum_level->with('curriculumSetup')->where('id', $curriculum_level->id)->first();
        return $curriculum_level;
    }
}
