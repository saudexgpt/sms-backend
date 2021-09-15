<?php

namespace Modules\Core\Http\Controllers;

use App\Timeline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class TimelinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = $this->getUser();
        $school = $this->getSchool();

        $user_id = $user->id;
        //Personal time line
        $personal_timelines = Timeline::where(['school_id'=>$school->id, 'user_id'=>$user->id])->orderBy('id', 'DESC')->paginate(5);

        //posts and time line that is permitted to be viewed

        $permitted_timelines = Timeline::where(['school_id'=>$school->id])
                                ->where(function ($query) use ($user_id) {
                                    
                                    return $query->where('visible_to', 'like', $user_id)
                                        ->orWhere('visible_to', 'like', $user_id.'~%')
                                        ->orWhere('visible_to', 'like', '%~'.$user_id.'~%')
                                        ->orWhere('visible_to', 'like', '%~'.$user_id);
                                })->orderBy('id', 'DESC')->paginate(5);

        return collect($personal_timelines)->merge($permitted_timelines);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function show(Timeline $timeline)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function edit(Timeline $timeline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Timeline $timeline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timeline $timeline)
    {
        //
    }
}
